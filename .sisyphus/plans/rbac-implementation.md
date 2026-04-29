# RBAC Implementation Plan — Attendance Web Application

## Context
- **Codebase**: `/media/arjuna/DATA20/PROJECT-CODING/absensi/laravel-app/`
- **Stack**: Laravel 12, PHP 8.2+, Blade views, Breeze auth, Docker (absensi_laravel container)
- **Current State**: Binary role system (string column `role` = 'admin' | 'user'), single `AdminOnly` middleware
- **Target**: Full RBAC with 7 roles, granular permissions, and data scoping
- **ALL commands**: `docker exec absensi_laravel <command>` (code runs inside Docker)

## Roles & Permission Matrix

| Permission | Direktur | VP | Manager | Supervisor | Team Leader | Staf | Magang |
|---|---|---|---|---|---|---|---|
| view_all_attendance | ✓ | ✓ | | | | | |
| view_department_attendance | ✓ | ✓ | ✓ | | | | |
| view_team_attendance | ✓ | ✓ | ✓ | ✓ | ✓ | | |
| view_self_attendance | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| approve_department_izin | ✓ | ✓ | ✓ | | | | |
| approve_team_izin | ✓ | ✓ | ✓ | ✓ | ✓ | | |
| request_izin | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | |
| request_izin_limited | | | | | | | ✓ |
| manage_employees | ✓ | ✓ | | | | | |
| manage_departments | ✓ | ✓ | | | | | |
| manage_shifts | ✓ | ✓ | | | | | |
| manage_holidays | ✓ | ✓ | | | | | |
| manage_leave_types | ✓ | ✓ | | | | | |
| manage_announcements | ✓ | ✓ | ✓ | | | | |
| manage_system_settings | ✓ | | | | | | |
| view_audit_logs | ✓ | ✓ | | | | | |
| manage_backups | ✓ | | | | | | |
| export_data | ✓ | ✓ | ✓ | | | | |
| manage_face_data | ✓ | ✓ | | | | | |

---

## Phase 1: Package Installation & Base Setup

- [ ] **Task 1.1**: Install spatie/laravel-permission via Composer inside Docker container (`docker exec absensi_laravel composer require spatie/laravel-permission`). Publish the migration and config files (`docker exec absensi_laravel php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`). Run the migration (`docker exec absensi_laravel php artisan migrate`). Verify tables `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` are created.

- [ ] **Task 1.2**: Update `app/Models/User.php` to add the `HasRoles` trait from Spatie (`use Spatie\Permission\Traits\HasRoles;`). Add `HasRoles` to the class traits list (alongside existing `HasFactory`, `Notifiable`, `Auditable`). Verify no LSP errors.

## Phase 2: Roles, Permissions & Data Migration

- [ ] **Task 2.1**: Create `database/seeders/RoleAndPermissionSeeder.php` that defines all 7 roles (Direktur, Vice President, Manager, Supervisor, Team Leader, Staf, Magang) and all 19 permissions from the matrix above. Assign the correct permissions to each role per the matrix. Register this seeder in `DatabaseSeeder.php`. Run it via `docker exec absensi_laravel php artisan db:seed --class=RoleAndPermissionSeeder`. Verify roles and permissions exist in DB.

- [ ] **Task 2.2**: Create a data migration `database/migrations/xxxx_migrate_old_roles_to_spatie.php` that: (1) reads each user's current `role` string column, (2) assigns Spatie role — map 'admin' → 'Direktur', 'user' → 'Staf', null/empty → 'Staf', (3) does NOT drop the old `role` column yet (safety). Run the migration via Docker. Verify users have correct Spatie roles assigned.

## Phase 3: Middleware & Route Protection

- [ ] **Task 3.1**: Refactor `app/Http/Middleware/AdminOnly.php` to use Spatie role checks instead of string comparison. It should allow access if the user has ANY of the admin-level roles (Direktur, Vice President, Manager, Supervisor, Team Leader) — NOT just `role === 'admin'`. Clean up or replace the phantom `EnsureUserHasRole` import in `bootstrap/app.php`. Register Spatie's `RoleMiddleware` and `PermissionMiddleware` as aliases in `bootstrap/app.php`.

- [ ] **Task 3.2**: Update `routes/web.php` to use granular permission-based middleware on admin routes instead of the single `admin` middleware. Group routes by permission level: (1) system settings/backups → `permission:manage_system_settings`, (2) employee management → `permission:manage_employees`, (3) department/shift/holiday/leave-type CRUD → appropriate manage_* permissions, (4) attendance views → appropriate view_*_attendance permissions, (5) izin approval → appropriate approve_*_izin permissions, (6) announcements → `permission:manage_announcements`, (7) audit logs → `permission:view_audit_logs`, (8) exports → `permission:export_data`. Keep the outer `auth` middleware group intact.

## Phase 4: Data Scoping (Query-Level Access Control)

- [ ] **Task 4.1**: Create `app/Scopes/AttendanceScope.php` (or a trait/service) that provides a method `scopeForUser(User $user, Builder $query)` which filters attendance queries based on user role: Direktur/VP → no filter (all data), Manager → filter by `department_id`, Supervisor/Team Leader → filter by team (via a `team_id` or `supervisor_id` relationship — if no team table exists, scope by department_id for now), Staf/Magang → filter by `user_id` only. Apply this scope in `AdminAttendanceController`, `AdminAbsenceController`, and `AdminDashboardController`.

- [ ] **Task 4.2**: Create `app/Scopes/IzinScope.php` (or integrate into existing service) that provides similar scoping for Izin queries. Apply to `AdminDashboardController` (pending izin count) and the izin approval routes. Ensure Managers only see/approve izin from their department, Supervisors/Team Leaders from their team, and Staf/Magang can only see their own.

## Phase 5: Frontend / Dashboard UI Adaptation

- [ ] **Task 5.1**: Update `resources/views/layouts/admin.blade.php` sidebar/navigation to conditionally show menu items based on Spatie roles/permissions. Use `@can('permission_name')` or `@role('RoleName')` Blade directives. Sidebar sections: (1) Dashboard → visible to all admin roles, (2) Attendance/Absence → visible to roles with any view_*_attendance, (3) Izin Management → visible to roles with approve_*_izin, (4) Employee Management → `@can('manage_employees')`, (5) Settings (departments, shifts, holidays, leave types, system) → appropriate manage_* permissions, (6) Announcements → `@can('manage_announcements')`, (7) Audit Logs → `@can('view_audit_logs')`, (8) Backups → `@can('manage_backups')`, (9) Export → `@can('export_data')`.

- [ ] **Task 5.2**: Update `resources/views/admin/dashboard.blade.php` to show role-appropriate widgets and statistics. Direktur/VP see company-wide stats. Manager sees department-level stats. Supervisor/Team Leader sees team-level stats. The controller already computes these — just ensure the view uses the scoped data from Task 4.1. Also add a welcome message showing the user's current role.

## Phase 6: Cleanup & Hardening

- [ ] **Task 6.1**: Create a new migration `database/migrations/xxxx_drop_old_role_column_from_users.php` to remove the legacy string `role` column from the users table. Update User model `$fillable` to remove `role`. Search entire codebase for any remaining references to `$user->role` or `->where('role',` and replace with Spatie equivalents (`$user->hasRole()`, `$user->hasPermissionTo()`). Remove or update the `AdminOnly` middleware if it still references the old column.

- [ ] **Task 6.2**: Update `app/Http/Controllers/AdminDashboardController.php` to replace hardcoded `where('role', '!=', 'admin')` with Spatie-based queries (e.g., `User::role(['Staf', 'Magang', 'Team Leader', 'Supervisor', 'Manager'])->count()` or `User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Direktur'))->count()`). Review all 13 admin controllers for any other hardcoded role string references.

## Phase 7: Verification & Testing

- [ ] **Task 7.1**: Create a comprehensive test or verification script that: (1) Seeds a test user for each of the 7 roles, (2) Verifies each user has the correct permissions, (3) Verifies route access — Staf/Magang get 403 on admin routes, Manager can access department-scoped routes but not system settings, Direktur can access everything. This can be an artisan command or PHPUnit test. Run it and confirm all assertions pass.

---

## Execution Notes
- All artisan/composer commands: `docker exec absensi_laravel <command>`
- Code path: `/media/arjuna/DATA20/PROJECT-CODING/absensi/laravel-app/`
- Notepad path: `/media/arjuna/DATA20/PROJECT-CODING/absensi/.sisyphus/notepads/rbac-implementation/`
- Phases 1-2 are sequential (dependencies)
- Tasks within Phase 2 are sequential (2.1 must complete before 2.2)
- Phases 3-5 can partially parallelize after Phase 2 completes
- Phase 6 depends on Phases 3-5
- Phase 7 depends on Phase 6
