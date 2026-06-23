from flask import Flask, request, jsonify
from flask_cors import CORS
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address
from Liveness_detector import LivenessDetector, LivenessResult
from face_recognition_service import FaceRecognitionService
import cv2
import numpy as np
import base64
import logging
import traceback
import threading
import time
import uuid
from PIL import Image
from dataclasses import dataclass, field
from typing import Optional
import io

# ──────────────────────────────────────────────
#  APP SETUP
# ──────────────────────────────────────────────

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)

# Rate limiting — cegah brute force & spam request
limiter = Limiter(
    get_remote_address,
    app=app,
    default_limits=["120 per minute"],
    storage_uri="memory://",
)

face_service = FaceRecognitionService()


# ──────────────────────────────────────────────
#  SESSION MANAGEMENT
# ──────────────────────────────────────────────

@dataclass
class LivenessSession:
    """Satu sesi liveness per user."""
    detector:    LivenessDetector
    created_at:  float = field(default_factory=time.time)
    passed:      bool  = False          # True kalau sudah lulus liveness
    finished:    bool  = False          # True kalau passed/failed/spoofed

    SESSION_TTL = 300.0                 # 5 menit — session expired otomatis


# Dict: session_id (UUID) → LivenessSession
# Setiap user/tab browser punya session sendiri — aman untuk multi-user
_sessions: dict[str, LivenessSession] = {}
_sessions_lock = threading.Lock()


def _get_session(session_id: str) -> Optional[LivenessSession]:
    with _sessions_lock:
        return _sessions.get(session_id)


def _create_session() -> tuple[str, LivenessSession]:
    session_id = str(uuid.uuid4())
    detector   = LivenessDetector(n_challenges=3)
    detector.start_session()
    session    = LivenessSession(detector=detector)
    with _sessions_lock:
        _sessions[session_id] = session
    return session_id, session


def _remove_session(session_id: str):
    with _sessions_lock:
        _sessions.pop(session_id, None)


def _cleanup_worker():
    """
    Background thread — hapus session yang sudah expired (> 5 menit).
    Mencegah memory leak jika user tidak menyelesaikan liveness.
    """
    while True:
        time.sleep(60)
        now = time.time()
        with _sessions_lock:
            expired = [
                sid for sid, s in _sessions.items()
                if now - s.created_at > LivenessSession.SESSION_TTL
            ]
            for sid in expired:
                del _sessions[sid]
                logger.info(f"[SESSION] {sid[:8]}... expired dan dihapus")


# Jalankan cleanup di background
threading.Thread(target=_cleanup_worker, daemon=True).start()


# ──────────────────────────────────────────────
#  HELPER
# ──────────────────────────────────────────────

def base64_to_cv2(base64_string: str) -> np.ndarray:
    """Convert base64 image string ke OpenCV BGR array."""
    if ',' in base64_string:
        base64_string = base64_string.split(',')[1]
    img_data = base64.b64decode(base64_string)
    img      = Image.open(io.BytesIO(img_data))
    if img.mode != 'RGB':
        img = img.convert('RGB')
    return cv2.cvtColor(np.array(img), cv2.COLOR_RGB2BGR)


def ok(data: dict, code: int = 200):
    return jsonify({"success": True, **data}), code


def err(message: str, code: int = 400):
    return jsonify({"success": False, "error": message}), code


def get_validated_frame(data: dict):
    """Parse dan validasi frame dari request. Return (frame, error_response)."""
    frame_b64 = data.get('frame')
    if not frame_b64:
        return None, err("Field 'frame' wajib diisi")
    try:
        return base64_to_cv2(frame_b64), None
    except Exception as e:
        return None, err(f"Frame tidak valid: {e}")


def get_validated_session(data: dict, require_passed: bool = False):
    """
    Ambil dan validasi session dari request.
    require_passed=True → session harus sudah lulus liveness.
    Return (session_id, session, error_response)
    """
    session_id = data.get('session_id')
    if not session_id:
        return None, None, err("Field 'session_id' wajib diisi — panggil /liveness/start dulu")

    session = _get_session(session_id)
    if session is None:
        return None, None, err("Session tidak ditemukan atau sudah expired — panggil /liveness/start ulang", 401)

    if require_passed and not session.passed:
        return None, None, err("Liveness belum lulus — selesaikan /liveness/process dulu", 403)

    return session_id, session, None


# ──────────────────────────────────────────────
#  HEALTH
# ──────────────────────────────────────────────

@app.route('/health', methods=['GET'])
def health_check():
    with _sessions_lock:
        active_sessions = len(_sessions)
    return ok({
        "message":          "Liveness + Face Recognition API is running",
        "active_sessions":  active_sessions,
        "registered_users": len(face_service.list_users()),
    })


# ──────────────────────────────────────────────
#  LIVENESS — session management
# ──────────────────────────────────────────────

@app.route('/liveness/start', methods=['POST'])
@limiter.limit("20 per minute")
def liveness_start():
    """
    Mulai sesi liveness baru.
    Setiap user/tab mendapat session_id unik — aman untuk multi-user.

    Returns:
        session_id : string — simpan dan kirim di setiap request berikutnya
    """
    try:
        session_id, session = _create_session()
        logger.info(f"[SESSION] Baru: {session_id[:8]}...")
        return ok({
            "session_id":   session_id,
            "message":      "Liveness session dimulai",
            "n_challenges": session.detector.challenge_mgr.n_challenges,
        })
    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


@app.route('/liveness/process', methods=['POST'])
@limiter.limit("60 per minute")
def liveness_process():
    """
    Proses satu frame liveness. Panggil terus tiap frame kamera.

    Body JSON:
        session_id : string — dari /liveness/start
        frame      : string — base64 image

    Returns:
        result     : pending | passed | failed | spoofed
        + ear, mar, pitch, yaw, blink_count, message, dll
    """
    try:
        data = request.get_json()

        session_id, session, error = get_validated_session(data)
        if error:
            return error

        if session.finished:
            return err("Session sudah selesai — panggil /liveness/start untuk sesi baru", 400)

        frame, error = get_validated_frame(data)
        if error:
            return error

        result           = session.detector.process_frame(frame)
        result_str       = result["result"].value
        result["result"] = result_str

        # Update status session berdasarkan hasil
        if result_str == "passed":
            session.passed   = True
            session.finished = True
            logger.info(f"[SESSION] {session_id[:8]}... LULUS liveness")

        elif result_str in ("failed", "spoofed"):
            session.finished = True
            _remove_session(session_id)   # hapus session gagal
            logger.warning(f"[SESSION] {session_id[:8]}... GAGAL liveness ({result_str})")

        return ok({"session_id": session_id, "data": result})

    except RuntimeError as e:
        return err(str(e) + " — panggil /liveness/start dulu", 400)
    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  FACE REGISTRATION
# ──────────────────────────────────────────────

@app.route('/face/register', methods=['POST'])
@limiter.limit("10 per minute")
def register_face():
    """
    Daftarkan wajah user.
    Liveness HARUS sudah lulus di server (session.passed == True).
    Client tidak bisa memalsukan liveness_passed.

    Body JSON:
        session_id : string — dari /liveness/start (harus sudah passed)
        frame      : string — base64 image
        user_id    : string
    """
    try:
        data = request.get_json()

        # Validasi session + liveness
        session_id, session, error = get_validated_session(data, require_passed=True)
        if error:
            return error

        user_id = data.get('user_id', '').strip()
        if not user_id:
            return err("Field 'user_id' wajib diisi")

        frame, error = get_validated_frame(data)
        if error:
            return error

        success, message = face_service.register_face(
            frame, user_id, liveness_passed=True
        )

        # Hapus session setelah registrasi (1 session = 1 aksi)
        _remove_session(session_id)

        logger.info(f"[REGISTER] user={user_id}, success={success}")
        return ok({"message": message, "user_id": user_id}) if success else err(message)

    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


@app.route('/face/register-simple', methods=['POST'])
@limiter.limit("10 per minute")
def register_face_simple():
    """
    Daftarkan wajah user TANPA liveness — khusus pendaftaran akun baru.
    Cukup kirim frame dan user_id, tidak perlu session_id.

    Body JSON:
        frame   : string — base64 image
        user_id : string
    """
    try:
        data = request.get_json()

        user_id = data.get('user_id', '').strip()
        if not user_id:
            return err("Field 'user_id' wajib diisi")

        frame, error = get_validated_frame(data)
        if error:
            return error

        success, message = face_service.register_face(
            frame, user_id, liveness_passed=True
        )

        logger.info(f"[REGISTER-SIMPLE] user={user_id}, success={success}")
        return ok({"message": message, "user_id": user_id}) if success else err(message)

    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  FACE VERIFICATION  (1-to-1)
# ──────────────────────────────────────────────

@app.route('/face/verify', methods=['POST'])
@limiter.limit("20 per minute")
def verify_face():
    """
    Verifikasi wajah untuk user_id tertentu (1-to-1).
    Liveness divalidasi di server — client tidak bisa bypass.

    Body JSON:
        session_id : string — dari /liveness/start (harus sudah passed)
        frame      : string — base64 image
        user_id    : string
    """
    try:
        data = request.get_json()

        session_id, session, error = get_validated_session(data, require_passed=True)
        if error:
            return error

        user_id = data.get('user_id', '').strip()
        if not user_id:
            return err("Field 'user_id' wajib diisi")

        frame, error = get_validated_frame(data)
        if error:
            return error

        result = face_service.verify_face(frame, user_id, liveness_passed=True)

        # Hapus session setelah verifikasi
        _remove_session(session_id)

        logger.info(
            f"[VERIFY] user={user_id}, matched={result.matched}, "
            f"sim={result.similarity}, locked={result.locked}"
        )

        http_code = 423 if result.locked else 200
        return jsonify({
            "success":    True,
            "is_match":   result.matched,
            "similarity": result.similarity,
            "user_id":    result.user_id,
            "message":    result.message,
            "locked":     result.locked,
        }), http_code

    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  FACE IDENTIFICATION  (1-to-N)
# ──────────────────────────────────────────────

@app.route('/face/identify', methods=['POST'])
@limiter.limit("10 per minute")
def identify_face():
    """
    Identifikasi siapa orangnya dari semua user terdaftar (1-to-N).
    Tidak perlu kirim user_id.
    Liveness divalidasi di server.

    Body JSON:
        session_id : string — dari /liveness/start (harus sudah passed)
        frame      : string — base64 image
    """
    try:
        data = request.get_json()

        session_id, session, error = get_validated_session(data, require_passed=True)
        if error:
            return error

        frame, error = get_validated_frame(data)
        if error:
            return error

        result = face_service.identify_face(frame, liveness_passed=True)

        # Hapus session setelah identifikasi
        _remove_session(session_id)

        logger.info(
            f"[IDENTIFY] matched={result.matched}, user={result.user_id}, "
            f"sim={result.similarity}"
        )

        return ok({
            "matched":    result.matched,
            "user_id":    result.user_id,
            "similarity": result.similarity,
            "message":    result.message,
        })

    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  USER MANAGEMENT
# ──────────────────────────────────────────────

@app.route('/face/users', methods=['GET'])
@limiter.limit("30 per minute")
def list_users():
    """List semua user yang terdaftar."""
    try:
        users = face_service.list_users()
        return ok({"users": users, "count": len(users)})
    except Exception as e:
        return err(str(e), 500)


@app.route('/face/users/<user_id>', methods=['DELETE'])
@limiter.limit("10 per minute")
def delete_user(user_id: str):
    """Hapus user dari database."""
    try:
        deleted = face_service.delete_user(user_id)
        if deleted:
            logger.info(f"[DELETE] user={user_id}")
            return ok({"message": f"User '{user_id}' berhasil dihapus"})
        return err(f"User '{user_id}' tidak ditemukan", 404)
    except Exception as e:
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  ERROR HANDLERS GLOBAL
# ──────────────────────────────────────────────

@app.errorhandler(404)
def not_found(e):
    return err("Endpoint tidak ditemukan", 404)


@app.errorhandler(405)
def method_not_allowed(e):
    return err("Method tidak diizinkan", 405)


@app.errorhandler(429)
def rate_limit_exceeded(e):
    return err("Terlalu banyak request — coba lagi sebentar", 429)


# ──────────────────────────────────────────────
#  MAIN
# ──────────────────────────────────────────────

if __name__ == '__main__':
    print("🚀 Starting Liveness + Face Recognition API...")
    print("📍 Running on http://localhost:5000\n")
    print("Endpoints:")
    print("  GET    /health")
    print("")
    print("  POST   /liveness/start          — mulai sesi liveness (dapat session_id)")
    print("  POST   /liveness/process        — proses frame (kirim session_id + frame)")
    print("")
    print("  POST   /face/register           — daftarkan wajah (butuh session_id yang sudah passed)")
    print("  POST   /face/register-simple    — daftarkan wajah TANPA liveness (untuk registrasi akun)")
    print("  POST   /face/verify             — verifikasi 1-to-1  (butuh session_id yang sudah passed)")
    print("  POST   /face/identify           — identifikasi 1-to-N (butuh session_id yang sudah passed)")
    print("  GET    /face/users              — list semua user")
    print("  DELETE /face/users/<user_id>    — hapus user")
    print("")
    print("Alur penggunaan:")
    print("  1. POST /liveness/start          → dapat session_id")
    print("  2. POST /liveness/process (loop) → kirim frame terus sampai result=passed")
    print("  3. POST /face/verify atau /face/identify → kirim session_id yang sudah passed")
    app.run(host='0.0.0.0', port=5000, debug=False)