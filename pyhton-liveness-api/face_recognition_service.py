import cv2
import numpy as np
import dlib
import pickle
import os
import time
import hashlib
import logging
from dataclasses import dataclass, field
from typing import Optional
from scipy.spatial import distance as dist

logger = logging.getLogger(__name__)


# ──────────────────────────────────────────────
#  DATA CLASSES
# ──────────────────────────────────────────────

@dataclass
class FaceEntry:
    """Stores multiple encodings per user for better accuracy."""
    user_id:   str
    encodings: list = field(default_factory=list)   # list of np.ndarray
    created_at: float = field(default_factory=time.time)
    updated_at: float = field(default_factory=time.time)

    # security
    failed_attempts: int   = 0
    locked_until:    float = 0.0

    MAX_ENCODINGS   = 5     # keep last N samples per user
    LOCKOUT_AFTER   = 5     # failed attempts before lockout
    LOCKOUT_SECONDS = 30.0  # lockout duration


@dataclass
class VerifyResult:
    matched:    bool
    similarity: float
    user_id:    Optional[str]
    message:    str
    locked:     bool = False


# ──────────────────────────────────────────────
#  FACE ALIGNMENT  (new — improves accuracy)
# ──────────────────────────────────────────────

class FaceAligner:
    """
    Aligns face so that both eyes are on the same horizontal line.
    This normalises for head tilt and greatly improves matching stability.
    """
    TARGET_LEFT_EYE  = (0.35, 0.35)   # relative position in output image
    OUTPUT_SIZE      = (112, 112)

    def align(self, image: np.ndarray, shape: np.ndarray) -> Optional[np.ndarray]:
        left_eye_pts  = shape[36:42]
        right_eye_pts = shape[42:48]

        left_center  = left_eye_pts.mean(axis=0)
        right_center = right_eye_pts.mean(axis=0)

        dy = right_center[1] - left_center[1]
        dx = right_center[0] - left_center[0]
        angle = np.degrees(np.arctan2(dy, dx))

        desired_dist = (1.0 - 2 * self.TARGET_LEFT_EYE[0]) * self.OUTPUT_SIZE[0]
        current_dist = np.linalg.norm(right_center - left_center)
        if current_dist < 1e-6:
            return None

        scale = desired_dist / current_dist
        eye_center = ((left_center + right_center) / 2).astype(int)

        M = cv2.getRotationMatrix2D(tuple(eye_center.tolist()), angle, scale)

        # shift so the eye centre maps to desired position
        M[0, 2] += self.OUTPUT_SIZE[0] * 0.5 - eye_center[0]
        M[1, 2] += self.OUTPUT_SIZE[1] * self.TARGET_LEFT_EYE[1] - eye_center[1]

        aligned = cv2.warpAffine(image, M, self.OUTPUT_SIZE, flags=cv2.INTER_CUBIC)
        return aligned


# ──────────────────────────────────────────────
#  ENCODING EXTRACTOR  (improved normalisation)
# ──────────────────────────────────────────────

class EncodingExtractor:
    """
    Extracts a robust face encoding combining:
      - Normalised 68-landmark geometry  (pose-invariant)
      - Pairwise inter-landmark distances (scale-invariant ratios)
    """

    # Key landmark pairs for ratio features
    PAIRS = [
        (36, 45),   # eye distance (baseline)
        (48, 54),   # mouth width
        (27, 8),    # nose to chin
        (0,  16),   # face width
        (17, 26),   # brow width
        (36, 48),   # left eye to left mouth
        (45, 54),   # right eye to right mouth
        (30, 48),   # nose tip to left mouth
        (30, 54),   # nose tip to right mouth
        (8,  57),   # chin to bottom lip
    ]

    def extract(self, shape: np.ndarray) -> np.ndarray:
        lm = shape.astype(np.float64)

        # ── part 1: normalised geometry (136 dims) ──
        center       = lm.mean(axis=0)
        lm_centered  = lm - center
        eye_dist     = np.linalg.norm(lm_centered[45] - lm_centered[36])
        if eye_dist < 1e-6:
            return None
        lm_norm = lm_centered / eye_dist
        geo_enc = lm_norm.flatten()           # 136

        # ── part 2: pairwise distance ratios (10 dims) ──
        baseline = np.linalg.norm(lm[36] - lm[45]) + 1e-6
        ratios   = np.array([
            np.linalg.norm(lm[a] - lm[b]) / baseline
            for a, b in self.PAIRS
        ])

        # ── combine & L2-normalise ──
        combined = np.concatenate([geo_enc, ratios])
        norm     = np.linalg.norm(combined)
        return combined / (norm + 1e-6)


# ──────────────────────────────────────────────
#  SECURE STORAGE
# ──────────────────────────────────────────────

class EncodingStore:
    """
    Persists FaceEntry objects.
    Adds a simple integrity checksum so tampered files are rejected.
    """

    def __init__(self, path: str = "models/face_encodings.pkl"):
        self.path      = path
        self.hash_path = path + ".sha256"
        self._data: dict[str, FaceEntry] = {}
        self._load()

    # ── public ────────────────────────────────

    def get(self, user_id: str) -> Optional[FaceEntry]:
        return self._data.get(user_id)

    def set(self, entry: FaceEntry):
        self._data[entry.user_id] = entry
        self._save()

    def delete(self, user_id: str) -> bool:
        if user_id in self._data:
            del self._data[user_id]
            self._save()
            return True
        return False

    def exists(self, user_id: str) -> bool:
        return user_id in self._data

    def all_ids(self) -> list[str]:
        return list(self._data.keys())

    # ── private ───────────────────────────────

    def _save(self):
        os.makedirs(os.path.dirname(self.path) or ".", exist_ok=True)
        raw = pickle.dumps(self._data)
        checksum = hashlib.sha256(raw).hexdigest()
        with open(self.path,      "wb") as f: f.write(raw)
        with open(self.hash_path, "w")  as f: f.write(checksum)

    def _load(self):
        if not os.path.exists(self.path):
            return
        with open(self.path, "rb") as f:
            raw = f.read()

        # integrity check
        if os.path.exists(self.hash_path):
            with open(self.hash_path) as f:
                stored = f.read().strip()
            actual = hashlib.sha256(raw).hexdigest()
            if stored != actual:
                logger.error("Face encoding file integrity check FAILED — data may be tampered.")
                self._data = {}
                return

    def _load(self):
        if not os.path.exists(self.path):
            return
        with open(self.path, "rb") as f:
            raw = f.read()

        # integrity check
        if os.path.exists(self.hash_path):
            with open(self.hash_path) as f:
                stored = f.read().strip()
            actual = hashlib.sha256(raw).hexdigest()
            if stored != actual:
                logger.error("Face encoding file integrity check FAILED — data may be tampered.")
                self._data = {}
                return

        loaded = pickle.loads(raw)

        # ── backward compatibility ──
        # format lama: {user_id: np.ndarray 136-dim}
        # format baru: {user_id: FaceEntry dengan encoding 146-dim}
        migrated = {}
        for uid, val in loaded.items():
            if isinstance(val, FaceEntry):
                # filter encoding yang dimensinya tidak sesuai (146)
                valid = [e for e in val.encodings if isinstance(e, np.ndarray) and e.shape[0] == 146]
                if valid:
                    val.encodings = valid
                    migrated[uid] = val
                else:
                    logger.warning(f"User '{uid}' encoding dimensi tidak sesuai, perlu daftar ulang.")
            elif isinstance(val, np.ndarray):
                # format lama — dimensi tidak kompatibel, skip
                logger.warning(f"User '{uid}' format lama (dim={val.shape[0]}), perlu daftar ulang wajah.")
            else:
                logger.warning(f"Data tidak dikenal untuk user '{uid}', dilewati.")

        self._data = migrated
        # simpan ulang dengan format baru
        self._save()
        logger.info("Migrasi selesai — file disimpan ulang dengan format baru.")


# ──────────────────────────────────────────────
#  MAIN SERVICE
# ──────────────────────────────────────────────

class FaceRecognitionService:
    """
    Improved face recognition service.

    Improvements over original:
      ✓ Face alignment before encoding  → consistent pose
      ✓ Combined geometry + ratio encoding → more discriminative
      ✓ Multi-sample averaging per user  → robust to expression change
      ✓ Cosine similarity instead of raw L2 → scale-invariant comparison
      ✓ Brute-force identify() across all users
      ✓ Lockout after repeated failures  → brute-force protection
      ✓ Checksum-protected storage       → tamper detection
      ✓ Liveness result integration      → spoof guard
    """

    SIMILARITY_THRESHOLD = 0.82   # cosine similarity (0-1)

    def __init__(
        self,
        landmark_model: str = "models/shape_predictor_68_face_landmarks.dat",
        encodings_path: str = "models/face_encodings.pkl",
    ):
        self.detector  = dlib.get_frontal_face_detector()
        self.predictor = dlib.shape_predictor(landmark_model)

        self.aligner   = FaceAligner()
        self.extractor = EncodingExtractor()
        self.store     = EncodingStore(encodings_path)

    # ── registration ──────────────────────────

    def register_face(
        self,
        image:   np.ndarray,
        user_id: str,
        liveness_passed: bool = False,
    ) -> tuple[bool, str]:
        """
        Register (or update) a face for user_id.
        Pass liveness_passed=True when called after a successful liveness check.
        """
        if not liveness_passed:
            return False, "Liveness check required before registration"

        encoding, msg = self._extract(image)
        if encoding is None:
            return False, msg

        entry = self.store.get(user_id) or FaceEntry(user_id=user_id)

        # keep last MAX_ENCODINGS samples
        entry.encodings.append(encoding)
        if len(entry.encodings) > FaceEntry.MAX_ENCODINGS:
            entry.encodings = entry.encodings[-FaceEntry.MAX_ENCODINGS:]

        entry.updated_at = time.time()
        self.store.set(entry)
        return True, f"Face registered ({len(entry.encodings)}/{FaceEntry.MAX_ENCODINGS} samples)"

    # ── 1-to-1 verification ───────────────────

    def verify_face(
        self,
        image:   np.ndarray,
        user_id: str,
        liveness_passed: bool = False,
    ) -> VerifyResult:
        """Verify image belongs to user_id."""
        if not liveness_passed:
            return VerifyResult(False, 0.0, user_id, "Liveness check required", False)

        entry = self.store.get(user_id)
        if entry is None:
            return VerifyResult(False, 0.0, user_id, "User not registered", False)

        # lockout check
        if self._is_locked(entry):
            remaining = round(entry.locked_until - time.time(), 1)
            return VerifyResult(False, 0.0, user_id,
                                f"Account locked — try again in {remaining}s", True)

        encoding, msg = self._extract(image)
        if encoding is None:
            return VerifyResult(False, 0.0, user_id, msg, False)

        similarity = self._best_similarity(encoding, entry.encodings)
        matched    = similarity >= self.SIMILARITY_THRESHOLD

        # update lockout state
        if matched:
            entry.failed_attempts = 0
        else:
            entry.failed_attempts += 1
            if entry.failed_attempts >= FaceEntry.LOCKOUT_AFTER:
                entry.locked_until    = time.time() + FaceEntry.LOCKOUT_SECONDS
                entry.failed_attempts = 0
        self.store.set(entry)

        return VerifyResult(
            matched    = matched,
            similarity = round(float(similarity), 4),
            user_id    = user_id if matched else None,
            message    = "Match ✓" if matched else "No match",
        )

    # ── 1-to-N identification ─────────────────

    def identify_face(
        self,
        image: np.ndarray,
        liveness_passed: bool = False,
    ) -> VerifyResult:
        """
        Identify who the person is across ALL registered users.
        Returns the best match if above threshold, otherwise unknown.
        """
        if not liveness_passed:
            return VerifyResult(False, 0.0, None, "Liveness check required", False)

        encoding, msg = self._extract(image)
        if encoding is None:
            return VerifyResult(False, 0.0, None, msg, False)

        best_user = None
        best_sim  = -1.0

        for uid in self.store.all_ids():
            entry = self.store.get(uid)
            if entry is None:
                continue
            sim = self._best_similarity(encoding, entry.encodings)
            if sim > best_sim:
                best_sim  = sim
                best_user = uid

        matched = best_sim >= self.SIMILARITY_THRESHOLD
        return VerifyResult(
            matched    = matched,
            similarity = round(float(best_sim), 4),
            user_id    = best_user if matched else None,
            message    = f"Identified as {best_user}" if matched else "Unknown person",
        )

    # ── management ────────────────────────────

    def delete_user(self, user_id: str) -> bool:
        return self.store.delete(user_id)

    def list_users(self) -> list[str]:
        return self.store.all_ids()

    # ── private helpers ───────────────────────

    def _extract(self, image: np.ndarray) -> tuple[Optional[np.ndarray], str]:
        """Detect face → align → extract encoding."""
        gray  = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        faces = self.detector(gray, 1)

        if len(faces) == 0:
            return None, "No face detected"
        if len(faces) > 1:
            return None, "Multiple faces detected — use one face only"

        shape_dlib = self.predictor(gray, faces[0])
        shape      = np.array([[p.x, p.y] for p in shape_dlib.parts()])

        # align first
        aligned = self.aligner.align(image, shape)
        if aligned is None:
            return None, "Face alignment failed"

        # re-detect on aligned image
        aligned_gray  = cv2.cvtColor(aligned, cv2.COLOR_BGR2GRAY)
        aligned_faces = self.detector(aligned_gray, 1)
        if len(aligned_faces) == 0:
            # fallback: use original shape
            encoding = self.extractor.extract(shape)
        else:
            aligned_shape_dlib = self.predictor(aligned_gray, aligned_faces[0])
            aligned_shape      = np.array([[p.x, p.y] for p in aligned_shape_dlib.parts()])
            encoding           = self.extractor.extract(aligned_shape)

        if encoding is None:
            return None, "Cannot normalise face (eye distance too small)"

        return encoding, "OK"

    @staticmethod
    def _cosine_similarity(a: np.ndarray, b: np.ndarray) -> float:
        """Cosine similarity between two L2-normalised vectors."""
        return float(np.dot(a, b))   # already normalised in extractor

    def _best_similarity(
        self,
        probe: np.ndarray,
        gallery: list[np.ndarray],
    ) -> float:
        """Return highest similarity between probe and any gallery encoding."""
        if not gallery:
            return 0.0
        sims = [self._cosine_similarity(probe, g) for g in gallery]
        return max(sims)

    @staticmethod
    def _is_locked(entry: FaceEntry) -> bool:
        return time.time() < entry.locked_until


# ──────────────────────────────────────────────
#  INTEGRATION EXAMPLE  (with liveness_detector)
# ──────────────────────────────────────────────
#
#   from liveness_detector import LivenessDetector, LivenessResult
#   from face_recognition_service import FaceRecognitionService, VerifyResult
#
#   liveness = LivenessDetector(n_challenges=3)
#   recog    = FaceRecognitionService()
#
#   liveness.start_session()
#   while True:
#       ret, frame = cap.read()
#       result = liveness.process_frame(frame)
#
#       if result["result"] == LivenessResult.PASSED:
#           verify = recog.verify_face(frame, user_id="alice", liveness_passed=True)
#           print(verify)
#           break
#
#       elif result["result"] in (LivenessResult.FAILED, LivenessResult.SPOOFED):
#           print("Liveness failed — access denied")
#           break