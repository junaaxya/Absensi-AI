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
embeddings = {}

def generate_embedding_from_image(image_path):
    """Generate face embedding from a single image file."""
    try:
        img = cv2.imread(image_path)
        if img is None:
            print(f"  ⚠️ Failed to load image: {image_path}")
            return None
        
        faces = face_app.get(img)
        if len(faces) == 0:
            print(f"  ⚠️ No face detected in: {image_path}")
            return None
        
        # Get the face with highest detection score
        face = sorted(faces, key=lambda x: x.det_score, reverse=True)[0]
        return face.embedding
    except Exception as e:
        print(f"  ⚠️ Error processing {image_path}: {e}")
        return None

def scan_and_generate_embeddings():
    """Scan dataset directory and generate embeddings for all users."""
    global embeddings
    
    # Use absolute path and print it for debugging
    dataset_dir = os.path.join(BASE_DIR, "dataset")
    abs_dataset_path = os.path.abspath(dataset_dir)
    
    print(f"[DEBUG] ============================================")
    print(f"[DEBUG] Starting scan_and_generate_embeddings()")
    print(f"[DEBUG] Absolute Dataset Path: {abs_dataset_path}")
    print(f"[DEBUG] Scanning directory: {dataset_dir}")
    print(f"[DEBUG] BASE_DIR: {BASE_DIR}")
    print(f"[DEBUG] Current working directory: {os.getcwd()}")
    print(f"[DEBUG] ============================================")
    
    if not os.path.exists(dataset_dir):
        print(f"[ERROR] Dataset directory not found: {dataset_dir}")
        print(f"[ERROR] Full path checked: {abs_dataset_path}")
        return
    
    if not os.path.isdir(dataset_dir):
        print(f"[ERROR] Dataset path exists but is not a directory: {dataset_dir}")
        return
    
    # List subdirectories
    try:
        subdirs = os.listdir(dataset_dir)
        print(f"[DEBUG] Found subdirs/files in dataset: {subdirs}")
    except OSError as e:
        print(f"[ERROR] Could not list dataset directory: {e}")
        return
        
    if not subdirs:
        print(f"[WARNING] Dataset directory is empty! No user folders found.")
        print(f"[WARNING] Expected structure: {dataset_dir}/<username>/<images>")
        return

    # Supported image extensions
    image_extensions = ('.jpg', '.jpeg', '.png', '.bmp', '.webp')
    
    print(f"[DEBUG] Starting to process {len(subdirs)} items in dataset...")
    
    for username in subdirs:
        user_dir = os.path.join(dataset_dir, username)
        
        # Skip if not a directory
        if not os.path.isdir(user_dir):
            print(f"[DEBUG] Skipping non-directory: {username}")
            continue
        
        print(f"[DEBUG] Processing folder: {username}")
        print(f"[DEBUG] User directory path: {user_dir}")
        
        # Collect all embeddings for this user
        user_embeddings = []
        
        for filename in os.listdir(user_dir):
            if filename.lower().endswith(image_extensions):
                image_path = os.path.join(user_dir, filename)
                print(f"[DEBUG] Found image: {image_path}")
                print(f"  📷 Processing: {filename}")
                
                embedding = generate_embedding_from_image(image_path)
                if embedding is not None:
                    user_embeddings.append(embedding)
            else:
                print(f"[DEBUG] Skipping non-image file: {filename}")
        
        if user_embeddings:
            # Calculate average embedding
            avg_embedding = np.mean(user_embeddings, axis=0)
            # Normalize
            norm_embedding = avg_embedding / np.linalg.norm(avg_embedding)
            
            # Save to file
            save_path = os.path.join(EMBEDDING_DIR, f"{username}.npy")
            np.save(save_path, norm_embedding)
            print(f"  ✅ Saved embedding: {save_path} (used {len(user_embeddings)} images)")
            
            # Add to memory
            embeddings[username] = norm_embedding
        else:
            print(f"  ⚠️ No valid embeddings generated for {username}")

def load_embeddings():
    global embeddings
    print("[INFO] Loading embeddings...")
    embeddings = {}
    
    # Ensure embedding directory exists
    if not os.path.exists(EMBEDDING_DIR):
        print(f"[DEBUG] Creating embedding directory: {EMBEDDING_DIR}")
        os.makedirs(EMBEDDING_DIR)

    # Load existing embeddings first with error handling
    try:
        embedding_files = os.listdir(EMBEDDING_DIR)
        print(f"[DEBUG] Found {len(embedding_files)} files in embedding dir: {embedding_files}")
        
        for file in embedding_files:
            if file.endswith(".npy"):
                name = os.path.splitext(file)[0]
                path = os.path.join(EMBEDDING_DIR, file)
                try:
                    emb = np.load(path)

                    # ⚠️ NORMALISASI: kalau (N,512) → jadi (512,)
                    if emb.ndim == 2:
                        print(f"🛠️ Fix embedding shape for {name}: {emb.shape} → mean")
                        emb = emb.mean(axis=0)

                    embeddings[name] = emb
                    print(f"[DEBUG] Loaded embedding for: {name}")
                except Exception as e:
                    print(f"[ERROR] Failed to load embedding {file}: {e}")
    except OSError as e:
        print(f"[ERROR] Could not list embedding directory {EMBEDDING_DIR}: {e}")

    print(f"[INFO] Embeddings loaded from cache: {list(embeddings.keys())}")
    
    # CRITICAL FALLBACK: Scan dataset if no embeddings loaded
    if not embeddings:
        print("[INFO] No embeddings found in cache. Scanning dataset...")
        scan_and_generate_embeddings()
        print(f"[INFO] Embeddings loaded after scan: {list(embeddings.keys())}")
    else:
        # Also check if dataset has more users than cache
        dataset_dir = os.path.join(BASE_DIR, "dataset")
        print(f"[DEBUG] Checking dataset for new users at: {dataset_dir}")
        
        if os.path.exists(dataset_dir):
            try:
                dataset_users = [d for d in os.listdir(dataset_dir) if os.path.isdir(os.path.join(dataset_dir, d))]
                print(f"[DEBUG] Dataset has {len(dataset_users)} users: {dataset_users}")
                print(f"[DEBUG] Cache has {len(embeddings)} users: {list(embeddings.keys())}")
                
                if len(dataset_users) > len(embeddings):
                     print(f"[INFO] Detected potential new users in dataset ({len(dataset_users)} users) vs cache ({len(embeddings)}). Scanning...")
                     scan_and_generate_embeddings()
                     print(f"[INFO] Embeddings loaded after rescan: {list(embeddings.keys())}")
            except OSError as e:
                print(f"[ERROR] Could not check dataset directory: {e}")

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
