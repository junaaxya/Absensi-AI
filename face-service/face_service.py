print("🔥🔥🔥 THIS IS THE CORRECT face_service.py 🔥🔥🔥")
print("FILE PATH =", __file__)

from flask import Flask, request, jsonify, render_template
import cv2
import numpy as np
import os
from ultralytics import YOLO
from insightface.app import FaceAnalysis

app = Flask(__name__)

# =========================
# CONFIG
# =========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
EMBEDDING_DIR = os.path.join(BASE_DIR, "embeddings")
MODEL_PATH = os.path.join(BASE_DIR, "yolov8n.pt")

SIMILARITY_THRESHOLD = 0.5

# =========================
# LOAD MODELS
# =========================
print("[INFO] Loading YOLOv8 model...")
yolo = YOLO(MODEL_PATH)

print("[INFO] Loading InsightFace model...")
face_app = FaceAnalysis(name="buffalo_l")
face_app.prepare(ctx_id=0)

# =========================
# LOAD EMBEDDINGS
# =========================
# =========================
# LOAD EMBEDDINGS
# =========================
embeddings = {}

def load_embeddings():
    global embeddings
    print("[INFO] Loading embeddings...")
    embeddings = {}
    
    if not os.path.exists(EMBEDDING_DIR):
        os.makedirs(EMBEDDING_DIR)

    for file in os.listdir(EMBEDDING_DIR):
        if file.endswith(".npy"):
            name = os.path.splitext(file)[0]
            path = os.path.join(EMBEDDING_DIR, file)
            emb = np.load(path)

            # ⚠️ NORMALISASI: kalau (N,512) → jadi (512,)
            if emb.ndim == 2:
                print(f"🛠️ Fix embedding shape for {name}: {emb.shape} → mean")
                emb = emb.mean(axis=0)

            embeddings[name] = emb

    print("[INFO] Embeddings loaded:", list(embeddings.keys()))

# Initial load
load_embeddings()

# =========================
# HELPER: COSINE SIMILARITY
# =========================
def cosine_similarity(a, b):
    a = np.array(a)
    b = np.array(b)

    if a.shape != b.shape:
        print("⚠️ Shape mismatch:", a.shape, b.shape)
        return -1.0

    return float(np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b)))

# =========================
# ROUTE: ROOT → TEST CAMERA PAGE
# =========================
@app.route("/")
def index():
    print("📄 Serving test_camera.html via render_template")
    return render_template("test_camera.html")

# =========================
# ROUTE: RECOGNIZE FRAME
# =========================
@app.route("/recognize_frame", methods=["POST"])
def recognize_frame():
    print("📥 /recognize_frame called")

    if "frame" not in request.files:
        return jsonify({"error": "no frame"}), 400

    file = request.files["frame"]
    img_bytes = np.frombuffer(file.read(), np.uint8)
    frame = cv2.imdecode(img_bytes, cv2.IMREAD_COLOR)

    if frame is None:
        return jsonify({"error": "decode failed"}), 400

    print("🖼️ Frame shape:", frame.shape)

    result_name = "unknown"
    best_score = 0.0
    best_bbox = None

    faces = face_app.get(frame)
    print("🟣 InsightFace faces:", len(faces))

    for face in faces:
        emb = np.array(face.embedding)

        for name, db_emb in embeddings.items():
            db_emb = np.array(db_emb)

            if db_emb.ndim == 2:
                db_emb = db_emb.mean(axis=0)

            if emb.shape != db_emb.shape:
                continue

            score = cosine_similarity(emb, db_emb)

            if score > best_score:
                best_score = score
                result_name = name
                best_bbox = face.bbox.astype(int).tolist()

    # =========================
    # AUTO ABSENSI LOGIC
    # =========================
    status = "rejected"
    if best_score >= SIMILARITY_THRESHOLD:
        status = "accepted"

    print("🏁 FINAL RESULT:", result_name, best_score, status)

    return jsonify({
        "name": result_name,
        "score": float(best_score),
        "status": status,
        "bbox": best_bbox
    })



# =========================
# DEBUG: SHOW ROUTES
# =========================
print("📌 REGISTERED ROUTES:")
print(app.url_map)

@app.route('/register-face', methods=['POST'])
def register_face():
    try:
        user_id = request.form.get('user_id')
        if not user_id:
            return jsonify({'status': 'error', 'message': 'User ID required'}), 400
            
        photos = request.files.getlist('photos')
        if not photos:
            photos = request.files.getlist('photos[]') # Try array notation
            
        if not photos:
            return jsonify({'status': 'error', 'message': 'No photos provided'}), 400

        print(f"Registering face for User ID: {user_id}, Photos: {len(photos)}")

        embeddings_list = [] # Renamed to avoid conflict with global 'embeddings'
        for photo in photos:
            # Convert to numpy array
            in_memory_file = np.frombuffer(photo.read(), np.uint8)
            img = cv2.imdecode(in_memory_file, cv2.IMREAD_COLOR)
            
            if img is None:
                print(f"Failed to decode image: {photo.filename}")
                continue

            # Detect and get embedding
            faces = face_app.get(img) # Changed app_insightface to face_app
            if len(faces) > 0:
                # Ambil wajah terbesar/terbaik
                face = sorted(faces, key=lambda x: x.det_score, reverse=True)[0]
                embeddings_list.append(face.embedding)
            else:
                 print(f"No face detected in: {photo.filename}")

        if not embeddings_list:
            return jsonify({'status': 'error', 'message': 'No valid faces detected in photos'}), 400

        # Calculate average embedding
        avg_embedding = np.mean(embeddings_list, axis=0)
        norm_embedding = avg_embedding / np.linalg.norm(avg_embedding)

        # Save to file
        save_path = os.path.join(EMBEDDING_DIR, f"{user_id}.npy") # Changed EMBEDDINGS_DIR to EMBEDDING_DIR, removed "user_" prefix
        np.save(save_path, norm_embedding)
        
        # Reload embeddings to memory
        load_embeddings()

        return jsonify({
            'status': 'success', 
            'message': f'Face registered successfully. Used {len(embeddings_list)}/{len(photos)} photos.',
            'embedding_path': save_path
        })

    except Exception as e:
        print(f"Error registering face: {e}")
        return jsonify({'status': 'error', 'message': str(e)}), 500

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=False)
