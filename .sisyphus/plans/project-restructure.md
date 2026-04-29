# Project Restructure: Absensi Face Recognition

## TL;DR

> **Quick Summary**: Restructure monorepo project absensi - rename folders ke kebab-case, fix hardcoded URLs, setup git repository dengan proper .gitignore, dan create architecture documentation.
> 
> **Deliverables**:
> - Folder structure yang clean dan konsisten
> - Git repository terinitialisasi dengan .gitignore proper
> - Hardcoded URL bugs fixed
> - AGENTS.md dan README.md documentation
> 
> **Estimated Effort**: Medium (~2-3 jam)
> **Parallel Execution**: YES - 2 waves (some tasks parallel)
> **Critical Path**: Stop Docker → Rename Folders → Update docker-compose → Fix URLs → Git Init → Documentation

---

## Context

### Original Request
User meminta analisis struktur project. Ditemukan berbagai masalah:
- Bukan git repository
- Hardcoded URLs
- Inkonsistensi penamaan folder
- File utility scripts berserakan
- Tidak ada documentation

User memilih "Full Restructure" sebagai solusi.

### Interview Summary
**Key Discussions**:
- Naming convention: kebab-case (dash style)
- Folder renames: `absensi-face/` → `laravel-app/`, `face_service_python/` → `face-service/`
- Docker service rename: `flask` → `face-service`
- Training scripts: Move ke `face-service/scripts/`
- Git: Init fresh repository
- Testing: Skip (fokus struktur dulu)
- Documentation: Create AGENTS.md dan README

**Research Findings**:
- Docker setup sudah proper dengan 3 services
- Laravel mengikuti standard structure
- Flask service punya clean endpoints
- AttendanceController hardcoded localhost:5000 (bug)
- FaceRegistrationController sudah benar pakai env var

### Metis Review
**Identified Gaps** (addressed):
- Docker service naming: User memilih rename ke `face-service` → cascading changes ke env vars
- step*.py path issues: Document sebagai known limitation (scripts akan break setelah move karena relative path)
- test_camera.html hardcoded URLs: Fix dengan window.location.origin
- Missing FLASK_SERVICE_URL di .env.example: Add saat fix AttendanceController
- .venv folder: DO NOT move, add ke .gitignore

---

## Work Objectives

### Core Objective
Restructure project dari state berantakan ke clean monorepo dengan consistent naming, proper git setup, dan architecture documentation.

### Concrete Deliverables
- `laravel-app/` (renamed from absensi-face/)
- `face-service/` (renamed from face_service_python/)
- `face-service/scripts/` containing training scripts
- Updated `docker-compose.yml` with new paths and service names
- Fixed `AttendanceController.php` using env var
- `.gitignore` file with proper exclusions
- `AGENTS.md` architecture documentation
- `README.md` project overview
- Git repository with initial commit

### Definition of Done
- [x] `docker compose config` shows no errors
- [x] `git log --oneline` shows initial commit
- [x] `grep -r "127.0.0.1:5000" laravel-app/app/` returns empty
- [x] All old folder names removed

### Must Have
- Folder renames completed
- Docker still works after restructure
- Hardcoded URLs fixed
- Git initialized with proper .gitignore
- AGENTS.md created

### Must NOT Have (Guardrails)
- ❌ DO NOT move or modify `.venv/` directory
- ❌ DO NOT delete `dataset/` or `embeddings/` directories
- ❌ DO NOT change Flask API endpoint paths
- ❌ DO NOT modify database schema
- ❌ DO NOT refactor code logic in step*.py (just move files)
- ❌ DO NOT delete model files (*.pt, *.npy)
- ❌ DO NOT commit `.env` file (only `.env.example`)
- ❌ DO NOT add test infrastructure (user said skip)

---

## Verification Strategy (MANDATORY)

> **UNIVERSAL RULE: ZERO HUMAN INTERVENTION**
>
> ALL tasks in this plan MUST be verifiable WITHOUT any human action.
> ALL verification is executed by the agent using tools (Bash, etc.). No exceptions.

### Test Decision
- **Infrastructure exists**: NO (Laravel has Pest but user said skip testing)
- **Automated tests**: None (skipped per user request)
- **Framework**: N/A

### Agent-Executed QA Scenarios (MANDATORY — ALL tasks)

Every task includes detailed verification scenarios the executing agent will run.

---

## Execution Strategy

### Parallel Execution Waves

```
Wave 1 (Pre-requisite):
└── Task 1: Stop Docker containers

Wave 2 (Structural Changes - SEQUENTIAL):
├── Task 2: Rename folders → MUST complete first
├── Task 3: Update docker-compose.yml
├── Task 4: Move training scripts to scripts/
└── Task 5: Fix AttendanceController URL bug

Wave 3 (Git & Documentation - CAN PARALLEL):
├── Task 6: Create .gitignore
├── Task 7: Clear Laravel caches
├── Task 8: Init git repository
├── Task 9: Create AGENTS.md
└── Task 10: Create README.md

Wave 4 (Final):
└── Task 11: Final verification & commit
```

### Dependency Matrix

| Task | Depends On | Blocks | Can Parallelize With |
|------|------------|--------|---------------------|
| 1 | None | 2, 3, 4, 5 | None |
| 2 | 1 | 3, 4, 5, 6, 7, 8 | None |
| 3 | 2 | 7, 8 | 4, 5 |
| 4 | 2 | 8 | 3, 5 |
| 5 | 2 | 8 | 3, 4 |
| 6 | 2 | 8 | 3, 4, 5, 7 |
| 7 | 2, 3 | 8 | 6 |
| 8 | 6, 7, 3, 4, 5 | 9, 10, 11 | None |
| 9 | 8 | 11 | 10 |
| 10 | 8 | 11 | 9 |
| 11 | 9, 10 | None | None |

---

## TODOs

- [x] 1. Stop Running Docker Containers

  **What to do**:
  - Check if any Docker containers are running for this project
  - Stop all containers if running
  - This prevents file lock issues during rename

  **Must NOT do**:
  - Don't remove volumes (preserve data)

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Simple single command task
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: NO
  - **Parallel Group**: Wave 1 (pre-requisite)
  - **Blocks**: Tasks 2, 3, 4, 5
  - **Blocked By**: None

  **References**:
  - `docker-compose.yml` - Current service definitions

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: Docker containers stopped
    Tool: Bash
    Preconditions: In project root directory
    Steps:
      1. docker compose ps
      2. If containers running: docker compose down
      3. docker compose ps again
    Expected Result: No running containers for this project
    Failure Indicators: "Up" status shown for any container
    Evidence: Output of docker compose ps showing no containers
  ```

  **Commit**: NO

---

- [x] 2. Rename Project Folders

  **What to do**:
  - Rename `absensi-face/` to `laravel-app/`
  - Rename `face_service_python/` to `face-service/`
  - Verify all files moved correctly

  **Must NOT do**:
  - Don't rename `.venv/` inside face_service_python
  - Don't modify any file contents (just move)
  - Don't delete original folders until verified

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Simple file system operations
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: NO
  - **Parallel Group**: Wave 2 (first in sequence)
  - **Blocks**: Tasks 3, 4, 5, 6, 7, 8
  - **Blocked By**: Task 1

  **References**:
  - Current folder structure from analysis
  - Metis directive: Use `mv` for atomic operations

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: Laravel folder renamed successfully
    Tool: Bash
    Preconditions: Task 1 completed (Docker stopped)
    Steps:
      1. mv absensi-face laravel-app
      2. ls -la laravel-app/artisan
      3. ls -la laravel-app/composer.json
      4. ! ls -d absensi-face 2>/dev/null && echo "Old folder removed"
    Expected Result: artisan and composer.json exist in new location
    Failure Indicators: "No such file or directory" for new path
    Evidence: ls output showing files exist

  Scenario: Flask folder renamed successfully
    Tool: Bash
    Preconditions: Laravel folder renamed
    Steps:
      1. mv face_service_python face-service
      2. ls -la face-service/face_service.py
      3. ls -la face-service/requirements.txt
      4. ! ls -d face_service_python 2>/dev/null && echo "Old folder removed"
    Expected Result: face_service.py exists in new location
    Failure Indicators: "No such file or directory" for new path
    Evidence: ls output showing files exist

  Scenario: Data directories preserved
    Tool: Bash
    Preconditions: Folders renamed
    Steps:
      1. ls -la face-service/dataset/
      2. ls -la face-service/embeddings/
      3. ls face-service/*.pt
    Expected Result: dataset/, embeddings/, and *.pt files exist
    Failure Indicators: Directories or files missing
    Evidence: ls output showing data preserved
  ```

  **Commit**: NO (will commit after all structural changes)

---

- [x] 3. Update docker-compose.yml

  **What to do**:
  - Update build context paths to new folder names
  - Rename service `flask` to `face-service`
  - Update container names accordingly
  - Update FLASK_SERVICE_URL to use new service name
  - Validate with docker compose config

  **Must NOT do**:
  - Don't change port mappings
  - Don't change volume mounts (except paths)
  - Don't add new services

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Single file edit with known changes
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2 (with Tasks 4, 5)
  - **Blocks**: Tasks 7, 8
  - **Blocked By**: Task 2

  **References**:
  - `docker-compose.yml` - Current configuration
  - Metis directive: Service name change cascades to environment variables
  - `laravel` service depends on `flask` → update to `face-service`

  **Changes Required**:
  ```yaml
  # OLD                         # NEW
  flask:                        face-service:
    build:                        build:
      context: ./face_service_python  context: ./face-service
    container_name: absensi_flask     container_name: absensi_face_service

  laravel:                      laravel:
    build:                        build:
      context: ./absensi-face         context: ./laravel-app
    depends_on:                   depends_on:
      - flask                         - face-service
    environment:                  environment:
      FLASK_SERVICE_URL: http://flask:5000    FLASK_SERVICE_URL: http://face-service:5000
  ```

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: docker-compose.yml syntax valid
    Tool: Bash
    Preconditions: Folders already renamed
    Steps:
      1. docker compose config > /dev/null
      2. echo $? (should be 0)
    Expected Result: Exit code 0, no errors
    Failure Indicators: YAML parse error, unknown service name
    Evidence: Successful config output or exit code

  Scenario: Service names updated
    Tool: Bash
    Preconditions: docker-compose.yml edited
    Steps:
      1. grep "face-service:" docker-compose.yml
      2. grep "flask:" docker-compose.yml | grep -v FLASK (should be empty)
      3. grep "context: ./face-service" docker-compose.yml
      4. grep "context: ./laravel-app" docker-compose.yml
    Expected Result: face-service service exists, no flask service (except FLASK_SERVICE_URL)
    Failure Indicators: Old service name still present
    Evidence: grep outputs

  Scenario: Environment variable updated
    Tool: Bash
    Preconditions: docker-compose.yml edited
    Steps:
      1. grep "FLASK_SERVICE_URL.*face-service" docker-compose.yml
    Expected Result: URL points to face-service:5000
    Failure Indicators: Still points to flask:5000
    Evidence: grep output
  ```

  **Commit**: NO (will commit together)

---

- [x] 4. Move Training Scripts to scripts/ Subfolder

  **What to do**:
  - Create `face-service/scripts/` directory
  - Move `step1_detect_crop.py` to scripts/
  - Move `step2_create_embeddings.py` to scripts/
  - Move `step3_realtime_recognition.py` to scripts/

  **Must NOT do**:
  - Don't modify script contents
  - Don't fix the relative path issues (document as known limitation)
  - Don't move face_service.py (main app stays in root)

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Simple file move operations
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2 (with Tasks 3, 5)
  - **Blocks**: Task 8
  - **Blocked By**: Task 2

  **References**:
  - `face-service/step1_detect_crop.py` - Uses relative path `models/yolo-face.pt`
  - `face-service/step2_create_embeddings.py` - Uses relative paths `dataset`, `embeddings`, `yolov8n.pt`
  - `face-service/step3_realtime_recognition.py` - Uses relative paths `embeddings`, `yolov8n.pt`
  - Metis note: These scripts will break after move due to relative paths - just document

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: Scripts moved to scripts/ directory
    Tool: Bash
    Preconditions: face-service/ folder exists
    Steps:
      1. mkdir -p face-service/scripts
      2. mv face-service/step1_detect_crop.py face-service/scripts/
      3. mv face-service/step2_create_embeddings.py face-service/scripts/
      4. mv face-service/step3_realtime_recognition.py face-service/scripts/
      5. ls face-service/scripts/
    Expected Result: All 3 step*.py files in scripts/
    Failure Indicators: Files not found in new location
    Evidence: ls output showing files

  Scenario: Main service file untouched
    Tool: Bash
    Preconditions: Scripts moved
    Steps:
      1. ls face-service/face_service.py
    Expected Result: face_service.py still in face-service/ root
    Failure Indicators: File missing
    Evidence: ls output
  ```

  **Commit**: NO (will commit together)

---

- [x] 5. Fix AttendanceController Hardcoded URL

  **What to do**:
  - Replace hardcoded `http://127.0.0.1:5000` with env var
  - Use same pattern as FaceRegistrationController
  - Add `FLASK_SERVICE_URL` to `.env.example`
  - Update `test_camera.html` to use dynamic URL

  **Must NOT do**:
  - Don't change the redirect logic
  - Don't modify other controllers
  - Don't change query parameters

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Small code changes in known locations
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2 (with Tasks 3, 4)
  - **Blocks**: Task 8
  - **Blocked By**: Task 2

  **References**:
  - `laravel-app/app/Http/Controllers/AttendanceController.php:46,55` - Hardcoded URLs to fix
  - `laravel-app/app/Http/Controllers/Api/FaceRegistrationController.php:23` - Pattern to follow: `config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000'))`
  - `face-service/templates/test_camera.html:279,354` - Hardcoded localhost:8000

  **Code Changes Required**:
  
  AttendanceController.php:
  ```php
  // OLD:
  return redirect("http://127.0.0.1:5000/?type=masuk");
  // NEW:
  $flaskUrl = config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000'));
  return redirect("{$flaskUrl}/?type=masuk");
  ```

  test_camera.html (use window.location.origin):
  ```javascript
  // OLD:
  fetch('http://127.0.0.1:8000/api/attendance/auto', ...)
  // NEW:
  fetch(window.location.origin + '/api/attendance/auto', ...)
  ```

  .env.example (add):
  ```
  FLASK_SERVICE_URL=http://face-service:5000
  ```

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: No hardcoded Flask URLs in AttendanceController
    Tool: Bash
    Preconditions: Folder renamed to laravel-app/
    Steps:
      1. grep -n "127.0.0.1:5000" laravel-app/app/Http/Controllers/AttendanceController.php
      2. echo $? (should be 1 = not found)
    Expected Result: No matches (exit code 1)
    Failure Indicators: Lines still contain hardcoded URL
    Evidence: grep returns nothing

  Scenario: Env var pattern used
    Tool: Bash
    Preconditions: AttendanceController edited
    Steps:
      1. grep "FLASK_SERVICE_URL" laravel-app/app/Http/Controllers/AttendanceController.php
    Expected Result: FLASK_SERVICE_URL referenced
    Failure Indicators: No env var usage
    Evidence: grep output showing env var

  Scenario: FLASK_SERVICE_URL in .env.example
    Tool: Bash
    Preconditions: AttendanceController fixed
    Steps:
      1. grep "FLASK_SERVICE_URL" laravel-app/.env.example
    Expected Result: Variable documented
    Failure Indicators: Variable missing
    Evidence: grep output

  Scenario: test_camera.html uses dynamic URL
    Tool: Bash
    Preconditions: test_camera.html edited
    Steps:
      1. grep "127.0.0.1:8000" face-service/templates/test_camera.html
      2. echo $? (should be 1 = not found)
      3. grep "window.location.origin" face-service/templates/test_camera.html
    Expected Result: No hardcoded URL, uses window.location.origin
    Failure Indicators: Hardcoded URL still present
    Evidence: grep outputs
  ```

  **Commit**: NO (will commit together)

---

- [x] 6. Create Proper .gitignore

  **What to do**:
  - Create comprehensive .gitignore at project root
  - Include patterns for: PHP/Laravel, Python, Node.js, IDE files, OS files
  - Specifically exclude: .env (but not .env.example), vendor/, node_modules/, .venv/, storage/framework/

  **Must NOT do**:
  - Don't exclude dataset/ or embeddings/ by default (user may want them tracked)
  - Don't exclude docker-compose.yml

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Single file creation with known content
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 3 (with Task 7)
  - **Blocks**: Task 8
  - **Blocked By**: Task 2

  **References**:
  - Standard Laravel .gitignore patterns
  - Standard Python .gitignore patterns
  - Metis directive: .venv must be excluded

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: .gitignore created with essential patterns
    Tool: Bash
    Preconditions: Folders renamed
    Steps:
      1. cat .gitignore | grep "vendor"
      2. cat .gitignore | grep ".venv"
      3. cat .gitignore | grep "node_modules"
      4. cat .gitignore | grep ".env" | grep -v ".example"
    Expected Result: All patterns present
    Failure Indicators: Any pattern missing
    Evidence: grep outputs

  Scenario: .env.example NOT ignored
    Tool: Bash
    Preconditions: .gitignore created
    Steps:
      1. cat .gitignore | grep "!.*env.example" || echo "Check manually: .env.example should not match .env pattern"
    Expected Result: .env.example is not ignored (negation pattern or separate line)
    Failure Indicators: .env.example would be ignored
    Evidence: grep output or manual check note
  ```

  **Commit**: NO (will commit together)

---

- [x] 7. Clear Laravel Caches

  **What to do**:
  - Run artisan commands to clear all caches
  - This prevents issues from old path references in cached configs

  **Must NOT do**:
  - Don't run migrations
  - Don't seed database

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Simple artisan commands
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 3 (with Task 6)
  - **Blocks**: Task 8
  - **Blocked By**: Tasks 2, 3

  **References**:
  - `laravel-app/artisan` - Laravel CLI tool
  - Metis directive: Run `php artisan view:clear` after folder rename

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: All Laravel caches cleared
    Tool: Bash
    Preconditions: Folders renamed, in laravel-app/ directory
    Steps:
      1. cd laravel-app && php artisan config:clear
      2. php artisan cache:clear
      3. php artisan view:clear
      4. php artisan route:clear
    Expected Result: All commands succeed
    Failure Indicators: Error messages
    Evidence: Command outputs showing "... cleared successfully"
  ```

  **Commit**: NO (will commit together)

---

- [x] 8. Initialize Git Repository

  **What to do**:
  - Initialize git at project root
  - Stage all files (respecting .gitignore)
  - Create initial commit

  **Must NOT do**:
  - Don't push to remote (no remote set up yet)
  - Don't create branches yet

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Standard git initialization
  - **Skills**: `["git-master"]`
    - git-master: For atomic commits and best practices

  **Parallelization**:
  - **Can Run In Parallel**: NO
  - **Parallel Group**: Wave 3 (sequential, after 6 and 7)
  - **Blocks**: Tasks 9, 10, 11
  - **Blocked By**: Tasks 3, 4, 5, 6, 7

  **References**:
  - `.gitignore` from Task 6

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: Git repository initialized
    Tool: Bash
    Preconditions: .gitignore created, caches cleared
    Steps:
      1. git init
      2. git rev-parse --git-dir
    Expected Result: .git directory exists
    Failure Indicators: "not a git repository" error
    Evidence: Output showing .git

  Scenario: Initial commit created
    Tool: Bash
    Preconditions: Git initialized
    Steps:
      1. git add .
      2. git status (verify correct files staged)
      3. git commit -m "chore: initial commit with restructured project"
      4. git log --oneline -1
    Expected Result: Commit hash shown
    Failure Indicators: Nothing to commit, or commit failed
    Evidence: git log output

  Scenario: Ignored files not committed
    Tool: Bash
    Preconditions: Initial commit made
    Steps:
      1. git ls-files | grep -E "(vendor|node_modules|\.venv|\.env$)"
      2. echo $? (should be 1 = not found)
    Expected Result: No ignored files in repo
    Failure Indicators: Ignored files appear in git ls-files
    Evidence: Empty grep result
  ```

  **Commit**: YES (this IS the commit task)
  - Message: `chore: initial commit with restructured project`
  - Files: All files respecting .gitignore

---

- [x] 9. Create AGENTS.md Documentation

  **What to do**:
  - Create AGENTS.md at project root
  - Document project architecture, services, and how they interact
  - Include setup instructions and development workflow
  - List key files and their purposes

  **Must NOT do**:
  - Don't document individual API endpoints (too detailed)
  - Don't include sensitive configuration

  **Recommended Agent Profile**:
  - **Category**: `writing`
    - Reason: Documentation creation
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 3 (with Task 10)
  - **Blocks**: Task 11
  - **Blocked By**: Task 8

  **References**:
  - Analysis from interview: project is monorepo with Laravel + Flask
  - docker-compose.yml for service definitions
  - Key controllers and their purposes

  **Content Outline**:
  ```markdown
  # AGENTS.md
  
  ## Project Overview
  - Attendance system with face recognition
  - Monorepo structure
  
  ## Architecture
  - laravel-app/: Main web application (Laravel 12)
  - face-service/: Face recognition microservice (Flask + YOLOv8 + InsightFace)
  - MySQL database
  
  ## Services
  - How they communicate
  - Ports and URLs
  
  ## Development Setup
  - Docker commands
  - Environment variables
  
  ## Key Files
  - Controllers
  - Models
  - Face service endpoints
  ```

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: AGENTS.md created with required sections
    Tool: Bash
    Preconditions: Git initialized
    Steps:
      1. test -f AGENTS.md && echo "File exists"
      2. grep -i "architecture" AGENTS.md
      3. grep -i "laravel" AGENTS.md
      4. grep -i "face-service" AGENTS.md
      5. grep -i "docker" AGENTS.md
    Expected Result: All sections present
    Failure Indicators: File missing or sections missing
    Evidence: grep outputs
  ```

  **Commit**: Groups with Task 11

---

- [x] 10. Create README.md

  **What to do**:
  - Create or update README.md at project root
  - Include project description, quick start, and requirements
  - Reference AGENTS.md for detailed architecture

  **Must NOT do**:
  - Don't duplicate AGENTS.md content
  - Don't include installation steps for development tools

  **Recommended Agent Profile**:
  - **Category**: `writing`
    - Reason: Documentation creation
  - **Skills**: `[]`
    - No special skills needed

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 3 (with Task 9)
  - **Blocks**: Task 11
  - **Blocked By**: Task 8

  **References**:
  - Project overview from analysis
  - DOCKER_GUIDE.md for existing documentation style

  **Content Outline**:
  ```markdown
  # Absensi Face Recognition
  
  ## Overview
  Attendance system using face recognition technology.
  
  ## Quick Start
  docker compose up -d
  
  ## Requirements
  - Docker & Docker Compose
  - ...
  
  ## Documentation
  - See AGENTS.md for architecture details
  - See DOCKER_GUIDE.md for Docker setup
  ```

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: README.md created with required sections
    Tool: Bash
    Preconditions: Git initialized
    Steps:
      1. test -f README.md && echo "File exists"
      2. grep -i "absensi" README.md
      3. grep -i "docker" README.md
      4. grep -i "AGENTS.md" README.md
    Expected Result: All sections present
    Failure Indicators: File missing or sections missing
    Evidence: grep outputs
  ```

  **Commit**: Groups with Task 11

---

- [x] 11. Final Verification and Documentation Commit

  **What to do**:
  - Run complete verification of all changes
  - Validate Docker configuration works
  - Commit documentation files
  - Summary of completed work

  **Must NOT do**:
  - Don't start Docker containers (just validate config)
  - Don't push to remote

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Verification and final commit
  - **Skills**: `["git-master"]`
    - git-master: For proper commit

  **Parallelization**:
  - **Can Run In Parallel**: NO
  - **Parallel Group**: Wave 4 (final)
  - **Blocks**: None
  - **Blocked By**: Tasks 9, 10

  **References**:
  - All previous tasks' acceptance criteria

  **Acceptance Criteria**:

  **Agent-Executed QA Scenarios:**

  ```
  Scenario: Docker configuration valid
    Tool: Bash
    Preconditions: All structural changes complete
    Steps:
      1. docker compose config > /dev/null
      2. echo "Exit code: $?"
    Expected Result: Exit code 0
    Failure Indicators: Non-zero exit code
    Evidence: Exit code output

  Scenario: No old folder names remain
    Tool: Bash
    Preconditions: All renames complete
    Steps:
      1. ! ls -d absensi-face 2>/dev/null && echo "absensi-face gone"
      2. ! ls -d face_service_python 2>/dev/null && echo "face_service_python gone"
    Expected Result: Both old folders don't exist
    Failure Indicators: Old folders still present
    Evidence: Echo outputs

  Scenario: No hardcoded URLs remain
    Tool: Bash
    Preconditions: AttendanceController fixed
    Steps:
      1. grep -r "127.0.0.1:5000" laravel-app/app/ 2>/dev/null
      2. echo "Exit code: $?" (should be 1)
    Expected Result: No matches found
    Failure Indicators: Hardcoded URLs still present
    Evidence: Empty grep result

  Scenario: Git has clean state after doc commit
    Tool: Bash
    Preconditions: AGENTS.md and README.md created
    Steps:
      1. git add AGENTS.md README.md
      2. git commit -m "docs: add AGENTS.md and README.md"
      3. git status
    Expected Result: "nothing to commit, working tree clean"
    Failure Indicators: Uncommitted changes remain
    Evidence: git status output

  Scenario: Final git log shows expected commits
    Tool: Bash
    Preconditions: All commits made
    Steps:
      1. git log --oneline
    Expected Result: At least 2 commits (initial + docs)
    Failure Indicators: Missing commits
    Evidence: git log output
  ```

  **Commit**: YES
  - Message: `docs: add AGENTS.md and README.md`
  - Files: AGENTS.md, README.md

---

## Commit Strategy

| After Task | Message | Files | Verification |
|------------|---------|-------|--------------|
| 8 | `chore: initial commit with restructured project` | All project files | `git log --oneline` |
| 11 | `docs: add AGENTS.md and README.md` | AGENTS.md, README.md | `git status` clean |

---

## Success Criteria

### Verification Commands
```bash
# Structure verification
ls laravel-app/artisan                    # Expected: file exists
ls face-service/face_service.py           # Expected: file exists
ls face-service/scripts/step1_detect_crop.py  # Expected: file exists

# Docker verification
docker compose config > /dev/null && echo "OK"  # Expected: OK

# Code fix verification
grep -r "127.0.0.1:5000" laravel-app/app/ && echo "FAIL" || echo "OK"  # Expected: OK

# Git verification
git log --oneline                         # Expected: 2 commits
git status                                # Expected: clean working tree

# Documentation verification
test -f AGENTS.md && echo "OK"           # Expected: OK
test -f README.md && echo "OK"           # Expected: OK
test -f .gitignore && echo "OK"          # Expected: OK
```

### Final Checklist
- [x] All folders renamed to kebab-case
- [x] Docker compose config validates successfully
- [x] No hardcoded URLs in AttendanceController
- [x] .gitignore excludes vendor, node_modules, .venv, .env
- [x] Git initialized with 2 commits
- [x] AGENTS.md documents architecture
- [x] README.md provides quick start
- [x] Training scripts in face-service/scripts/
- [x] All data (dataset/, embeddings/, *.pt) preserved

---

## Known Limitations (Post-Restructure)

1. **step*.py scripts will not work from new location** - They use relative paths (`../embeddings`, `./yolov8n.pt`). To run them, user must either:
   - Run from face-service/ root: `python scripts/step1_detect_crop.py`
   - Or update paths in scripts (not in scope)

2. **test_camera.html requires proxy or CORS** - Even with window.location.origin, if Flask runs on different port than Laravel, CORS must be configured.
