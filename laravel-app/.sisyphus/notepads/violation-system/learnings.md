# Violation System Learnings

## Codebase Patterns
- Controllers use flat namespace `App\Http\Controllers` (NOT Admin sub-namespace)
- All models use `HasAuditLog` trait from `App\Traits\HasAuditLog`
- Settings system uses `AuthorizesSettingsTabs` trait with category→tabs mapping
- Categories: umum, kehadiran, organisasi, data
- Tab access controlled by `roleAllowedTabs()` mapping permissions to tab names
- Settings tab partials live in `resources/views/admin/settings/partials/tabs/`
- Category partials include tab partials conditionally via `in_array()` checks
- Sidebar items use Material Icons Round with `filled-icon` class for active state
- Sidebar items wrapped in `@can('permission_name')` directives

## Settings Tab Registration (3 places)
1. `$tabLabels` array in `settings/index.blade.php` — display name mapping
2. `categoryTabs()` in `AuthorizesSettingsTabs.php` — which category contains the tab
3. `roleAllowedTabs()` in `AuthorizesSettingsTabs.php` — which permission grants access
4. Category partial (e.g., `kehadiran.blade.php`) — include the tab partial

## Attendance Flow
- Api\AttendanceController handles check-in at line ~230 (updateOrCreate)
- Status determined as 'terlambat' or 'tepat_waktu' at line ~215
- Late notification check at line ~242 (after logging)
- Violation hook inserted between logging and notification

## Intelephense False Positives
- P1009 (Undefined type) — vendor classes not indexed
- P1010 (Undefined function) — Laravel helpers (route, view, auth, etc.)
- P1013 (Undefined method) — Eloquent magic methods
- P1014 (Undefined property) — Model attributes via $fillable
- All are known false positives, zero real errors

## Key Decisions
- Violation deduction settings support both per_point and percentage modes
- SP thresholds are configurable (default: SP1=10, SP2=20, SP3=30)
- Alpha violations generated via scheduled command at 23:00 daily
- Manual violations support editable points (for MANUAL type with 0 default)
- Dashboard violation card uses pastel badge system: sage=clean, peach=attention, rose=warning
