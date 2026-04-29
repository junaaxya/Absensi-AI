# Phase 4.1 — Project & Task Management Learnings

## Files Created/Modified

### New Files
- `database/migrations/2026_05_04_000001_create_project_management_tables.php` — 5 tables: projects, project_members, tasks, task_comments, time_entries
- `app/Models/Project.php` — HasFactory, HasAuditLog, SoftDeletes
- `app/Models/ProjectMember.php` — HasFactory, HasAuditLog
- `app/Models/Task.php` — HasFactory, HasAuditLog, SoftDeletes
- `app/Models/TaskComment.php` — HasFactory, HasAuditLog
- `app/Models/TimeEntry.php` — HasFactory, HasAuditLog
- `app/Http/Controllers/AdminProjectController.php` — Full CRUD + Kanban data
- `app/Http/Controllers/AdminTaskController.php` — CRUD + status update + comments + timer
- `resources/views/admin/projects/index.blade.php` — Card grid with filters
- `resources/views/admin/projects/create.blade.php` — Form with member selection
- `resources/views/admin/projects/edit.blade.php` — Edit form with member sync
- `resources/views/admin/projects/show.blade.php` — Kanban board with drag-drop

### Modified Files
- `app/Models/User.php` — Added projects(), assignedTasks(), timeEntries() relations
- `routes/web.php` — Added AdminProjectController + AdminTaskController imports and routes
- `database/seeders/RoleAndPermissionSeeder.php` — Added manage_projects permission
- `resources/views/layouts/admin.blade.php` — Added Project sidebar item after Payroll

## Patterns & Conventions
- Kanban drag-drop uses HTML5 Drag API with Alpine.js (no external libraries)
- Task status update via PATCH endpoint returns JSON for AJAX, then reloads page
- Project members managed via belongsToMany with pivot role (manager/member/viewer)
- Time tracking: startTimer creates entry with started_at=now, stopTimer calculates duration_minutes
- Active timer check prevents multiple concurrent timers per user
- Column colors: Backlog=stone, Todo=sky, In Progress=violet, Review=orange, Done=emerald

## Permission Assignment
- `manage_projects` given to: Direktur, VP (via array_diff), Manager, Supervisor, Team Leader
- Staf and Magang do NOT have project management access
