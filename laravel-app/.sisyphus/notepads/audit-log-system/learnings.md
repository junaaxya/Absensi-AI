# Audit Log System - Learnings

## Project Conventions
- Controllers are flat in `app/Http/Controllers/` (not in Admin subfolder) — e.g. `AdminAnnouncementController`
- Models use `HasFactory` trait; some use `$fillable`, some use `$guarded`
- Existing models use both `protected $casts = [...]` array style AND `protected function casts(): array` method style
- Views use Indonesian language for UI text (e.g. "Belum ada aktivitas", "Lihat Detail")
- Blade views use `@extends('layouts.admin')` with `@section('header-title')` and `@section('content')`
- Tailwind CSS with pastel theme, dark mode support, Alpine.js for interactivity

## LSP Environment
- Intelephense shows P1009/P1010/P1013/P1014 errors for ALL Laravel vendor types because vendor/ is inside Docker container, not on host
- These are false positives — all standard Laravel patterns (Model, Builder, now(), auth(), request()) trigger them
- Pre-existing across all models in the project

## Architecture Decisions
- `HasAuditLog` trait uses `bootHasAuditLog()` for automatic model event hooks
- Sensitive fields excluded: password, remember_token, no_rekening, npwp
- Console/queue context handled via try/catch on request() and auth() calls
- `$auditingDisabled` static flag with `withoutAuditLog()` for bulk operations
- AuditLog has no `updated_at` — immutable records, only `created_at`
- Event names use past tense: created, updated, deleted (matching Laravel model events)
