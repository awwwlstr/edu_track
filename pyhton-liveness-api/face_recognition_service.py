"""
face_recognition_service.py  —  v3 (deep embedding)
=====================================================

Revisi dari v2 (HOG+LBP+Landmark):
  ✗ DIHAPUS: EncodingExtractor (landmark geometry 154 dim)
  ✗ DIHAPUS: HOGExtractor (~1764 dim)
  ✗ DIHAPUS: LBPExtractor (10 dim)
  ✗ DIHAPUS: FaceAligner (tidak diperlukan, face_recognition handle sendiri)
  ✓ DIGANTI: face_recognition ResNet 128-dim embedding
              → dilatih dengan 3 juta+ wajah untuk membedakan IDENTITAS

  Semua logika bisnis DIPERTAHANKAN dari v2:
  ✓ Mean similarity + penalti ketidakkonsistenan
  ✓ Gap check antar user di identify_face()
  ✓ Threshold lebih ketat jika hanya 1 user
  ✓ Lockout setelah gagal berulang
  ✓ Checksum SHA-256 pada storage
  ✓ Debug logging
  ✓ Multi-sample per user

Install:
  pip install face_recognition
  # otomatis install dlib + ResNet model weights (~100MB)
"""

import cv2
import numpy as np
import face_recognition          # pip install face_recognition
import pickle
import os
import time
import hashlib
import logging
from dataclasses import dataclass, field
from typing import Optional

logger = logging.getLogger(__name__)


# ──────────────────────────────────────────────
#  DATA CLASSES  (tidak berubah dari v2)
# ──────────────────────────────────────────────

@dataclass
class FaceEntry:
    user_id:    str
    encodings:  list = field(default_factory=list)   # list of np.ndarray 128-dim
    created_at: float = field(default_factory=time.time)
    updated_at: float = field(default_factory=time.time)

    failed_attempts: int   = 0
    locked_until:    float = 0.0

    MAX_ENCODINGS   = 5
    LOCKOUT_AFTER   = 5
    LOCKOUT_SECONDS = 30.0


@dataclass
class VerifyResult:
    matched:    bool
    similarity: float      # cosine similarity 0–1 (makin besar makin mirip)
    user_id:    Optional[str]
    message:    str
    locked:     bool = False


# ──────────────────────────────────────────────
#  SECURE STORAGE  (tidak berubah dari v2)
# ──────────────────────────────────────────────

class EncodingStore:
    """Simpan FaceEntry ke disk dengan checksum SHA-256."""

    def __init__(self, path: str = "models/face_encodings.pkl"):
        self.path      = path
        self.hash_path = path + ".sha256"
        self._data: dict[str, FaceEntry] = {}
        self._load()

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

    def _save(self):
        os.makedirs(os.path.dirname(self.path) or ".", exist_ok=True)
        raw      = pickle.dumps(self._data)
        checksum = hashlib.sha256(raw).hexdigest()
        with open(self.path,      "wb") as f: f.write(raw)
        with open(self.hash_path, "w")  as f: f.write(checksum)

    def _load(self):
        if not os.path.exists(self.path):
            return
        with open(self.path, "rb") as f:
            raw = f.read()

        if os.path.exists(self.hash_path):
            with open(self.hash_path) as f:
                stored = f.read().strip()
            if hashlib.sha256(raw).hexdigest() != stored:
                logger.error("Integrity check GAGAL — file encoding mungkin dimanipulasi.")
                self._data = {}
                return

        loaded   = pickle.loads(raw)
        migrated = {}

        for uid, val in loaded.items():
            if isinstance(val, FaceEntry):
                # Hanya terima encoding 128-dim (ResNet face_recognition)
                # Encoding dari v1/v2 (146/154/1920+ dim) otomatis dibuang
                valid = [
                    e for e in val.encodings
                    if isinstance(e, np.ndarray) and e.shape[0] == 128
                ]
                if valid:
                    val.encodings = valid
                    migrated[uid] = val
                else:
                    old_dim = val.encodings[0].shape[0] if val.encodings else "?"
                    logger.warning(
                        f"User '{uid}': encoding lama (dim={old_dim}) tidak kompatibel. "
                        "Daftar ulang wajah diperlukan."
                    )
            else:
                logger.warning(f"Format tidak dikenal untuk user '{uid}', dilewati.")

        self._data = migrated
        self._save()
        logger.info("Data dimuat dan disimpan ulang dengan format v3 (128-dim ResNet).")


# ──────────────────────────────────────────────
#  MAIN SERVICE
# ──────────────────────────────────────────────

class FaceRecognitionService:
    """
    Face Recognition Service v3 — berbasis deep embedding ResNet 128-dim.

    MENGAPA v1/v2 GAGAL membedakan wajah orang lain:
    ─────────────────────────────────────────────────
    v1/v2 menggunakan koordinat landmark (titik mata, hidung, mulut) dan
    fitur tekstur (HOG, LBP). Ini hanya mengukur BENTUK dan PROPORSI wajah,
    bukan IDENTITAS. Dua orang dengan proporsi wajah mirip (jarak mata,
    lebar hidung, dll. serupa) akan menghasilkan encoding yang dekat,
    meskipun mereka orang yang berbeda.

    MENGAPA v3 BENAR:
    ─────────────────
    face_recognition menggunakan ResNet yang dilatih dengan 3 juta+ wajah
    menggunakan metric learning (triplet loss). Model ini belajar
    "siapa orang ini" — bukan "seperti apa bentuknya". Hasilnya adalah
    128 angka yang merepresentasikan IDENTITAS unik seseorang, sehingga:
    - Foto yang sama orang dalam pose berbeda → distance kecil (~0.2–0.4)
    - Foto dua orang berbeda → distance besar (>0.5, bahkan kembar >0.45)

    Threshold:
    ─────────
    Menggunakan L2 distance (bukan cosine) karena itu standar untuk
    face_recognition library. Distance < THRESHOLD = cocok.
    Makin KECIL distance = makin mirip (kebalikan dari cosine similarity).

    Logika bisnis dipertahankan dari v2:
    ✓ Mean distance + penalti ketidakkonsistenan
    ✓ Gap check antar user di identify_face()
    ✓ Threshold lebih ketat jika hanya 1 user terdaftar
    ✓ Lockout setelah 5x gagal berulang
    ✓ Checksum SHA-256 pada storage
    ✓ Debug logging untuk tuning threshold
    ✓ Multi-sample (5 sampel) per user
    """

    # ── THRESHOLD UTAMA ──────────────────────────────────────────────────
    # L2 distance — LEBIH KECIL = LEBIH MIRIP (kebalikan cosine!)
    # < 0.40 : sangat ketat (keamanan tinggi, mungkin sering FRR)
    # < 0.45 : ketat         ← DEFAULT, rekomendasikan mulai dari sini
    # < 0.50 : longgar       (toleran terhadap pencahayaan/angle buruk)
    # < 0.60 : default library (terlalu longgar untuk produksi)
    DISTANCE_THRESHOLD = 0.45

    # Selisih minimum distance antara user terbaik & kedua terbaik
    # Mencegah false acceptance jika dua user punya wajah mirip
    MIN_GAP = 0.05

    def __init__(
        self,
        encodings_path: str = "models/face_encodings.pkl",
        model: str = "large",
    ):
        """
        Args:
            encodings_path : path penyimpanan encoding
            model          : "large" (CNN ResNet, akurat) | "small" (HOG dlib, cepat)
        """
        self.model = model
        self.store = EncodingStore(encodings_path)

    # ── REGISTRASI ────────────────────────────────────────────────────────

    def register_face(
        self,
        image:           np.ndarray,
        user_id:         str,
        liveness_passed: bool = False,
    ) -> tuple[bool, str]:
        if not liveness_passed:
            return False, "Liveness check diperlukan sebelum registrasi"

        encoding, msg = self._extract(image)
        if encoding is None:
            return False, msg

        entry = self.store.get(user_id) or FaceEntry(user_id=user_id)
        entry.encodings.append(encoding)
        if len(entry.encodings) > FaceEntry.MAX_ENCODINGS:
            entry.encodings = entry.encodings[-FaceEntry.MAX_ENCODINGS:]

        entry.updated_at = time.time()
        self.store.set(entry)
        logger.info(f"[REGISTER] user={user_id}, sampel={len(entry.encodings)}, dim=128")
        return True, f"Wajah terdaftar ({len(entry.encodings)}/{FaceEntry.MAX_ENCODINGS} sampel)"

    # ── VERIFIKASI 1-KE-1 ─────────────────────────────────────────────────

    def verify_face(
        self,
        image:           np.ndarray,
        user_id:         str,
        liveness_passed: bool = False,
    ) -> VerifyResult:
        """Verifikasi apakah gambar cocok dengan user_id."""
        if not liveness_passed:
            return VerifyResult(False, 0.0, user_id, "Liveness check diperlukan", False)

        entry = self.store.get(user_id)
        if entry is None:
            return VerifyResult(False, 0.0, user_id, "User belum terdaftar", False)

        if self._is_locked(entry):
            remaining = round(entry.locked_until - time.time(), 1)
            return VerifyResult(False, 0.0, user_id,
                                f"Akun terkunci — coba lagi dalam {remaining}s", True)

        encoding, msg = self._extract(image)
        if encoding is None:
            return VerifyResult(False, 0.0, user_id, msg, False)

        best_dist, detail = self._compute_distance(encoding, entry.encodings)
        matched = best_dist <= self.DISTANCE_THRESHOLD

        # similarity untuk VerifyResult (0–1, makin besar makin mirip)
        # konversi dari distance: similarity ≈ 1 - (distance / max_possible)
        similarity = max(0.0, 1.0 - best_dist)

        logger.debug(
            f"[VERIFY] user={user_id}, dist={best_dist:.4f}, "
            f"detail={detail}, matched={matched}"
        )

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
            message    = "Cocok ✓" if matched else f"Tidak cocok (dist={best_dist:.4f})",
        )

    # ── IDENTIFIKASI 1-KE-N ───────────────────────────────────────────────

    def identify_face(
        self,
        image:           np.ndarray,
        liveness_passed: bool = False,
    ) -> VerifyResult:
        """
        Identifikasi siapa orang ini dari SEMUA user terdaftar.
        Dipertahankan dari v2: gap check & threshold ketat jika 1 user.
        """
        if not liveness_passed:
            return VerifyResult(False, 0.0, None, "Liveness check diperlukan", False)

        encoding, msg = self._extract(image)
        if encoding is None:
            return VerifyResult(False, 0.0, None, msg, False)

        results = []
        for uid in self.store.all_ids():
            entry = self.store.get(uid)
            if entry is None or not entry.encodings:
                continue
            dist, detail = self._compute_distance(encoding, entry.encodings)
            results.append((dist, uid))
            logger.debug(f"[IDENTIFY] user={uid}, dist={dist:.4f}, detail={detail}")

        if not results:
            return VerifyResult(False, 0.0, None, "Tidak ada user terdaftar", False)

        # Urutkan dari distance TERKECIL (paling mirip)
        results.sort(key=lambda x: x[0])

        best_dist, best_user = results[0]
        second_dist = results[1][0] if len(results) > 1 else float("inf")
        gap = second_dist - best_dist   # gap positif = best_user menang jelas

        logger.debug(
            f"[IDENTIFY] best={best_user}({best_dist:.4f}), "
            f"second={results[1][1] if len(results)>1 else '-'}({second_dist:.4f}), "
            f"gap={gap:.4f}"
        )

        if len(results) == 1:
            # Hanya 1 user terdaftar — pakai threshold lebih ketat
            threshold = self.DISTANCE_THRESHOLD * 0.90
            matched   = best_dist <= threshold
            logger.debug(f"[IDENTIFY] 1 user — threshold diperketat ke {threshold:.3f}")
        else:
            matched = (
                best_dist <= self.DISTANCE_THRESHOLD and
                gap       >= self.MIN_GAP
            )

        similarity = max(0.0, 1.0 - best_dist)
        return VerifyResult(
            matched    = matched,
            similarity = round(float(similarity), 4),
            user_id    = best_user if matched else None,
            message    = (
                f"Dikenali sebagai {best_user} (dist={best_dist:.4f})"
                if matched else
                f"Tidak dikenali (dist={best_dist:.4f}, gap={gap:.4f})"
            ),
        )

    # ── MANAJEMEN ─────────────────────────────────────────────────────────

    def delete_user(self, user_id: str) -> bool:
        return self.store.delete(user_id)

    def list_users(self) -> list[str]:
        return self.store.all_ids()

    def set_threshold(self, value: float):
        """
        Sesuaikan threshold jika diperlukan.
        0.40 = ketat, 0.45 = default, 0.50 = toleran
        """
        if not 0.1 <= value <= 0.8:
            raise ValueError("Threshold harus antara 0.1 dan 0.8")
        self.DISTANCE_THRESHOLD = value
        logger.info(f"Threshold diubah ke {value}")

    # ── PRIVATE HELPERS ───────────────────────────────────────────────────

    def _extract(self, image: np.ndarray) -> tuple[Optional[np.ndarray], str]:
        """
        Deteksi wajah & ekstrak 128-dim deep embedding.
        Input: BGR image (format OpenCV)
        """
        # face_recognition butuh RGB
        rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)

        locations = face_recognition.face_locations(rgb, model=self.model)

        if len(locations) == 0:
            return None, "Wajah tidak terdeteksi"
        if len(locations) > 1:
            return None, "Lebih dari satu wajah — gunakan satu wajah saja"

        encodings = face_recognition.face_encodings(rgb, known_face_locations=locations)
        if not encodings:
            return None, "Gagal mengekstrak encoding wajah"

        return encodings[0], "OK"   # np.ndarray shape (128,)

    def _compute_distance(
        self,
        probe:   np.ndarray,
        gallery: list[np.ndarray],
    ) -> tuple[float, dict]:
        """
        Hitung L2 distance probe vs semua gallery encoding.
        Gunakan MEAN distance + penalti ketidakkonsistenan (dari v2).

        Catatan: distance kecil = mirip (kebalikan cosine similarity!)
        """
        if not gallery:
            return float("inf"), {}

        distances = [float(np.linalg.norm(probe - g)) for g in gallery]

        mean_dist = float(np.mean(distances))
        min_dist  = float(np.min(distances))
        max_dist  = float(np.max(distances))
        spread    = max_dist - mean_dist

        # Penalti jika hasil tidak konsisten
        # (hanya 1 sampel yang mirip, sisanya jauh)
        if spread > 0.08:
            penalised = mean_dist * 1.10   # naikkan distance = perketat
            logger.debug(
                f"[DIST] Penalti ketidakkonsistenan: mean={mean_dist:.4f}, "
                f"min={min_dist:.4f}, spread={spread:.4f} → {penalised:.4f}"
            )
            final = penalised
        else:
            final = mean_dist

        detail = {
            "mean":      round(mean_dist, 4),
            "min":       round(min_dist,  4),
            "max":       round(max_dist,  4),
            "n":         len(distances),
            "penalised": spread > 0.08,
        }
        return final, detail

    @staticmethod
    def _is_locked(entry: FaceEntry) -> bool:
        return time.time() < entry.locked_until


# ──────────────────────────────────────────────
#  PERBANDINGAN VERSI
# ──────────────────────────────────────────────
#
#  ┌──────────────────────┬──────────────────────┬──────────────────────┐
#  │ Aspek                │ v1/v2 (lama)         │ v3 (ini)             │
#  ├──────────────────────┼──────────────────────┼──────────────────────┤
#  │ Encoding             │ Landmark + HOG + LBP │ ResNet 128-dim       │
#  │ Dimensi              │ 154 ~ 1920+ dim      │ 128 dim              │
#  │ Dilatih untuk        │ Bentuk & tekstur     │ Identitas orang      │
#  │ Metric               │ Cosine similarity    │ L2 distance          │
#  │ Threshold            │ 0.90 (cosine)        │ 0.45 (L2 distance)   │
#  │ False acceptance     │ Tinggi               │ Sangat rendah        │
#  │ Butuh GPU?           │ Tidak                │ Tidak (CPU cukup)    │
#  ├──────────────────────┼──────────────────────┼──────────────────────┤
#  │ Lockout              │ ✓                    │ ✓ (dipertahankan)    │
#  │ Checksum storage     │ ✓                    │ ✓ (dipertahankan)    │
#  │ Gap check            │ ✓                    │ ✓ (dipertahankan)    │
#  │ Penalti spread       │ ✓                    │ ✓ (dipertahankan)    │
#  │ Liveness guard       │ ✓                    │ ✓ (dipertahankan)    │
#  └──────────────────────┴──────────────────────┴──────────────────────┘
#
#  TUNING UNTUK SKRIPSI (ukur FAR & FRR):
#  - DISTANCE_THRESHOLD : 0.40 ~ 0.50
#  - MIN_GAP            : 0.03 ~ 0.08
#  - Buat kurva ROC dari hasil eksperimen
#
#  INTEGRASI LIVENESS:
#
#   from liveness_detector import LivenessDetector, LivenessResult
#   from face_recognition_service import FaceRecognitionService
#
#   liveness = LivenessDetector(n_challenges=3)
#   recog    = FaceRecognitionService(model="large")
#
#   liveness.start_session()
#   while True:
#       ret, frame = cap.read()
#       result = liveness.process_frame(frame)
#       if result["result"] == LivenessResult.PASSED:
#           verify = recog.identify_face(frame, liveness_passed=True)
#           print(verify)
#           break