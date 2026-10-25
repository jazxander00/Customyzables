#!/bin/sh
set -e

cd /var/www/html

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is empty. Generate one locally with:"
    echo "  php artisan key:generate --show"
    echo "then add it as an environment variable on the host."
    exit 1
fi

echo "→ Preparing storage directories"
mkdir -p storage/app/public \
         storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache \
         storage/logs \
         bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# The symlink is created at build time, but a mounted volume can hide it.
if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

echo "→ Running migrations (retrying while the database starts)"
attempts=0
until php artisan migrate --force; do
    attempts=$((attempts + 1))
    if [ "$attempts" -ge 10 ]; then
        echo "ERROR: database still unreachable after 10 attempts. Check DB_HOST/DB_PORT/DB_USERNAME/DB_PASSWORD."
        exit 1
    fi
    echo "   database not ready, retrying in 3s ($attempts/10)"
    sleep 3
done

if [ "$SEED_DATABASE" = "true" ]; then
    echo "→ Seeding demo data"
    php artisan db:seed --force
fi

echo "→ Caching config, routes and views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
