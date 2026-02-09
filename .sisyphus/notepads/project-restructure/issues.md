# Issues & Gotchas: Project Restructure

## Problems Encountered
[Will be populated by subagents during execution]


## [2026-02-09] Task 7: PHP Artisan Commands Failed
- **Issue**: 'php' binary not found on host and 'laravel-app/vendor/' directory is missing.
- **Consequence**: Could not run 'php artisan' commands as requested in the plan.
- **Workaround**: Manually cleared cache files by deleting:
    - laravel-app/storage/framework/views/*.php
    - laravel-app/bootstrap/cache/packages.php
    - laravel-app/bootstrap/cache/services.php
    - All files in laravel-app/storage/framework/cache/data/ (except .gitignore)
- **Status**: Caches are cleared, but via manual file deletion instead of artisan commands.
