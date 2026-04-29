# Phase 1D: Anti-Fake GPS & Liveness Detection — Learnings

## Patterns & Conventions

- Settings tabs require registration in 4 places: `$tabLabels` (index.blade.php), `categoryTabs()` (AuthorizesSettingsTabs.php), `roleAllowedTabs()` (AuthorizesSettingsTabs.php), and category partial include (e.g., kehadiran.blade.php)
- Controllers use flat namespace `App\Http\Controllers` — NOT Admin sub-namespace
- All models MUST use `HasAuditLog` trait
- Sidebar items follow pattern: `@can('permission')` → `<a>` with Material Icons Round, active state via `request()->routeIs()`
- Settings update methods follow pattern: `authorizeTabAccess()` → `validate()` → `SystemSetting::first()->update()` → `back()->with('success', ...)`
- Attendance model uses `updateOrCreate` for masuk, `update` for pulang — anti-cheat data merged via `array_merge()`

## Architecture Decisions

- AntiCheatService is a plain class (not a Laravel service provider) — instantiated directly with data array, User, and SystemSetting
- AntiCheatResult is a simple DTO class (not a Laravel resource) — public properties for score, flags, passed, warning
- GPS readings collected as 3 sequential readings with 1-second intervals on frontend, averaged for main lat/long
- Device fingerprint is a simple hash of userAgent + screen + timezone + language — not cryptographically secure but sufficient for device tracking
- Liveness detection uses 3 weighted checks: LBP texture (25%), YCrCb color distribution (15%), Laplacian sharpness (60%)
- Face identity verification added: `$recognizedName !== $authUser->username` prevents user A using user B's face

## Key Files Created/Modified

### New Files
- `database/migrations/2026_05_01_000001_create_employee_devices_and_anticheat_columns.php`
- `database/migrations/2026_05_01_000002_add_anti_cheat_settings_to_system_settings.php`
- `app/Models/EmployeeDevice.php`
- `app/DTOs/AntiCheatResult.php`
- `app/Services/AntiCheatService.php`
- `app/Http/Controllers/AdminAnomalyController.php`
- `resources/views/admin/anomaly/index.blade.php`
- `resources/views/admin/settings/partials/tabs/anti_cheat.blade.php`

### Modified Files
- `app/Models/Attendance.php` — added 6 new fillable fields + casts
- `app/Models/User.php` — added `devices()` hasMany relation
- `app/Models/SystemSetting.php` — added 4 new fillable fields + casts
- `app/Http/Controllers/Api/AttendanceController.php` — anti-cheat integration + face identity verification
- `resources/views/dashboard.blade.php` — multi-GPS collection, device fingerprint, mock detection
- `face-service/face_service.py` — liveness detection + /health endpoint
- `routes/web.php` — anomaly routes + anti-cheat settings route
- `database/seeders/RoleAndPermissionSeeder.php` — view_anomaly_attendance permission
- `resources/views/layouts/admin.blade.php` — Anomali GPS sidebar item
- `resources/views/admin/settings/index.blade.php` — anti_cheat tab label
- `app/Http/Controllers/Concerns/AuthorizesSettingsTabs.php` — anti_cheat tab registration
- `resources/views/admin/settings/partials/kehadiran.blade.php` — anti_cheat tab include
- `app/Http/Controllers/AdminSystemSettingController.php` — updateAntiCheat method
