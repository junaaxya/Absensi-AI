# Form Builder (Phase 4.5) — Learnings

## Files Created
- `database/migrations/2026_05_05_000003_create_form_builder_tables.php` — 3 tables: form_templates, form_submissions, form_submission_comments
- `app/Models/FormTemplate.php` — HasFactory, HasAuditLog, SoftDeletes. Casts: fields→array, approval_roles→array
- `app/Models/FormSubmission.php` — HasFactory, HasAuditLog. Casts: data→array, attachments→array, approved_at→datetime
- `app/Models/FormSubmissionComment.php` — HasFactory, HasAuditLog
- `app/Http/Controllers/FormController.php` — User-facing: index, show, store, mySubmissions, showSubmission, addComment
- `app/Http/Controllers/AdminFormController.php` — Admin: index, create, store, edit, update, destroy, submissions, showSubmission, approve, reject, addComment, export (CSV)
- `resources/views/forms/index.blade.php` — User form listing grid
- `resources/views/forms/show.blade.php` — Dynamic form renderer (loops template.fields, renders appropriate input type)
- `resources/views/forms/submissions.blade.php` — User's own submissions list
- `resources/views/forms/submission-show.blade.php` — Submission detail + comments
- `resources/views/admin/forms/index.blade.php` — Admin template list with submission counts
- `resources/views/admin/forms/create.blade.php` — Alpine.js form builder (click-to-add + move up/down)
- `resources/views/admin/forms/edit.blade.php` — Same builder, pre-populated with existing fields
- `resources/views/admin/forms/submissions.blade.php` — Submissions table with status filter + approve/reject
- `resources/views/admin/forms/submission-show.blade.php` — Submission detail with action buttons + comments

## Files Modified
- `routes/web.php` — Added FormController + AdminFormController imports, 6 user routes, 13 admin routes under manage_forms permission
- `database/seeders/RoleAndPermissionSeeder.php` — Added manage_forms permission, assigned to Direktur (via array_diff), VP (via array_diff), Manager (explicit)
- `resources/views/layouts/admin.blade.php` — Added "Form Internal" sidebar item with dynamic_form icon, gated by @can('manage_forms')
- `resources/views/layouts/absensi.blade.php` — Added "Form" link in user desktop sidebar

## Patterns Confirmed
- User views extend `layouts.absensi` (NOT `layouts.app` as originally specified in task — absensi is the actual user layout)
- Admin views extend `layouts.admin` with @section('header-title') and @section('header-subtitle')
- Alpine.js form builder uses click-to-add + move up/down buttons (no external drag-drop library)
- Fields JSON stored as hidden input, serialized on submit
- File uploads stored in `form-uploads` directory on public disk
- CSV export uses StreamedResponse for memory efficiency

## Docker Note
- Container `absensi_laravel` was not running during implementation — migration and seeder need to be run when container starts:
  - `docker exec absensi_laravel php artisan migrate --force`
  - `docker exec absensi_laravel php artisan db:seed --class=RoleAndPermissionSeeder --force`
