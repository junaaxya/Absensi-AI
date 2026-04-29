# Visit Attendance (Phase 3A) — Learnings

## Architecture Decisions

1. **No geofencing for visits**: Unlike regular attendance which validates office radius, visit attendance skips geofencing since employees visit external client locations. Anti-cheat validation is still applied for GPS anomaly detection.

2. **Reused AntiCheatService**: The same `AntiCheatService` from regular attendance is reused for visit check-in. This validates GPS variance, speed anomaly, accuracy paradox, device consistency, timezone mismatch, and mock location detection.

3. **Face verification flow**: Identical to regular attendance — photo sent to Flask `/recognize_frame`, recognized name must match `auth()->user()->username`. Applied on both check-in and check-out.

4. **Background GPS tracking**: Client-side `setInterval` every 5 minutes POSTs to `/api/visit/{id}/track` while a visit is active. Creates `VisitLocation` records for GPS trail visualization.

5. **Photo storage**: Visit photos stored in `storage/app/public/visit-photos/` via `$photoFile->store('visit-photos', 'public')`.

## File Inventory

### New Files
- `database/migrations/2026_05_03_000001_create_visit_attendance_tables.php`
- `app/Models/VisitAttendance.php`
- `app/Models/VisitLocation.php`
- `app/Http/Controllers/Api/VisitAttendanceController.php`
- `app/Http/Controllers/AdminVisitAttendanceController.php`
- `resources/views/admin/visits/index.blade.php`
- `resources/views/admin/visits/show.blade.php`

### Modified Files
- `app/Models/User.php` — added `visitAttendances()` relationship
- `app/Http/Controllers/AttendanceController.php` — added `$activeVisit` and `$visitHistory` to dashboard data
- `routes/api.php` — added 3 visit API routes + import
- `routes/web.php` — added 2 admin visit routes + import
- `database/seeders/RoleAndPermissionSeeder.php` — added `view_visit_attendance` permission to Direktur, VP, Manager, Supervisor
- `resources/views/layouts/admin.blade.php` — added "Kunjungan" sidebar item
- `resources/views/dashboard.blade.php` — added visit button, active visit section, visit history table, check-in/check-out modals, Alpine.js handlers, GPS tracker

## Key Patterns Followed
- Controllers use FLAT namespace `App\Http\Controllers` (admin) and `App\Http\Controllers\Api` (API)
- All models use `HasAuditLog` trait
- Admin views extend `layouts.admin`
- Pastel badge colors: sky (active), emerald/sage (completed), rose (cancelled)
- Alpine.js data components registered inside `alpine:init` event listener
- API routes under `Route::middleware(['web', 'auth'])` group (session-based auth)
- Permission-based route groups with `permission:` middleware
- Sidebar items wrapped in `@can('permission_name')`

## Leaflet.js Integration
- Used Leaflet CDN v1.9.4 for GPS trail map on admin visit detail page
- Green marker for check-in, red marker for check-out
- Blue dashed polyline for GPS trail from `visit_locations`
- Map auto-fits bounds to show all markers and trail points
