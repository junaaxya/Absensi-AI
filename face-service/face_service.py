print("🔥🔥🔥 THIS IS THE CORRECT face_service.py 🔥🔥🔥")
print("FILE PATH =", __file__)

from flask import Flask, request, jsonify, render_template, send_from_directory
import cv2
import numpy as np
import os
import time
import shutil
from ultralytics import YOLO
from insightface.app import FaceAnalysis
from werkzeug.utils import secure_filename

app = Flask(__name__)

# =========================
# CONFIG
# =========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
EMBEDDING_DIR = os.path.join(BASE_DIR, "embeddings")
MODEL_PATH = os.path.join(BASE_DIR, "yolov8n.pt")

SIMILARITY_THRESHOLD = 0.5
LIVENESS_THRESHOLD = 0.5

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


def get_user_dataset_dir(username):
    return os.path.join(BASE_DIR, "dataset", username)


def safe_identity(value):
    if not value:
        return None
    cleaned = secure_filename(value)
    return cleaned if cleaned else None


def regenerate_embedding_for_user(username):
    """Regenerate user's embedding from remaining dataset photos."""
    user_dir = get_user_dataset_dir(username)
    image_extensions = ('.jpg', '.jpeg', '.png', '.bmp', '.webp')
    embedding_path = os.path.join(EMBEDDING_DIR, f"{username}.npy")

    if not os.path.isdir(user_dir):
        if os.path.exists(embedding_path):
            os.remove(embedding_path)
        embeddings.pop(username, None)
        return 0

    embeddings_list = []
    for filename in os.listdir(user_dir):
        if filename.lower().endswith(image_extensions):
            image_path = os.path.join(user_dir, filename)
            embedding = generate_embedding_from_image(image_path)
            if embedding is not None:
                embeddings_list.append(embedding)

    if not embeddings_list:
        if os.path.exists(embedding_path):
            os.remove(embedding_path)
        embeddings.pop(username, None)
        return 0

    avg_embedding = np.mean(embeddings_list, axis=0)
    norm_embedding = avg_embedding / np.linalg.norm(avg_embedding)
    np.save(embedding_path, norm_embedding)
    embeddings[username] = norm_embedding
    return len(embeddings_list)

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
# LIVENESS DETECTION
# =========================
def check_liveness(image):
    """
    Perform liveness detection using texture, color, and sharpness analysis.
    Returns (liveness_score, is_live) tuple.
    """
    scores = []
    weights = []

    # 1. LBP Texture Analysis (25% weight)
    try:
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        # Compute LBP-like texture using Laplacian of Gaussian
        # Real faces have richer texture variation
        lbp_kernel = np.array([[1, 1, 1], [1, -8, 1], [1, 1, 1]], dtype=np.float32)
        lbp_response = cv2.filter2D(gray, cv2.CV_32F, lbp_kernel)
        lbp_variance = float(np.var(lbp_response))
        # Normalize: real faces typically have variance > 100
        # Printed/screen faces tend to have lower variance
        lbp_score = min(1.0, lbp_variance / 500.0)
        scores.append(lbp_score)
        weights.append(0.25)
    except Exception as e:
        print(f"  [Liveness] LBP analysis error: {e}")
        scores.append(0.5)
        weights.append(0.25)

    # 2. Color Distribution in YCrCb (15% weight)
    try:
        ycrcb = cv2.cvtColor(image, cv2.COLOR_BGR2YCrCb)
        # Real skin has specific Cr/Cb distribution
        cr_channel = ycrcb[:, :, 1].astype(float)
        cb_channel = ycrcb[:, :, 2].astype(float)
        cr_std = float(np.std(cr_channel))
        cb_std = float(np.std(cb_channel))
        # Real faces have moderate Cr/Cb std deviation (10-30 range)
        # Printed photos tend to have different distributions
        cr_score = 1.0 if 8.0 < cr_std < 40.0 else 0.3
        cb_score = 1.0 if 5.0 < cb_std < 35.0 else 0.3
        color_score = (cr_score + cb_score) / 2.0
        scores.append(color_score)
        weights.append(0.15)
    except Exception as e:
        print(f"  [Liveness] Color analysis error: {e}")
        scores.append(0.5)
        weights.append(0.15)

    # 3. Laplacian Variance for blur/sharpness (60% weight)
    try:
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY) if len(image.shape) == 3 else image
        laplacian = cv2.Laplacian(gray, cv2.CV_64F)
        lap_variance = float(laplacian.var())
        # Real faces captured by camera have specific sharpness patterns
        # Screen captures tend to have different sharpness (either too sharp or too blurry)
        # Typical real face: variance 50-500
        # Screen/print: often < 30 or unnaturally high
        if lap_variance < 15.0:
            sharp_score = 0.1  # Too blurry - likely printed
        elif lap_variance < 50.0:
            sharp_score = 0.4  # Somewhat blurry
        elif lap_variance > 1500.0:
            sharp_score = 0.3  # Unnaturally sharp - possible screen
        else:
            sharp_score = min(1.0, lap_variance / 300.0)
        scores.append(sharp_score)
        weights.append(0.60)
    except Exception as e:
        print(f"  [Liveness] Sharpness analysis error: {e}")
        scores.append(0.5)
        weights.append(0.60)

    # Weighted average
    total_weight = sum(weights)
    if total_weight > 0:
        liveness_score = sum(s * w for s, w in zip(scores, weights)) / total_weight
    else:
        liveness_score = 0.0

    is_live = liveness_score >= LIVENESS_THRESHOLD

    print(f"  [Liveness] Score: {liveness_score:.3f}, Live: {is_live}, "
          f"LBP: {scores[0]:.3f}, Color: {scores[1]:.3f}, Sharp: {scores[2]:.3f}")

    return liveness_score, is_live


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

    anti_spoofing = request.form.get("anti_spoofing_enabled", "true").lower() != "false"

    liveness_score = None
    if anti_spoofing and best_score >= SIMILARITY_THRESHOLD:
        liveness_score, is_live = check_liveness(frame)
        if not is_live:
            print("🚫 Liveness check FAILED:", liveness_score)
            return jsonify({
                "status": "rejected",
                "message": "Liveness check failed",
                "liveness_score": float(liveness_score),
                "name": result_name,
                "score": float(best_score),
                "bbox": best_bbox
            })

    print("🏁 FINAL RESULT:", result_name, best_score, status)

    response_data = {
        "name": result_name,
        "score": float(best_score),
        "status": status,
        "bbox": best_bbox
    }
    if liveness_score is not None:
        response_data["liveness_score"] = float(liveness_score)

    return jsonify(response_data)



@app.route("/health", methods=["GET"])
def health():
    models_loaded = yolo is not None and face_app is not None
    return jsonify({
        "status": "ok",
        "models_loaded": models_loaded,
        "users_loaded": len(embeddings),
    })


# =========================
# ROUTE: REGISTER NEW USER (Single Photo)
# =========================
@app.route('/register', methods=['POST'])
def register():
    """Register a new user with a single photo during Laravel registration."""
    try:
        name = request.form.get('name')
        if not name:
            return jsonify({'status': 'error', 'message': 'Name is required'}), 400
        
        if 'file' not in request.files:
            return jsonify({'status': 'error', 'message': 'Photo file is required'}), 400
        
        file = request.files['file']
        if file.filename == '':
            return jsonify({'status': 'error', 'message': 'No file selected'}), 400
        
        print(f"📥 Registering new user: {name}")
        
        # Convert file to numpy array for processing
        file_bytes = file.read()
        nparr = np.frombuffer(file_bytes, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        
        if img is None:
            return jsonify({'status': 'error', 'message': 'Failed to decode image'}), 400
        
        # Detect face and generate embedding
        faces = face_app.get(img)
        if len(faces) == 0:
            return jsonify({'status': 'error', 'message': 'No face detected in the photo'}), 400
        
        # Get the face with highest detection score
        face = sorted(faces, key=lambda x: x.det_score, reverse=True)[0]
        embedding = face.embedding
        
        # Normalize embedding
        norm_embedding = embedding / np.linalg.norm(embedding)
        
        # Create dataset directory for user
        user_dataset_dir = os.path.join(BASE_DIR, "dataset", name)
        os.makedirs(user_dataset_dir, exist_ok=True)
        
        # Save the image to dataset folder
        timestamp = str(int(time.time()))
        image_filename = f"{name}_{timestamp}.jpg"
        image_path = os.path.join(user_dataset_dir, image_filename)
        cv2.imwrite(image_path, img)
        print(f"💾 Saved image to: {image_path}")
        
        # Save embedding to embeddings folder
        embedding_path = os.path.join(EMBEDDING_DIR, f"{name}.npy")
        np.save(embedding_path, norm_embedding)
        print(f"💾 Saved embedding to: {embedding_path}")
        
        # Update runtime memory immediately
        global embeddings
        embeddings[name] = norm_embedding
        print(f"✅ Registered user '{name}' with face embedding. Total users: {len(embeddings)}")
        
        return jsonify({
            'status': 'success',
            'message': f'User {name} registered successfully with face data',
            'user': name,
            'image_saved': image_path,
            'embedding_saved': embedding_path
        })
        
    except Exception as e:
        print(f"❌ Error during registration: {e}")
        return jsonify({'status': 'error', 'message': str(e)}), 500

# =========================
# DEBUG: SHOW ROUTES
# =========================
print("📌 REGISTERED ROUTES:")
print(app.url_map)

@app.route('/register-face', methods=['POST'])
def register_face():
    """Register/update face data with multiple photos (used from API/dashboard)."""
    try:
        username = request.form.get('username')
        if not username:
            return jsonify({'status': 'error', 'message': 'Username is required'}), 400

        photos = request.files.getlist('photos')
        if not photos:
            photos = request.files.getlist('photos[]')  # Try array notation

        if not photos:
            return jsonify({'status': 'error', 'message': 'No photos provided'}), 400

        print(f"📥 Registering face for username: {username}, Photos: {len(photos)}")

        # Create dataset directory for user
        user_dataset_dir = os.path.join(BASE_DIR, "dataset", username)
        os.makedirs(user_dataset_dir, exist_ok=True)

        embeddings_list = []
        for idx, photo in enumerate(photos):
            # Convert to numpy array
            in_memory_file = np.frombuffer(photo.read(), np.uint8)
            img = cv2.imdecode(in_memory_file, cv2.IMREAD_COLOR)

            if img is None:
                print(f"  ⚠️ Failed to decode image: {photo.filename}")
                continue

            # Detect and get embedding
            faces = face_app.get(img)
            if len(faces) > 0:
                # Ambil wajah terbesar/terbaik
                face = sorted(faces, key=lambda x: x.det_score, reverse=True)[0]
                embeddings_list.append(face.embedding)

                # Save photo to dataset folder
                timestamp = str(int(time.time()))
                image_filename = f"{username}_{timestamp}_{idx}.jpg"
                image_path = os.path.join(user_dataset_dir, image_filename)
                cv2.imwrite(image_path, img)
                print(f"  💾 Saved image to: {image_path}")
            else:
                print(f"  ⚠️ No face detected in: {photo.filename}")

        if not embeddings_list:
            return jsonify({'status': 'error', 'message': 'No valid faces detected in photos'}), 400

        # Calculate average embedding
        avg_embedding = np.mean(embeddings_list, axis=0)
        norm_embedding = avg_embedding / np.linalg.norm(avg_embedding)

        # Save to file (using username as key)
        save_path = os.path.join(EMBEDDING_DIR, f"{username}.npy")
        np.save(save_path, norm_embedding)
        print(f"  💾 Saved embedding to: {save_path}")

        # Update runtime memory directly (faster than full reload)
        global embeddings
        embeddings[username] = norm_embedding
        print(f"  ✅ Registered '{username}' with {len(embeddings_list)}/{len(photos)} photos. Total users: {len(embeddings)}")

        return jsonify({
            'status': 'success',
            'message': f'Face registered successfully. Used {len(embeddings_list)}/{len(photos)} photos.',
            'embedding_path': save_path
        })

    except Exception as e:
        print(f"❌ Error registering face: {e}")
        return jsonify({'status': 'error', 'message': str(e)}), 500


@app.route('/face-dataset/<username>', methods=['GET'])
def face_dataset(username):
    safe_username = safe_identity(username)
    if not safe_username:
        return jsonify({'status': 'error', 'message': 'Invalid username'}), 400

    user_dir = get_user_dataset_dir(safe_username)
    image_extensions = ('.jpg', '.jpeg', '.png', '.bmp', '.webp')

    photos = []
    if os.path.isdir(user_dir):
        for filename in sorted(os.listdir(user_dir)):
            if filename.lower().endswith(image_extensions):
                safe_file = safe_identity(filename)
                if not safe_file:
                    continue
                photos.append({
                    'filename': safe_file,
                    'status': 'valid',
                    'photo_url': f"/face-dataset/{safe_username}/photo/{safe_file}"
                })

    embedding_path = os.path.join(EMBEDDING_DIR, f"{safe_username}.npy")
    registered = os.path.exists(embedding_path) and len(photos) > 0

    return jsonify({
        'status': 'success',
        'username': safe_username,
        'registered': registered,
        'photo_count': len(photos),
        'photos': photos,
    })


@app.route('/face-dataset/<username>/photo/<filename>', methods=['GET'])
def face_dataset_photo(username, filename):
    safe_username = safe_identity(username)
    safe_filename = safe_identity(filename)

    if not safe_username or not safe_filename:
        return jsonify({'status': 'error', 'message': 'Invalid path'}), 400

    user_dir = get_user_dataset_dir(safe_username)
    file_path = os.path.join(user_dir, safe_filename)

    if not os.path.exists(file_path):
        return jsonify({'status': 'error', 'message': 'Photo not found'}), 404

    return send_from_directory(user_dir, safe_filename)


@app.route('/face-dataset/<username>', methods=['DELETE'])
def delete_face_dataset(username):
    safe_username = safe_identity(username)
    if not safe_username:
        return jsonify({'status': 'error', 'message': 'Invalid username'}), 400

    user_dir = get_user_dataset_dir(safe_username)
    embedding_path = os.path.join(EMBEDDING_DIR, f"{safe_username}.npy")

    if os.path.isdir(user_dir):
        shutil.rmtree(user_dir, ignore_errors=True)

    if os.path.exists(embedding_path):
        os.remove(embedding_path)

    embeddings.pop(safe_username, None)

    return jsonify({
        'status': 'success',
        'message': 'Face dataset deleted',
        'username': safe_username,
        'remaining_photo_count': 0,
    })


@app.route('/face-dataset/<username>/photo/<filename>', methods=['DELETE'])
def delete_face_dataset_photo(username, filename):
    safe_username = safe_identity(username)
    safe_filename = safe_identity(filename)

    if not safe_username or not safe_filename:
        return jsonify({'status': 'error', 'message': 'Invalid path'}), 400

    user_dir = get_user_dataset_dir(safe_username)
    file_path = os.path.join(user_dir, safe_filename)

    if not os.path.exists(file_path):
        return jsonify({'status': 'error', 'message': 'Photo not found'}), 404

    os.remove(file_path)

    remaining_count = 0
    if os.path.isdir(user_dir):
        image_extensions = ('.jpg', '.jpeg', '.png', '.bmp', '.webp')
        remaining_files = [
            f for f in os.listdir(user_dir)
            if f.lower().endswith(image_extensions)
        ]
        remaining_count = len(remaining_files)

        if remaining_count == 0:
            shutil.rmtree(user_dir, ignore_errors=True)

    valid_embedding_count = regenerate_embedding_for_user(safe_username)

    return jsonify({
        'status': 'success',
        'message': 'Photo deleted',
        'username': safe_username,
        'remaining_photo_count': remaining_count,
        'registered': valid_embedding_count > 0,
    })

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=False)
