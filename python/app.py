print("RUNNING:", __file__)
from flask import Flask, request, jsonify
from recognizer import FaceRecognizer
from datetime import datetime
import os
import random

app = Flask(__name__)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

CHALLENGES = [
    "left",
    "right",
]
CURRENT_CHALLENGE = {}

@app.get("/")
def home():
    return {
        "status": "ok",
        "message": "Face API Running"
    }

@app.route("/generate-embedding", methods=["POST"])
def generate_embedding():
    user_id = request.form.get("user_id")
    if not user_id:
        return jsonify({
            "success": False,
            "message": "User ID diperlukan"
        }), 400

    required = [
        "front",
        "left",
        "right",
        "up",
        "down"
    ]

    files = request.files
    for photo in required:
        if photo not in files:
            return jsonify({
                "success": False,
                "message": f"Foto {photo} tidak ditemukan"
            }), 400

    images = {}
    for key in required:
        images[key] = files[key]

    try:
        result = FaceRecognizer.generate_embeddings(
            images,
            user_id
        )

        if not all(result.values()):
            return jsonify({
                "success": False,
                "message": "Sebagian foto gagal diproses",
                "data": result
            }), 400

        return jsonify({
            "success": True,
            "message": "Embedding berhasil dibuat",
            "data": result
        })

    except Exception as e:
        return jsonify({
            "success": False,
            "message": str(e)
        }), 500

@app.route("/verify", methods=["POST"])
def verify():
    print("VERIFY CALLED")
    if "photo" not in request.files:
        return jsonify({
            "success": False,
            "message": "Foto diperlukan"
        }), 400

    user_id = request.form.get("user_id")
    if not user_id:
        return jsonify({
            "success": False,
            "message": "ID Pengguna diperlukan"
        }), 400

    photo = request.files["photo"]
    result = FaceRecognizer.verify(
        photo,
        user_id
    )

    return jsonify(result)

if __name__ == "__main__":
    app.run(
        host="127.0.0.1",
        port=5000,
        debug=False,
        use_reloader=False
    )