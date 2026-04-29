#!/bin/bash
# Run tests inside Docker container with proper env isolation
# Docker env vars override phpunit.xml, so we unset them first
docker exec absensi_laravel bash -c '
unset SESSION_DRIVER DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD \
     CACHE_STORE QUEUE_CONNECTION APP_ENV MAIL_MAILER FLASK_SERVICE_URL \
     APP_KEY APP_NAME APP_DEBUG APP_URL
php artisan test "$@"
' -- "$@"
