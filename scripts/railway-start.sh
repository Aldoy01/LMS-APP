#!/usr/bin/env bash
set -e

echo "Starting Techverse Learning LMS on port ${PORT:-8080}"

if [ -n "${DATABASE_URL:-}" ] && [ -z "${DB_CONNECTION:-}" ]; then
    export DB_CONNECTION=pgsql
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
