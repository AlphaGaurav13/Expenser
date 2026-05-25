#!/usr/bin/env bash
echo "Running migrations..."
php artisan migrate --force

echo "Caching config and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
