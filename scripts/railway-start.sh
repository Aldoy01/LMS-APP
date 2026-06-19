#!/usr/bin/env bash
set -e

echo "Starting Techverse Learning LMS on port ${PORT:-8080}"

export PHPRC="${PHPRC:-$(pwd)/php.ini}"

echo "PHP upload limits: upload_max_filesize=$(php -r 'echo ini_get("upload_max_filesize");'), post_max_size=$(php -r 'echo ini_get("post_max_size");')"

# Railway Volume mounted at /app/storage starts as an empty directory.
# Create Laravel runtime and material directories before Artisan writes to them.
mkdir -p \
    storage/app/public/site \
    storage/app/public/avatars \
    storage/app/materials/videos \
    storage/app/materials/files \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

if [ -n "${DATABASE_URL:-}" ] && [ -z "${DB_CONNECTION:-}" ]; then
    export DB_CONNECTION=pgsql
fi

if [ -n "${PGHOST:-}" ]; then
    export DB_CONNECTION="${DB_CONNECTION:-pgsql}"
    export DB_HOST="${DB_HOST:-$PGHOST}"
    export DB_PORT="${DB_PORT:-${PGPORT:-5432}}"
    export DB_DATABASE="${DB_DATABASE:-${PGDATABASE:-}}"
    export DB_USERNAME="${DB_USERNAME:-${PGUSER:-}}"
    export DB_PASSWORD="${DB_PASSWORD:-${PGPASSWORD:-}}"
fi

if [ -n "${DB_HOST:-}" ] && [ -n "${DB_DATABASE:-}" ] && [ -n "${DB_USERNAME:-}" ]; then
    unset DATABASE_URL
fi

echo "Database check: DB_CONNECTION=${DB_CONNECTION:-empty}, DATABASE_URL=$([ -n "${DATABASE_URL:-}" ] && echo set || echo empty), DB_HOST=$([ -n "${DB_HOST:-}" ] && echo set || echo empty)"

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ "${DB_CONNECTION:-}" = "pgsql" ] && { [ -n "${DATABASE_URL:-}" ] || { [ -n "${DB_HOST:-}" ] && [ -n "${DB_DATABASE:-}" ] && [ -n "${DB_USERNAME:-}" ]; }; }; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration failed; continuing so the web server can start. Check Railway database variables."
    php artisan db:seed --force || echo "Seeder failed; continuing so the web server can start."
else
    echo "PostgreSQL variables are incomplete and DATABASE_URL is empty, or DB_CONNECTION is not pgsql; skipping migrations."
fi

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
