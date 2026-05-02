#!/bin/sh
set -e

export COMPOSER_MEMORY_LIMIT=512M

# Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL..."
for i in $(seq 1 30); do
    if php -r "new PDO('pgsql:host=${DB_HOST:-postgres};port=${DB_PORT:-5432};dbname=postgres','${DB_USERNAME:-culinary_user}','${DB_PASSWORD:-}');" 2>/dev/null; then
        echo "PostgreSQL is ready!"
        break
    fi
    if [ "$i" -eq 30 ]; then
        echo "ERROR: PostgreSQL not ready after 30 seconds"
        exit 1
    fi
    sleep 1
done

if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
fi

php artisan key:generate --no-interaction 2>/dev/null || true
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction
php artisan passport:keys --no-interaction 2>/dev/null || true

exec php artisan serve --host=0.0.0.0 --port=8000