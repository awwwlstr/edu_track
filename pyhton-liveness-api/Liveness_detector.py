import cv2
import numpy as np
import dlib
from scipy.spatial import distance as dist
from skimage.feature import local_binary_pattern
import random
import time
from collections import deque
from enum import Enum


# ──────────────────────────────────────────────
#  ENUMS & CONSTANTS
# ──────────────────────────────────────────────

class ChallengeType(Enum):
    BLINK_TWICE   = "blink_twice"
    SMILE         = "smile"
    OPEN_MOUTH    = "open_mouth"
    TURN_LEFT     = "turn_left"
    TURN_RIGHT    = "turn_right"
    NOD_UP        = "nod_up"
    NOD_DOWN      = "nod_down"
    RAISE_EYEBROW = "raise_eyebrow"


class LivenessResult(Enum):
    PENDING  = "pending"
    PASSED   = "passed"
    FAILED   = "failed"
    SPOOFED  = "spoofed"


# ──────────────────────────────────────────────
#  BLINK TRACKER  (state-machine based)
# ──────────────────────────────────────────────

class BlinkTracker:
    """
    Tracks complete blink cycles using a state machine.
    A valid blink requires OPEN → CLOSING → CLOSED → OPENING → OPEN.
    Prevents false positives from a single low-EAR frame.
    """

    EAR_CLOSE_THRESH = 0.21   # EAR bellow this → eye closing
    EAR_OPEN_THRESH  = 0.27   # EAR above this  → eye open (hysteresis)
    MIN_CLOSED_FRAMES = 1     # at least N consecutive closed frames
    MAX_CLOSED_FRAMES = 25    # more than this → not a blink (e.g. looking away)

    def __init__(self):
        self.state         = "OPEN"
        self.closed_frames = 0
        self.blink_count   = 0
        self.ear_history   = deque(maxlen=60)

    def update(self, ear: float) -> bool:
        """Feed one EAR value. Returns True on completed blink."""
        self.ear_history.append(ear)
        blinked = False

        if self.state == "OPEN":
            if ear < self.EAR_CLOSE_THRESH:
                self.state = "CLOSED"
                self.closed_frames = 1

        elif self.state == "CLOSED":
            if ear < self.EAR_CLOSE_THRESH:
                self.closed_frames += 1
                if self.closed_frames > self.MAX_CLOSED_FRAMES:
                    # held too long — reset (not a blink)
                    self.state = "OPEN"
                    self.closed_frames = 0
            else:  # eye opened again
                if self.closed_frames >= self.MIN_CLOSED_FRAMES:
                    self.blink_count += 1
                    blinked = True
                self.state = "OPEN"
                self.closed_frames = 0

        return blinked

    def reset(self):
        self.state = "OPEN"
        self.closed_frames = 0
        self.blink_count = 0
        self.ear_history.clear()


# ──────────────────────────────────────────────
#  OPTICAL FLOW ANALYSER
# ──────────────────────────────────────────────

class OpticalFlowAnalyser:
    """
    Analyses per-frame optical flow inside the face bounding box.
    Real faces exhibit small, natural micro-movements.
    Printed photos → near-zero magnitude.
    Screen replays → erratic / unnatural pattern.
    """

    NATURAL_MAG_MIN  = 0.1   # lebih rendah untuk webcam biasa
    NATURAL_MAG_MAX  = 8.0   # lebih toleran
    MIN_ANGLE_STD    = 0.15  # lebih toleran
    HISTORY_LEN      = 30

    def __init__(self):
        self.prev_gray   = None
        self.mag_history = deque(maxlen=self.HISTORY_LEN)

    def update(self, frame: np.ndarray, face_rect) -> tuple[float, bool]:
        """
        Returns (confidence 0-1, is_natural bool).
        """
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

        if self.prev_gray is None or face_rect is None:
            self.prev_gray = gray
            return 0.5, True  # neutral on first frame

        x, y, w, h = face_rect
        # guard against tiny / out-of-bounds rects
        x, y = max(0, x), max(0, y)
        w, h = max(10, w), max(10, h)

        prev_face = self.prev_gray[y:y+h, x:x+w]
        curr_face = gray[y:y+h, x:x+w]

        if prev_face.shape != curr_face.shape or prev_face.size == 0:
            self.prev_gray = gray
            return 0.5, True

        flow = cv2.calcOpticalFlowFarneback(
            prev_face, curr_face, None,
            pyr_scale=0.5, levels=3, winsize=15,
            iterations=3, poly_n=5, poly_sigma=1.2, flags=0
        )

        magnitude, angle = cv2.cartToPolar(flow[..., 0], flow[..., 1])
        mean_mag  = float(np.mean(magnitude))
        std_angle = float(np.std(angle))

        self.mag_history.append(mean_mag)
        self.prev_gray = gray

        # score based on whether movement falls in 'natural' range
        mag_ok   = self.NATURAL_MAG_MIN < mean_mag < self.NATURAL_MAG_MAX
        angle_ok = std_angle > self.MIN_ANGLE_STD

        # smoothed confidence from history
        recent = list(self.mag_history)
        natural_count = sum(
            1 for m in recent
            if self.NATURAL_MAG_MIN < m < self.NATURAL_MAG_MAX
        )
        confidence = natural_count / max(len(recent), 1)

        return confidence, (mag_ok and angle_ok)

    def reset(self):
        self.prev_gray = None
        self.mag_history.clear()


# ──────────────────────────────────────────────
#  FREQUENCY DOMAIN ANALYSER
# ──────────────────────────────────────────────

class FrequencyAnalyser:
    """
    Uses 2-D FFT on the face region.
    Printed / screen-replayed faces tend to show periodic high-frequency
    artefacts (JPEG blocks, moire patterns) that shift the energy ratio.
    """

    REAL_RATIO_MIN = 1.2   # lebih toleran untuk webcam biasa

    def analyse(self, face_gray: np.ndarray) -> tuple[float, bool]:
        """Returns (centre/edge ratio, is_real bool)."""
        if face_gray.size == 0:
            return 1.0, True

        f       = np.fft.fft2(face_gray.astype(np.float32))
        fshift  = np.fft.fftshift(f)
        spectrum = 20 * np.log(np.abs(fshift) + 1)

        h, w = spectrum.shape
        centre_energy = float(np.mean(spectrum[h//4:3*h//4, w//4:3*w//4]))
        total_energy  = float(np.mean(spectrum))
        edge_energy   = total_energy - centre_energy + 1e-6

        ratio   = centre_energy / edge_energy
        is_real = ratio >= self.REAL_RATIO_MIN

        # map ratio to [0, 1] confidence
        confidence = min(1.0, ratio / (self.REAL_RATIO_MIN * 2))
        return confidence, is_real


# ──────────────────────────────────────────────
#  TEXTURE ANALYSER  (LBP)
# ──────────────────────────────────────────────

class TextureAnalyser:
    """
    Local Binary Pattern texture analysis.
    Real skin has richer texture variation than a flat photo.
    """

    LBP_RADIUS = 3
    LBP_POINTS = 24

    def analyse(self, face_gray: np.ndarray) -> float:
        """Returns confidence score 0-1."""
        if face_gray.size == 0:
            return 0.5

        lbp  = local_binary_pattern(face_gray, self.LBP_POINTS, self.LBP_RADIUS, method='uniform')
        hist, _ = np.histogram(
            lbp.ravel(),
            bins=np.arange(0, self.LBP_POINTS + 3),
            range=(0, self.LBP_POINTS + 2)
        )
        hist = hist.astype("float")
        hist /= (hist.sum() + 1e-6)

        variance   = float(np.var(hist))
        confidence = min(1.0, variance / 0.003)
        return confidence


# ──────────────────────────────────────────────
#  HEAD POSE ESTIMATOR
# ──────────────────────────────────────────────

class HeadPoseEstimator:
    """
    Estimates pitch / yaw / roll using cv2.solvePnP with
    the standard 6-point 3-D facial model.
    """

    # 3-D model points (mm, face-centred coordinate system)
    MODEL_POINTS = np.array([
        (  0.0,    0.0,    0.0),   # nose tip        – landmark 30
        (  0.0, -330.0,  -65.0),   # chin            – landmark  8
        (-225.0,  170.0, -135.0),  # left eye corner – landmark 36
        ( 225.0,  170.0, -135.0),  # right eye corner– landmark 45
        (-150.0, -150.0, -125.0),  # left mouth      – landmark 48
        ( 150.0, -150.0, -125.0),  # right mouth     – landmark 54
    ], dtype=np.float64)

    def estimate(self, shape: np.ndarray, frame_hw: tuple) -> tuple[float, float, float]:
        """Returns (pitch, yaw, roll) in degrees."""
        h, w = frame_hw
        image_points = np.array([
            shape[30], shape[8],
            shape[36], shape[45],
            shape[48], shape[54]
        ], dtype=np.float64)

        focal   = float(w)
        cam_mtx = np.array([
            [focal, 0,     w / 2],
            [0,     focal, h / 2],
            [0,     0,     1    ]
        ], dtype=np.float64)

        dist_coeffs = np.zeros((4, 1))
        ok, rvec, _ = cv2.solvePnP(
            self.MODEL_POINTS, image_points,
            cam_mtx, dist_coeffs,
            flags=cv2.SOLVEPNP_ITERATIVE
        )
        if not ok:
            return 0.0, 0.0, 0.0

        rot_mat, _ = cv2.Rodrigues(rvec)
        angles, *_ = cv2.RQDecomp3x3(rot_mat)
        pitch, yaw, roll = angles[0], angles[1], angles[2]
        return pitch, yaw, roll


# ──────────────────────────────────────────────
#  CHALLENGE MANAGER
# ──────────────────────────────────────────────

class ChallengeManager:
    """
    Generates and evaluates a randomised sequence of N challenges.
    Each challenge has its own timeout.
    """

    CHALLENGE_TIMEOUT = 7.0   # seconds per challenge

    # Thresholds
    MAR_SMILE      = 0.40
    MAR_OPEN_MOUTH = 0.60
    YAW_TURN       = 12.0     # derajat
    PITCH_NOD      = 8.0      # derajat
    EYEBROW_RAISE  = 4.0      # piksel

    def __init__(self, n_challenges: int = 3):
        self.n_challenges   = n_challenges
        self.sequence: list[dict] = []
        self.current_index  = 0
        self.challenge_start: float = 0.0
        self.blink_tracker  = BlinkTracker()

    # ── public interface ──────────────────────

    def start(self):
        pool = list(ChallengeType)
        chosen = random.sample(pool, min(self.n_challenges, len(pool)))
        self.sequence = [
            {"type": c, "passed": False, "attempts": 0}
            for c in chosen
        ]
        self.current_index   = 0
        self.challenge_start = time.time()
        self.blink_tracker.reset()

    @property
    def current_challenge(self) -> dict | None:
        if self.current_index < len(self.sequence):
            return self.sequence[self.current_index]
        return None

    @property
    def is_complete(self) -> bool:
        return self.current_index >= len(self.sequence)

    @property
    def all_passed(self) -> bool:
        return self.is_complete and all(c["passed"] for c in self.sequence)

    @property
    def time_remaining(self) -> float:
        elapsed = time.time() - self.challenge_start
        return max(0.0, self.CHALLENGE_TIMEOUT - elapsed)

    @property
    def timed_out(self) -> bool:
        return self.time_remaining <= 0.0

    def evaluate(
        self,
        ear:   float,
        mar:   float,
        pitch: float,
        yaw:   float,
        shape: np.ndarray | None
    ) -> tuple[bool, str]:
        """
        Evaluate the current challenge.
        Returns (passed, message).
        """
        ch = self.current_challenge
        if ch is None:
            return False, "No active challenge"

        ctype = ch["type"]

        # ── blink_twice ──────────────────────
        if ctype == ChallengeType.BLINK_TWICE:
            did_blink = self.blink_tracker.update(ear)
            count = self.blink_tracker.blink_count
            msg = f"Kedipkan mata 2x — sudah: {count}/2"
            if count >= 2:
                return self._pass_current(msg)
            return False, msg

        # ── smile ────────────────────────────
        if ctype == ChallengeType.SMILE:
            passed = mar > self.MAR_SMILE
            msg = "Tersenyumlah 😊" if not passed else "Senyum terdeteksi ✓"
            if passed:
                return self._pass_current(msg)
            return False, msg

        # ── open_mouth ───────────────────────
        if ctype == ChallengeType.OPEN_MOUTH:
            passed = mar > self.MAR_OPEN_MOUTH
            msg = "Buka mulut lebar-lebar" if not passed else "Mulut terbuka ✓"
            if passed:
                return self._pass_current(msg)
            return False, msg

        # ── turn_left ────────────────────────
        if ctype == ChallengeType.TURN_LEFT:
            passed = yaw < -self.YAW_TURN
            msg = "Gelengkan kepala ke KIRI" if not passed else "Geleng kiri ✓"
            if passed:
                return self._pass_current(msg)
            return False, msg

        # ── turn_right ───────────────────────
        if ctype == ChallengeType.TURN_RIGHT:
            passed = yaw > self.YAW_TURN
            msg = "Gelengkan kepala ke KANAN" if not passed else "Geleng kanan ✓"
            if passed:
                return self._pass_current(msg)
            return False, msg

        # ── nod_up ───────────────────────────
        if ctype == ChallengeType.NOD_UP:
            passed = pitch > self.PITCH_NOD
            msg = "Tengadahkan kepala ke ATAS" if not passed else "Tengadah ✓"
            if passed:
                return self._pass_current(msg)
            return False, msg

        # ── nod_down ─────────────────────────
        if ctype == ChallengeType.NOD_DOWN:
            passed = pitch < -self.PITCH_NOD
            msg = "Tundukkan kepala ke BAWAH" if not passed else "Menunduk ✓"
            if passed:
                return self._pass_current(msg)
            return False, msg

        # ── raise_eyebrow ────────────────────
        if ctype == ChallengeType.RAISE_EYEBROW:
            passed = self._check_eyebrow_raise(shape, ear)
            msg = "Angkat kedua alis Anda" if not passed else "Alis terangkat ✓"
            if passed:
                return self._pass_current(msg)
            return False, msg

        return False, "Tantangan tidak dikenali"

    # ── private helpers ───────────────────────

    def _pass_current(self, msg: str) -> tuple[bool, str]:
        self.sequence[self.current_index]["passed"] = True
        self.current_index += 1
        self.challenge_start = time.time()
        self.blink_tracker.reset()
        return True, msg

    def _check_eyebrow_raise(self, shape: np.ndarray | None, ear: float) -> bool:
        """
        Approximate eyebrow raise: vertical distance between brow and eye
        increases when eyebrows are raised.
        Landmarks: left brow 17-21, right brow 22-26; eyes 36-41, 42-47.
        """
        if shape is None or len(shape) < 48:
            return False
        left_brow_y  = np.mean(shape[17:22, 1])
        right_brow_y = np.mean(shape[22:27, 1])
        left_eye_y   = np.mean(shape[36:42, 1])
        right_eye_y  = np.mean(shape[42:48, 1])
        left_gap  = left_eye_y  - left_brow_y
        right_gap = right_eye_y - right_brow_y
        avg_gap = (left_gap + right_gap) / 2.0
        # large gap → eyebrows raised
        return avg_gap > 20.0 + self.EYEBROW_RAISE


# ──────────────────────────────────────────────
#  MAIN LIVENESS DETECTOR
# ──────────────────────────────────────────────

class LivenessDetector:
    """
    Full liveness pipeline:
      1. Face detection & landmark extraction (dlib)
      2. Blink detection  (state-machine EAR)
      3. Head-pose estimation (solvePnP)
      4. Randomised challenge sequence
      5. Optical-flow analysis (anti-spoof)
      6. Frequency-domain analysis (anti-spoof)
      7. LBP texture analysis (anti-spoof)
      8. Weighted liveness score
    """

    # Scoring weights (must sum to 1.0)
    WEIGHTS = {
        "challenge":  0.50,   # challenge adalah penentu utama
        "optical":    0.20,
        "texture":    0.15,
        "frequency":  0.10,
        "blink_seq":  0.05,
    }

    PASS_THRESHOLD  = 0.70   # 0-1 score needed to pass
    SPOOF_THRESHOLD = 0.15   # hanya SPOOFED kalau score sangat rendah
    SPOOF_MIN_FRAMES = 30    # jangan judge spoof sebelum 30 frame (~15 detik)

    def __init__(
        self,
        landmark_model: str = "models/shape_predictor_68_face_landmarks.dat",
        n_challenges:   int  = 3,
    ):
        # dlib
        self.detector  = dlib.get_frontal_face_detector()
        self.predictor = dlib.shape_predictor(landmark_model)

        # sub-modules
        self.blink_tracker    = BlinkTracker()
        self.optical_flow     = OpticalFlowAnalyser()
        self.freq_analyser    = FrequencyAnalyser()
        self.texture_analyser = TextureAnalyser()
        self.head_pose        = HeadPoseEstimator()
        self.challenge_mgr    = ChallengeManager(n_challenges)

        # session state
        self._session_started = False
        self._frame_count     = 0

    # ── public API ────────────────────────────

    def start_session(self):
        """Call once before processing frames."""
        self.blink_tracker.reset()
        self.optical_flow.reset()
        self.challenge_mgr.start()
        self._session_started = True
        self._frame_count     = 0

    def process_frame(self, frame: np.ndarray) -> dict:
        """
        Process a single BGR frame.
        Returns a result dict with scores, challenge status, and final verdict.
        """
        if not self._session_started:
            raise RuntimeError("Call start_session() before process_frame().")

        gray  = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = self.detector(gray, 0)

        base = {
            "face_detected":   False,
            "ear":             0.0,
            "mar":             0.0,
            "pitch":           0.0,
            "yaw":             0.0,
            "roll":            0.0,
            "blink_count":     self.blink_tracker.blink_count,
            "challenge":       self._challenge_status(),
            "optical_conf":    0.0,
            "texture_conf":    0.0,
            "frequency_conf":  0.0,
            "liveness_score":  0.0,
            "result":          LivenessResult.PENDING,
            "message":         "Memulai...",
        }

        if len(faces) == 0:
            base["message"] = "Wajah tidak terdeteksi"
            return base
        if len(faces) > 1:
            base["message"] = "Terdeteksi lebih dari satu wajah — gunakan satu wajah saja"
            return base

        face  = faces[0]
        shape_dlib = self.predictor(gray, face)
        shape = np.array([[p.x, p.y] for p in shape_dlib.parts()])

        # ── landmarks ──────────────────────────
        left_eye  = shape[36:42]
        right_eye = shape[42:48]
        mouth     = shape[48:68]

        ear = self._ear(left_eye, right_eye)
        mar = self._mar(mouth)

        # ── blink tracking ──────────────────────
        self.blink_tracker.update(ear)

        # ── head pose ───────────────────────────
        h, w = frame.shape[:2]
        pitch, yaw, roll = self.head_pose.estimate(shape, (h, w))

        # ── face region for texture / frequency ─
        fx = max(0, face.left())
        fy = max(0, face.top())
        fw = face.width()
        fh = face.height()
        face_gray = gray[fy:fy+fh, fx:fx+fw]
        face_rect = (fx, fy, fw, fh)

        # ── anti-spoof modules ──────────────────
        self._frame_count += 1
        optical_conf, _ = self.optical_flow.update(frame, face_rect)
        texture_conf    = self.texture_analyser.analyse(face_gray)
        freq_conf, _    = self.freq_analyser.analyse(face_gray)

        # ── challenge evaluation ─────────────────
        ch_passed = False
        ch_msg    = ""
        if not self.challenge_mgr.is_complete:
            if self.challenge_mgr.timed_out:
                ch_msg = f"Time out — challenge failed"
            else:
                ch_passed, ch_msg = self.challenge_mgr.evaluate(
                    ear, mar, pitch, yaw, shape
                )

        # ── compute score ────────────────────────
        blink_seq_score = min(1.0, self.blink_tracker.blink_count / 2)
        challenge_score = 1.0 if self.challenge_mgr.all_passed else (
            self.challenge_mgr.current_index / max(len(self.challenge_mgr.sequence), 1)
        )

        score = (
            self.WEIGHTS["challenge"] * challenge_score +
            self.WEIGHTS["optical"]   * optical_conf    +
            self.WEIGHTS["texture"]   * texture_conf    +
            self.WEIGHTS["frequency"] * freq_conf       +
            self.WEIGHTS["blink_seq"] * blink_seq_score
        )

        # ── result ───────────────────────────────
        if self.challenge_mgr.timed_out and not self.challenge_mgr.all_passed:
            result  = LivenessResult.FAILED
            message = "Waktu habis — verifikasi GAGAL"
        elif self.challenge_mgr.all_passed:
            # semua challenge selesai = PASSED, anti-spoof hanya informasi tambahan
            result  = LivenessResult.PASSED
            message = "Verifikasi BERHASIL ✓"
        elif self._frame_count > self.SPOOF_MIN_FRAMES and score < self.SPOOF_THRESHOLD:
            result  = LivenessResult.SPOOFED
            message = "Terdeteksi sebagai foto/video. Coba lagi."
        else:
            result  = LivenessResult.PENDING
            message = ch_msg or self._challenge_prompt()

        base.update({
            "face_detected":   True,
            "ear":             round(ear,   3),
            "mar":             round(mar,   3),
            "pitch":           round(pitch, 2),
            "yaw":             round(yaw,   2),
            "roll":            round(roll,  2),
            "blink_count":     self.blink_tracker.blink_count,
            "challenge":       self._challenge_status(),
            "optical_conf":    round(optical_conf,  3),
            "texture_conf":    round(texture_conf,  3),
            "frequency_conf":  round(freq_conf,     3),
            "liveness_score":  round(score,         3),
            "result":          result,
            "message":         message,
        })
        return base

    # ── drawing helper ─────────────────────────

    def draw_overlay(self, frame: np.ndarray, result: dict) -> np.ndarray:
        """Draw debug overlay on frame. Returns annotated copy."""
        out = frame.copy()
        h, w = out.shape[:2]

        color = {
            LivenessResult.PASSED:  (0, 220, 0),
            LivenessResult.FAILED:  (0, 0, 220),
            LivenessResult.SPOOFED: (0, 0, 180),
            LivenessResult.PENDING: (220, 200, 0),
        }.get(result["result"], (200, 200, 200))

        lines = [
            f"Result : {result['result'].value.upper()}",
            f"Score  : {result['liveness_score']:.2f}",
            f"EAR    : {result['ear']:.3f}",
            f"MAR    : {result['mar']:.3f}",
            f"Yaw    : {result['yaw']:.1f}°  Pitch: {result['pitch']:.1f}°",
            f"Blinks : {result['blink_count']}",
            f"Optical: {result['optical_conf']:.2f}",
            f"Texture: {result['texture_conf']:.2f}",
            f"Freq   : {result['frequency_conf']:.2f}",
            f"",
            f">> {result['message']}",
        ]
        for i, ch in enumerate(result["challenge"].get("sequence", [])):
            tick = "✓" if ch["passed"] else "○"
            lines.append(f"  {tick} {ch['type']}")

        y0, dy = 24, 22
        for i, line in enumerate(lines):
            cv2.putText(out, line, (10, y0 + i * dy),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.55, color, 1, cv2.LINE_AA)

        # border colour by result
        cv2.rectangle(out, (0, 0), (w - 1, h - 1), color, 3)
        return out

    # ── private helpers ────────────────────────

    @staticmethod
    def _ear(left_eye, right_eye) -> float:
        def _single(eye):
            A = dist.euclidean(eye[1], eye[5])
            B = dist.euclidean(eye[2], eye[4])
            C = dist.euclidean(eye[0], eye[3])
            return (A + B) / (2.0 * C + 1e-6)
        return (_single(left_eye) + _single(right_eye)) / 2.0

    @staticmethod
    def _mar(mouth) -> float:
        A = dist.euclidean(mouth[2], mouth[10])
        B = dist.euclidean(mouth[4], mouth[8])
        C = dist.euclidean(mouth[0], mouth[6])
        return (A + B) / (2.0 * C + 1e-6)

    def _challenge_status(self) -> dict:
        mgr = self.challenge_mgr
        return {
            "current_index": mgr.current_index,
            "total":         len(mgr.sequence),
            "time_remaining": round(mgr.time_remaining, 1),
            "all_passed":    mgr.all_passed,
            "sequence": [
                {"type": c["type"].value, "passed": c["passed"]}
                for c in mgr.sequence
            ],
        }

    def _challenge_prompt(self) -> str:
        ch = self.challenge_mgr.current_challenge
        if ch is None:
            return "Semua tantangan selesai"
        prompts = {
            ChallengeType.BLINK_TWICE:   "Kedipkan mata 2 kali",
            ChallengeType.SMILE:         "Tolong tersenyum",
            ChallengeType.OPEN_MOUTH:    "Tolong buka mulut",
            ChallengeType.TURN_LEFT:     "Gelengkan kepala ke kiri",
            ChallengeType.TURN_RIGHT:    "Gelengkan kepala ke kanan",
            ChallengeType.NOD_UP:        "Tengadahkan kepala ke atas",
            ChallengeType.NOD_DOWN:      "Tundukkan kepala ke bawah",
            ChallengeType.RAISE_EYEBROW: "Angkat kedua alis Anda",
        }
        remaining = self.challenge_mgr.time_remaining
        return f"{prompts.get(ch['type'], '?')}  [{remaining:.1f}d]"


# ──────────────────────────────────────────────
#  DEMO  (run with: python liveness_detector.py)
# ──────────────────────────────────────────────

if __name__ == "__main__":
    cap      = cv2.VideoCapture(0)
    detector = LivenessDetector(n_challenges=3)
    detector.start_session()

    print("Liveness detection started. Press Q to quit.")

    while True:
        ret, frame = cap.read()
        if not ret:
            break

        result  = detector.process_frame(frame)
        display = detector.draw_overlay(frame, result)

        cv2.imshow("Liveness Detection", display)

        if result["result"] in (LivenessResult.PASSED, LivenessResult.FAILED, LivenessResult.SPOOFED):
            print(f"\n[FINAL] {result['result'].value.upper()} | score={result['liveness_score']:.2f}")
            # restart session to try again
            detector.start_session()

        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()