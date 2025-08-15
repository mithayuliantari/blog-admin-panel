#!/bin/bash

# Set permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Waiting for database connection..."

# Tunggu database siap dengan timeout
RETRIES=30
until php artisan migrate:status > /dev/null 2>&1 || [ $RETRIES -eq 0 ]; do
  echo "Database not ready, waiting... ($RETRIES attempts left)"
  RETRIES=$((RETRIES-1))
  sleep 5
done

if [ $RETRIES -eq 0 ]; then
  echo "Database connection failed after multiple attempts"
  exit 1
fi

echo "Database connected! Running migrations..."

# Jalankan migrasi
php artisan migrate --force || {
  echo "Migration failed"
  exit 1
}

# Seed jika ada
if [ -f "database/seeders/DatabaseSeeder.php" ]; then
  echo "Running seeders..."
  php artisan db:seed --force || echo "Seeding failed, continuing..."
fi

# Storage link
php artisan storage:link || echo "Storage link already exists"

# Optimizations
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache server..."

# Start Apache di foreground
exec apache2-foreground
