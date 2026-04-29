# Recruitment Pipeline - Learnings

## Phase 4.2 Implementation

### Files Created
- **Migration**: `database/migrations/2026_05_04_000002_create_recruitment_tables.php` — 5 tables (job_positions, candidates, interviews, onboarding_tasks, candidate_onboarding)
- **Models**: `JobPosition`, `Candidate`, `Interview`, `OnboardingTask`, `CandidateOnboarding` — all with HasAuditLog trait
- **Controller**: `AdminRecruitmentController` — 12 methods covering full CRUD + pipeline management
- **Views**: 5 Blade views in `resources/views/admin/recruitment/`
- **Routes**: 14 routes under `permission:manage_recruitment` middleware
- **Sidebar**: Added "Rekrutmen" with `person_search` icon

### Patterns & Conventions
- CandidateOnboarding model needs explicit `$table = 'candidate_onboarding'` since Laravel would pluralize to `candidate_onboardings`
- Pipeline drag-drop uses Alpine.js `x-data` with HTML5 Drag API + fetch PATCH to update status via AJAX
- CSRF token for AJAX calls: added `<meta name="csrf-token">` to admin layout head
- Resume upload stored to `storage/app/resumes/` via `$request->file('resume')->store('resumes', 'local')`
- Pastel status colors: Applied=stone, Screening=sky, Interview=lavender, Assessment=peach, Offered=green-100, Hired=primary, Rejected=pastel-rose

### Permission Setup
- Added `manage_recruitment` to permissions array in RoleAndPermissionSeeder
- Assigned to Direktur (via array_diff), Vice President (via array_diff), and Manager (explicit list)
- Seeder must be re-run: `php artisan db:seed --class=RoleAndPermissionSeeder`

### Key Decisions
- Used `belongsToMany` with pivot for candidate_onboarding (not just hasMany) to leverage `withPivot` and `withTimestamps`
- Interview timeline uses vertical layout with colored dots matching status
- Position detail page uses horizontal Kanban columns for candidate pipeline
- Create/Edit position share the same view (`positions-create.blade.php`) with `isset($jobPosition)` check
