# Gunakan image PHP resmi
FROM php:8.2-fpm

# Install extensions yang dibutuhkan Laravel + Filament
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git curl libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy project ke container
WORKDIR /var/www
COPY . .

# Install dependencies Laravel
RUN composer install --optimize-autoloader --no-dev

# Set permission
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Jalankan Laravel di port 8000
EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
