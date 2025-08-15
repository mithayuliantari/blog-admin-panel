#!/bin/sh

# Tunggu sampai database siap
until php artisan migrate:status > /dev/null 2>&1; do
  echo "Waiting for database..."
  sleep 3
done

# Jalankan migrate & seed
php artisan migrate --force
php artisan db:seed --force

# Cache dan serve Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
