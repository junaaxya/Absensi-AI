# Absensi Face Recognition System - Architecture

## Project Overview

Attendance system using face recognition technology with a monorepo structure.

**Tech Stack:**
- **Backend**: Laravel 12 (PHP 8.2+)
- **Face Recognition**: Flask + YOLOv8 + InsightFace (Python)
- **Database**: MySQL 8.0
- **Deployment**: Docker Compose

## Architecture

This is a **monorepo** with two main services that communicate via HTTP:

```
absensi/
├── laravel-app/          # Main web application
├── face-service/         # Face recognition microservice
└── docker-compose.yml    # Service orchestration
```

### Service Communication

```
User → Laravel (port 8000) → Face Service (port 5000)
                  ↓
              MySQL (port 3306)
```

- Laravel calls Face Service via `FLASK_SERVICE_URL` environment variable
- Internal Docker network uses service name: `http://face-service:5000`
- Face Service is stateless; embeddings stored as `.npy` files

## Services

### 1. Laravel App (`laravel-app/`)

**Purpose**: Main web application handling authentication, attendance management, and UI

**Key Features:**
- User authentication (Laravel Breeze)
- Attendance tracking (check-in/check-out)
- Leave request management (izin)
- Employee management
- Profile management with face registration

**Tech:**
- Laravel 12
- Tailwind CSS
- Vite for asset bundling
- MySQL database

**Key Controllers:**
- `AttendanceController`: Dashboard and attendance operations
- `FaceRegistrationController`: Register user face data via API
- `IzinController`: Leave request management
- `EmployeeController`: Admin employee management

**API Endpoints:**
- `POST /api/face/register` - Register face embeddings
- `POST /api/attendance/auto` - Auto-attendance from face recognition

### 2. Face Service (`face-service/`)

**Purpose**: Face detection, recognition, and embedding generation

**Key Features:**
- Face detection using YOLOv8
- Face embedding extraction using InsightFace (buffalo_l model)
- Face recognition via cosine similarity
- Face registration (multi-image averaging)

**Tech:**
- Flask web framework
- YOLOv8n for face detection
- InsightFace for face embeddings
- NumPy for embedding storage

**Endpoints:**
- `GET /` - Test camera interface
- `POST /recognize_frame` - Recognize face from image
- `POST /register-face` - Register new face embeddings

**Data Storage:**
- `embeddings/` - User face embeddings as `{user_id}.npy`
- `yolov8n.pt` - Pre-trained YOLO model weights

### 3. MySQL Database

**Purpose**: Data persistence for users, attendance, and leave requests

**Key Tables:**
- `users` - User accounts with roles (admin/user)
- `attendances` - Check-in/check-out records
- `izins` - Leave requests

## Development Setup

### Prerequisites
- Docker & Docker Compose
- Git

### Quick Start

1. **Clone and setup environment:**
   ```bash
   cd laravel-app
   cp .env.example .env
   # Edit .env to set FLASK_SERVICE_URL=http://face-service:5000
   ```

2. **Start services:**
   ```bash
   docker compose up -d
   ```

3. **Run Laravel migrations:**
   ```bash
   docker compose exec laravel php artisan migrate
   ```

4. **Access application:**
   - Laravel: http://localhost:8000
   - Face Service: http://localhost:5000

### Environment Variables

**Laravel (.env):**
- `DB_CONNECTION=mysql`
- `DB_HOST=mysql`
- `DB_DATABASE=absensi`
- `FLASK_SERVICE_URL=http://face-service:5000`

### Docker Services

```yaml
mysql:         # Database (port 3306)
laravel:       # Web app (port 8000)
face-service:  # AI service (port 5000)
```

## Key Files & Directories

### Laravel App Structure
```
laravel-app/
├── app/Http/Controllers/
│   ├── AttendanceController.php     # Attendance operations
│   ├── IzinController.php          # Leave requests
│   └── Api/
│       ├── FaceRegistrationController.php
│       └── AttendanceController.php
├── app/Models/
│   ├── User.php                    # User model (has face_data flag)
│   ├── Attendance.php
│   └── Izin.php
├── routes/
│   ├── web.php                     # Web routes
│   └── api.php                     # API routes
└── resources/views/                # Blade templates
```

### Face Service Structure
```
face-service/
├── face_service.py                 # Main Flask app
├── embeddings/                     # User face embeddings (.npy)
├── yolov8n.pt                     # YOLO model weights
├── templates/
│   └── test_camera.html           # Browser-based test interface
└── scripts/                       # Training/utility scripts
    ├── step1_detect_crop.py       # Face detection & cropping
    ├── step2_create_embeddings.py # Generate embeddings from dataset
    └── step3_realtime_recognition.py # Realtime recognition test
```

## Workflows

### User Registration Flow
1. User registers via Laravel UI
2. Admin uploads user photos via Profile page
3. Photos sent to Face Service `/register-face`
4. Face Service generates averaged embedding
5. Embedding saved as `{user_id}.npy`
6. User's `has_face_data` flag set to `true`

### Attendance Flow
1. User clicks "Absen Masuk" in dashboard
2. Laravel redirects to Face Service test interface
3. User captures face via webcam
4. Face Service recognizes user
5. Face Service calls back Laravel API `/api/attendance/auto`
6. Attendance record created

## Known Limitations

1. **Training Scripts Paths**: Scripts in `face-service/scripts/` use relative paths. Run them from `face-service/` root:
   ```bash
   cd face-service
   python scripts/step1_detect_crop.py
   ```

2. **CORS**: If Flask and Laravel run on different domains, CORS must be configured in Flask.

3. **Face Recognition Threshold**: Currently set to 0.5 in `face_service.py`. Adjust based on accuracy needs.

## Further Documentation

- `DOCKER_GUIDE.md` - Detailed Docker setup instructions
- `README.md` - Quick start guide
