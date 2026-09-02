#!/bin/sh
set -e

echo "=== UEW Digital Library — Deployment Start ==="

# 1. Ensure valid base64 encryption key is active
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "null" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    echo "Notice: APP_KEY not provided as base64 key. Setting production base64 key..."
    export APP_KEY="base64:6MsNsV445Nu736a1nFYqt1cNboYaJQJZk/nv7FTp9K4="
fi

# 2. Wait for database connectivity if external DB is configured
if [ -n "$DB_HOST" ] && [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "Checking database connectivity to $DB_HOST:${DB_PORT:-5432}..."
    MAX_RETRIES=20
    COUNT=0
    until php -r "try { new PDO('pgsql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:'5432').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_TIMEOUT => 2]); exit(0); } catch (Exception \$e) { exit(1); }" > /dev/null 2>&1 || [ $COUNT -eq $MAX_RETRIES ]; do
        echo "Waiting for database to be ready ($COUNT/$MAX_RETRIES)..."
        sleep 2
        COUNT=$((COUNT + 1))
    done
    echo "Database connectivity confirmed."
fi

# 3. Cache configuration, routes, and views for optimal performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4. Run database migrations safely
echo "Running database migrations..."
php artisan migrate --force || true

# 5. Seed default data if needed
php artisan db:seed --force || true

# 6. Create storage symlink
php artisan storage:link || true

echo "=== Starting Supervisor (Nginx + PHP-FPM) ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
