#!/bin/sh
set -e

echo "=== UEW Digital Library — Deployment Start ==="

# Cache config, routes, and views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run pending migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link || true

echo "=== Starting Supervisor (Nginx + PHP-FPM) ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
