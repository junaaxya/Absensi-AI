#!/bin/bash
set -e

echo "🔧 Fixing permissions..."
# Fix permissions on mounted volumes
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "📦 Checking Vite build..."
# Build assets if manifest is missing
if [ ! -f /var/www/html/public/build/manifest.json ]; then
    echo "⚡ Building Vite assets..."
    npm run build
    echo "✅ Vite build complete"
else
    echo "✅ Vite manifest found"
fi

echo "🚀 Starting Apache..."
# Execute the main container command
exec apache2-foreground
