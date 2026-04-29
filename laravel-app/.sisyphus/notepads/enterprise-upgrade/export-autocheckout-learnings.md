# Phase 3D Learnings: Export & Auto-Checkout

## PDF Export (3D.1)

- `barryvdh/laravel-dompdf` is NOT in composer.json — must be installed before PDF export works: `composer require barryvdh/laravel-dompdf`
- DomPDF does not support Tailwind CSS — all PDF templates must use inline CSS
- DomPDF works best with `DejaVu Sans` font family for UTF-8/Indonesian character support
- PDF is generated to `storage/app/exports/` then served via `response()->download()->deleteFileAfterSend()`
- Export tab was refactored from `<form>` to Alpine.js `x-data` so both CSV and PDF buttons share the same date/department filters
- The `exportAttendancePdf` method accepts `date_from`, `date_to`, `department_id`, `user_id` filters
- Summary statistics are computed via `buildAttendanceSummary()` which groups by user_id and calculates hadir/terlambat/alpha/izin/total_jam

## Auto-Checkout (3D.2)

- Scheduler uses fixed time `23:55` instead of dynamic DB value because `Schedule::command()->dailyAt()` is evaluated at boot time, not at runtime — the command itself reads `auto_checkout_time` from SystemSetting and only processes if current time >= that setting
- `HasAuditLog` trait resolves `auth()->id()` which returns null in console context — the trait handles this gracefully (try/catch returns null), but we use `withoutAuditLog()` + manual `AuditLog::create()` for cleaner audit entries with explicit `user_agent: 'AutoCheckout Console Command'`
- Idempotency: the query filters `whereNull('jam_keluar')` so running twice won't double-checkout
- Status field preserves existing status (e.g., `terlambat,auto_checkout`) by appending rather than replacing
- The `??` null coalescing operator cannot be used inside PHP double-quoted string interpolation `{$var ?? 'default'}` — must extract to a variable first

## Route/Controller Patterns

- Manual trigger route added to `manage_system_settings` middleware group (Direktur only)
- Uses `Artisan::call()` + `Artisan::output()` for AJAX-based manual trigger with JSON response
- The `authorizeTabAccess($request, 'kebijakan_absensi')` pattern is used for tab-level authorization
