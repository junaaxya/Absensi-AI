import os
import cv2
import numpy as np
from ultralytics import YOLO
from insightface.app import FaceAnalysis

# Path
DATASET_DIR = "dataset"
EMBEDDING_DIR = "embeddings"
MODEL_PATH = "yolov8n.pt"   

os.makedirs(EMBEDDING_DIR, exist_ok=True)

# Load YOLO
yolo = YOLO(MODEL_PATH)

# Load InsightFace
face_app = FaceAnalysis(name="buffalo_l")
face_app.prepare(ctx_id=0)

# Loop setiap orang
for person_name in os.listdir(DATASET_DIR):
    person_path = os.path.join(DATASET_DIR, person_name)
    if not os.path.isdir(person_path):
        continue

    embeddings = []

    for img_name in os.listdir(person_path):
        img_path = os.path.join(person_path, img_name)
        img = cv2.imread(img_path)

        if img is None:
            continue

        # Deteksi wajah
        results = yolo(img, conf=0.5)
        for r in results:
            for box in r.boxes:
                x1, y1, x2, y2 = map(int, box.xyxy[0])
                face = img[y1:y2, x1:x2]

                if face.size == 0:
                    continue

                faces = face_app.get(face)
                if faces:
                    embeddings.append(faces[0].embedding)

    if embeddings:
        embeddings = np.array(embeddings)
        np.save(os.path.join(EMBEDDING_DIR, f"{person_name}.npy"), embeddings)
        print(f"[OK] Embedding disimpan untuk: {person_name}")
    else:
        print(f"[WARNING] Tidak ada wajah valid untuk: {person_name}")
