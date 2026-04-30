#!/bin/bash
set -e

fix_permissions() {
    echo "🔧 Fixing permissions recursively..."
    find /var/www/html/storage -type d -exec chmod 775 {} \; 2>/dev/null || true
    find /var/www/html/storage -type f -exec chmod 664 {} \; 2>/dev/null || true
    chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
    
    find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
    find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
    chown -R www-data:www-data /var/www/html/bootstrap/cache 2>/dev/null || true
    echo "✅ Permissions fixed"
}

fix_permissions

echo "🔗 Ensuring storage symlink..."
php artisan storage:link --force 2>/dev/null || true
echo "✅ Storage symlink ready"

echo "📦 Checking Vite build..."
if [ ! -f /var/www/html/public/build/manifest.json ]; then
    echo "⚡ Building Vite assets..."
    npm run build
    echo "✅ Vite build complete"
else
    echo "✅ Vite manifest found"
fi

echo "🔄 Starting background permission fixer..."
(
    while true; do
        sleep 300
        fix_permissions > /dev/null 2>&1
    done
) &

echo "🚀 Starting Apache..."
exec apache2-foreground
