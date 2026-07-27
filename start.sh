#!/bin/bash
set -e

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Running database seeders..."
php artisan db:seed --force

echo "==> Clearing and caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
