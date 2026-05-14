from flask import Flask, request, jsonify
from flask_cors import CORS
from Liveness_detector import LivenessDetector, LivenessResult
from face_recognition_service import FaceRecognitionService
import cv2
import numpy as np
import base64
import logging
import traceback
from PIL import Image
import io

# ──────────────────────────────────────────────
#  APP SETUP
# ──────────────────────────────────────────────

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)

face_service = FaceRecognitionService()

# Satu session liveness per instance (stateful)
# Untuk multi-user, ganti dengan dict: sessions[session_id]
liveness_detector = LivenessDetector(n_challenges=3)


# ──────────────────────────────────────────────
#  HELPER
# ──────────────────────────────────────────────

def base64_to_cv2(base64_string: str) -> np.ndarray:
    """Convert base64 image string ke OpenCV BGR array."""
    if ',' in base64_string:
        base64_string = base64_string.split(',')[1]
    img_data  = base64.b64decode(base64_string)
    img       = Image.open(io.BytesIO(img_data))
    if img.mode != 'RGB':
        img = img.convert('RGB')
    img_bgr = cv2.cvtColor(np.array(img), cv2.COLOR_RGB2BGR)
    return img_bgr


def err(message: str, code: int = 400):
    return jsonify({"success": False, "error": message}), code


# ──────────────────────────────────────────────
#  HEALTH
# ──────────────────────────────────────────────

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
        "status": "ok",
        "message": "Liveness Detection API is running"
    })


# ──────────────────────────────────────────────
#  LIVENESS  —  session management
# ──────────────────────────────────────────────

@app.route('/liveness/start', methods=['POST'])
def liveness_start():
    """
    Mulai sesi liveness baru.
    Harus dipanggil sebelum /liveness/process.
    """
    try:
        liveness_detector.start_session()
        return jsonify({
            "success": True,
            "message": "Liveness session started",
            "n_challenges": liveness_detector.challenge_mgr.n_challenges,
        })
    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


@app.route('/liveness/process', methods=['POST'])
def liveness_process():
    """
    Proses satu frame liveness.

    Body JSON:
        frame  : string  — base64 image
    
    Returns hasil lengkap termasuk:
        result         : pending | passed | failed | spoofed
        liveness_score : float 0-1
        challenge      : status challenge sequence
        ear, mar, pitch, yaw, roll, blink_count
        optical_conf, texture_conf, frequency_conf
        message        : instruksi untuk user
    """
    try:
        data         = request.get_json()
        frame_b64    = data.get('frame')

        if not frame_b64:
            return err("No frame provided")

        frame  = base64_to_cv2(frame_b64)
        result = liveness_detector.process_frame(frame)

        # convert enum to string for JSON
        result["result"] = result["result"].value

        return jsonify({"success": True, "data": result})

    except RuntimeError as e:
        # start_session() belum dipanggil
        return err(str(e) + " — call /liveness/start first", 400)
    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  FACE REGISTRATION
# ──────────────────────────────────────────────

@app.route('/face/register', methods=['POST'])
def register_face():
    """
    Daftarkan wajah user.
    Wajib sudah lulus liveness (liveness_passed: true di body).

    Body JSON:
        frame          : string  — base64 image
        user_id        : string
        liveness_passed: bool    — harus true
    """
    try:
        data            = request.get_json()
        frame_b64       = data.get('frame')
        user_id         = data.get('user_id')
        liveness_passed = bool(data.get('liveness_passed', False))

        if not frame_b64 or not user_id:
            return err("Missing 'frame' or 'user_id'")

        if not liveness_passed:
            return err("liveness_passed must be true — complete liveness check first", 403)

        frame           = base64_to_cv2(frame_b64)
        success, message = face_service.register_face(
            frame, user_id, liveness_passed=liveness_passed
        )

        status = 200 if success else 400
        return jsonify({"success": success, "message": message}), status

    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  FACE VERIFICATION  (1-to-1)
# ──────────────────────────────────────────────

@app.route('/face/verify', methods=['POST'])
def verify_face():
    """
    Verifikasi wajah untuk user_id tertentu (1-to-1).
    Wajib sudah lulus liveness.

    Body JSON:
        frame          : string  — base64 image
        user_id        : string
        liveness_passed: bool
    """
    try:
        data            = request.get_json()
        frame_b64       = data.get('frame')
        user_id         = data.get('user_id')
        liveness_passed = bool(data.get('liveness_passed', False))

        if not frame_b64 or not user_id:
            return err("Missing 'frame' or 'user_id'")

        if not liveness_passed:
            return err("liveness_passed must be true", 403)

        frame  = base64_to_cv2(frame_b64)
        result = face_service.verify_face(frame, user_id, liveness_passed=liveness_passed)

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
#  FACE IDENTIFICATION  (1-to-N)  — baru
# ──────────────────────────────────────────────

@app.route('/face/identify', methods=['POST'])
def identify_face():
    """
    Identifikasi siapa orangnya dari semua user terdaftar (1-to-N).
    Tidak perlu kirim user_id.

    Body JSON:
        frame          : string  — base64 image
        liveness_passed: bool
    """
    try:
        data            = request.get_json()
        frame_b64       = data.get('frame')
        liveness_passed = bool(data.get('liveness_passed', False))

        if not frame_b64:
            return err("No frame provided")

        if not liveness_passed:
            return err("liveness_passed must be true", 403)

        frame  = base64_to_cv2(frame_b64)
        result = face_service.identify_face(frame, liveness_passed=liveness_passed)

        return jsonify({
            "success":    True,
            "matched":    result.matched,
            "user_id":    result.user_id,
            "similarity": result.similarity,
            "message":    result.message,
        })

    except Exception as e:
        logger.error(traceback.format_exc())
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  USER MANAGEMENT  — baru
# ──────────────────────────────────────────────

@app.route('/face/users', methods=['GET'])
def list_users():
    """List semua user yang terdaftar."""
    try:
        users = face_service.list_users()
        return jsonify({"success": True, "users": users, "count": len(users)})
    except Exception as e:
        return err(str(e), 500)


@app.route('/face/users/<user_id>', methods=['DELETE'])
def delete_user(user_id: str):
    """Hapus user dari database."""
    try:
        deleted = face_service.delete_user(user_id)
        if deleted:
            return jsonify({"success": True, "message": f"User '{user_id}' deleted"})
        return err(f"User '{user_id}' not found", 404)
    except Exception as e:
        return err(str(e), 500)


# ──────────────────────────────────────────────
#  MAIN
# ──────────────────────────────────────────────

if __name__ == '__main__':
    print("🚀 Starting Liveness + Face Recognition API...")
    print("📍 Running on http://localhost:5000\n")
    print("Endpoints:")
    print("  GET    /health")
    print("")
    print("  POST   /liveness/start          — mulai sesi liveness")
    print("  POST   /liveness/process        — proses frame (kirim terus tiap frame)")
    print("")
    print("  POST   /face/register           — daftarkan wajah (butuh liveness_passed)")
    print("  POST   /face/verify             — verifikasi 1-to-1  (butuh liveness_passed)")
    print("  POST   /face/identify           — identifikasi 1-to-N (butuh liveness_passed)")
    print("  GET    /face/users              — list semua user")
    print("  DELETE /face/users/<user_id>    — hapus user")
    app.run(host='0.0.0.0', port=5000, debug=False)