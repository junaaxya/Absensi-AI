import cv2
import os
import numpy as np
from ultralytics import YOLO
from insightface.app import FaceAnalysis

# =======================
# CONFIG
# =======================
EMBEDDING_DIR = "embeddings"
MODEL_PATH = "yolov8n.pt"
SIMILARITY_THRESHOLD = 0.5  # bisa disesuaikan (0.45–0.6)

# =======================
# LOAD MODELS
# =======================
yolo = YOLO(MODEL_PATH)

face_app = FaceAnalysis(name="buffalo_l")
face_app.prepare(ctx_id=0)

# =======================
# LOAD EMBEDDINGS
# =======================
known_embeddings = {}
for file in os.listdir(EMBEDDING_DIR):
    if file.endswith(".npy"):
        name = file.replace(".npy", "")
        known_embeddings[name] = np.load(os.path.join(EMBEDDING_DIR, file))

print("[INFO] Embeddings loaded:", list(known_embeddings.keys()))

# =======================
# COSINE SIMILARITY
# =======================
def cosine_similarity(a, b):
    return np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b))

# =======================
# START WEBCAM
# =======================
cap = cv2.VideoCapture(0)

print("[INFO] Tekan 'q' untuk keluar")

while True:
    ret, frame = cap.read()
    if not ret:
        break

    results = yolo(frame, conf=0.5)

    for r in results:
        for box in r.boxes:
            x1, y1, x2, y2 = map(int, box.xyxy[0])
            face_img = frame[y1:y2, x1:x2]

            name = "Unknown"

            if face_img.size != 0:
                faces = face_app.get(face_img)
                if faces:
                    emb = faces[0].embedding

                    best_score = 0
                    best_name = "Unknown"

                    for person, embs in known_embeddings.items():
                        for ref_emb in embs:
                            score = cosine_similarity(emb, ref_emb)
                            if score > best_score:
                                best_score = score
                                best_name = person

                    if best_score > SIMILARITY_THRESHOLD:
                        name = best_name

            # DRAW
            cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
            cv2.putText(
                frame,
                name,
                (x1, y1 - 10),
                cv2.FONT_HERSHEY_SIMPLEX,
                0.8,
                (0, 255, 0),
                2
            )

    cv2.imshow("Face Recognition - Absensi", frame)

    if cv2.waitKey(1) & 0xFF == ord("q"):
        break

cap.release()
cv2.destroyAllWindows()
