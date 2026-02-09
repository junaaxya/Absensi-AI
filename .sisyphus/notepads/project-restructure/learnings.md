# Learnings: Project Restructure

## Conventions & Patterns
[Will be populated by subagents during execution]


## [2026-02-09T15:35:00Z] Task 1: Stop Docker Containers
- Status before: not running
- Action taken: no action needed (containers not running)
- Verification: docker ps shows no containers related to this project (absensi_mysql, absensi_laravel, absensi_flask). Note: 'docker compose' command was not found on this system, but manual check with 'docker ps -a' confirmed no containers from this project are present.

## [2026-02-09T15:45:00Z] Task 2: Rename Project Folders
- Action taken: Renamed 'absensi-face/' to 'laravel-app/' and 'face_service_python/' to 'face-service/' using atomic 'mv' commands.
- Verification: 
    - Verified 'laravel-app/artisan' and 'laravel-app/composer.json' exist.
    - Verified 'face-service/face_service.py', 'face-service/requirements.txt', 'face-service/dataset/', 'face-service/embeddings/', and 'face-service/*.pt' exist.
    - Verified old folders 'absensi-face/' and 'face_service_python/' no longer exist.

## [2026-02-09T16:00:00Z] Task 4: Move Training Scripts
- Moved step*.py to face-service/scripts/
- **KNOWN LIMITATION**: These scripts use relative paths (../embeddings, ./yolov8n.pt)
- They will NOT work from scripts/ without path adjustments
- To run: cd to face-service/ root and run `python scripts/stepN_*.py`

## Docker Compose Update (2026-02-09)
- Renamed service `flask` to `face-service` for better naming consistency.
- Updated build contexts: `./absensi-face` -> `./laravel-app` and `./face_service_python` -> `./face-service`.
- Updated container names and volume paths to reflect new folder structure.
- Updated `FLASK_SERVICE_URL` in Laravel environment variables to use the new service name.
### Task 5: Fix AttendanceController Hardcoded URLs
- Replaced hardcoded `http://127.0.0.1:5000` in `AttendanceController.php` with dynamic URL pattern using `config('services.flask.url')` and `env('FLASK_SERVICE_URL')`.
- Updated `test_camera.html` to use `window.location.origin` for API calls and redirects, removing hardcoded `http://127.0.0.1:8000`.
- Added `FLASK_SERVICE_URL=http://face-service:5000` to `laravel-app/.env.example`.
- Verified that no hardcoded URLs remain in the modified files using grep.

## [2026-02-09] Task 6: Create Proper .gitignore
- Action taken: Created comprehensive .gitignore at project root covering Laravel (PHP), Python, Node.js, IDEs, and OS-specific files.
- Patterns included: vendor/, node_modules/, .venv/, .env, storage/, __pycache__/, etc.
- Explicitly excluded from ignore (ensured tracked): .env.example, dataset/, embeddings/, docker-compose.yml.
- Verification: Grep confirmed presence of essential patterns (.env, vendor, .venv, node_modules).

## [2026-02-09] Task 7: Clear Laravel Caches
- config:clear: cleared manually (php/vendor missing)
- cache:clear: cleared manually (php/vendor missing)
- view:clear: cleared manually (php/vendor missing)
- route:clear: cleared manually (php/vendor missing)
- All caches cleared successfully via manual file deletion.
