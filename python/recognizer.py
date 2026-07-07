from insightface.app import FaceAnalysis
import numpy as np
import cv2
import os

app = FaceAnalysis(name="buffalo_l")
app.prepare(ctx_id=0)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
EMBEDDING_DIR = os.path.join(BASE_DIR, "embeddings")
os.makedirs(EMBEDDING_DIR, exist_ok=True)

SIMILARITY_THRESHOLD = 0.60


class FaceRecognizer:

    @staticmethod
    def file_to_image(file):
        data = np.frombuffer(file.read(), np.uint8)
        file.seek(0)
        return cv2.imdecode(data, cv2.IMREAD_COLOR)

    @staticmethod
    def verify(file, user_id):

        user_dir = os.path.join(
            EMBEDDING_DIR,
            str(user_id)
        )

        if not os.path.exists(user_dir):
            return {
                "success": False,
                "message": "Wajah belum didaftarkan"
            }

        image = FaceRecognizer.file_to_image(file)

        if image is None:
            return {
                "success": False,
                "message": "Foto tidak valid"
            }

        faces = app.get(image)

        if len(faces) == 0:
            return {
                "success": False,
                "message": "Wajah tidak ditemukan"
            }

        face = max(
            faces,
            key=lambda f: (f.bbox[2]-f.bbox[0]) * (f.bbox[3]-f.bbox[1])
        )

        embedding_verify = face.embedding

        similarities = []

        for filename in os.listdir(user_dir):

            if not filename.endswith(".npy"):
                continue

            embedding_register = np.load(
                os.path.join(user_dir, filename)
            )

            similarity = np.dot(
                embedding_register,
                embedding_verify
            ) / (
                np.linalg.norm(embedding_register)
                * np.linalg.norm(embedding_verify)
            )

            similarities.append(float(similarity))

        if len(similarities) == 0:
            return {
                "success": False,
                "message": "Embedding kosong"
            }

        best_similarity = max(similarities)

        return {
            "success": best_similarity >= SIMILARITY_THRESHOLD,
            "similarity": round(best_similarity, 4),
            "threshold": SIMILARITY_THRESHOLD
        }

    @staticmethod
    def generate_embeddings(images, user_id):

        user_dir = os.path.join(
            EMBEDDING_DIR,
            str(user_id)
        )

        os.makedirs(user_dir, exist_ok=True)

        result = {}

        for name, file in images.items():

            image = FaceRecognizer.file_to_image(file)

            if image is None:
                result[name] = False
                continue

            faces = app.get(image)

            if len(faces) == 0:
                result[name] = False
                continue

            face = max(
                faces,
                key=lambda f: (f.bbox[2]-f.bbox[0]) * (f.bbox[3]-f.bbox[1])
            )

            np.save(
                os.path.join(user_dir, f"{name}.npy"),
                face.embedding
            )

            result[name] = True

        return result