# Draft: Analisis Struktur Project Absensi Face Recognition

## Overview Project

Project ini adalah **sistem absensi berbasis face recognition** dengan arsitektur:
- **Frontend/Backend**: Laravel 12 (PHP)
- **Face Recognition**: Flask microservice (Python) dengan YOLOv8 + InsightFace
- **Database**: MySQL 8.0
- **Orchestration**: Docker Compose

---

## Temuan: Masalah Struktur

### 1. CRITICAL: Bukan Git Repository
- Root folder belum diinisialisasi sebagai git repo
- Tidak ada version control
- **Risiko**: Kehilangan kode, tidak ada rollback

### 2. CRITICAL: Hardcoded URL di AttendanceController
```php
// MASALAH: URL hardcoded localhost
return redirect("http://127.0.0.1:5000/?type=masuk");
return redirect("http://127.0.0.1:5000/?type=pulang");
```
- Tidak bisa jalan di Docker (harus pakai `http://flask:5000`)
- FaceRegistrationController sudah benar pakai env var

### 3. Inkonsistensi Struktur Folder
- `face_service_python/` - Penamaan dengan underscore
- `absensi-face/` - Penamaan dengan dash
- Tidak ada standardisasi penamaan

### 4. Python venv di dalam face_service_python
- Virtual environment disimpan di dalam project folder
- Seharusnya di-ignore atau di luar project

### 5. File Utility Scripts Berserakan
- `step1_detect_crop.py`, `step2_create_embeddings.py`, `step3_realtime_recognition.py`
- Ini scripts development/training, bukan bagian dari production
- Seharusnya di folder terpisah seperti `scripts/` atau `tools/`

### 6. Tidak Ada AGENTS.md / Dokumentasi Arsitektur
- Tidak ada dokumentasi teknis untuk developer baru
- DOCKER_GUIDE.md ada tapi belum ada architecture overview

### 7. Test Coverage Minimal
- Hanya ada auth tests bawaan Breeze
- Tidak ada test untuk:
  - AttendanceController
  - Face recognition API
  - IzinController

---

## Temuan: Yang Sudah Baik

### 1. Docker Setup
- docker-compose.yml sudah proper dengan 3 services
- Volume mounts untuk embeddings persistence
- Health checks ada

### 2. API Structure
- Ada pemisahan Api/ controller untuk REST endpoints
- Sanctum ready untuk API auth

### 3. Laravel Conventions
- Mengikuti standar Laravel 12 structure
- Breeze auth properly configured
- Migrations terstruktur dengan baik

### 4. Flask Service
- Clean separation of concerns
- Endpoints jelas: /recognize_frame, /register-face
- Error handling ada

---

## Rekomendasi Perbaikan

### Priority 1: Critical
1. **Inisialisasi Git repository** dengan proper .gitignore
2. **Fix hardcoded URL** di AttendanceController → gunakan env var
3. **Setup proper .gitignore** untuk:
   - Python venv
   - .env files
   - node_modules
   - storage/
   - embeddings/*.npy (production data)

### Priority 2: High
4. **Reorganisasi Python scripts**
   - Pindahkan step*.py ke `face_service_python/scripts/`
   - Atau pisahkan ke folder terpisah untuk training tools

5. **Standardisasi folder naming**
   - Pilih: underscore atau dash, konsisten

6. **Buat AGENTS.md** atau Architecture documentation

### Priority 3: Medium
7. **Tambah test coverage** untuk core features
8. **Konfigurasi environment** yang proper untuk dev/prod/docker

---

## Struktur yang Direkomendasikan

```
absensi/
├── .git/
├── .gitignore
├── README.md
├── ARCHITECTURE.md
├── docker-compose.yml
├── DOCKER_GUIDE.md
│
├── laravel-app/              # Renamed from absensi-face
│   ├── app/
│   ├── routes/
│   ├── tests/
│   └── ...
│
├── face-service/             # Renamed from face_service_python
│   ├── src/
│   │   └── face_service.py
│   ├── scripts/              # Training & utility scripts
│   │   ├── step1_detect_crop.py
│   │   ├── step2_create_embeddings.py
│   │   └── step3_realtime_recognition.py
│   ├── data/
│   │   ├── embeddings/
│   │   └── dataset/
│   ├── models/               # Pre-trained models
│   │   └── yolov8n.pt
│   ├── requirements.txt
│   └── Dockerfile
│
└── docs/                     # Optional: more documentation
    └── api-reference.md
```

---

## Keputusan User (Confirmed)

### Scope
- **Fokus**: Full Restructure (bukan hanya fix critical)

### Naming Convention
- **Style**: kebab-case (dash)
- **Folder names**: Generic (laravel-app, face-service)

### Folder Mapping
| Current | New |
|---------|-----|
| `absensi-face/` | `laravel-app/` |
| `face_service_python/` | `face-service/` |

### Training Scripts
- Move `step*.py` to `face-service/scripts/`

---

## Open Questions — RESOLVED

### Git Strategy
- **Decision**: Init fresh repository
- Buat .gitignore yang proper

### Test Infrastructure
- **Decision**: Skip testing dulu, fokus restructure

### Documentation
- **Decision**: Ya, buat AGENTS.md dan README

---

## CLEARANCE: ALL REQUIREMENTS CLEAR

Ready for plan generation.
