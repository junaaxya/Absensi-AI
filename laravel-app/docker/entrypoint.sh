#!/bin/bash
set -e

# Ensure storage directory structure exists
mkdir -p /var/www/html/storage/app/public/profile
mkdir -p /var/www/html/storage/framework/{cache,sessions,views}
mkdir -p /var/www/html/storage/logs

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create public/storage symlink (re-create on every startup)
php artisan storage:link --force 2>/dev/null || true

# Start Apache
exec apache2-foreground
