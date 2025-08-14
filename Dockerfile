# ---------- Stage 1: Build (composer + vite build) ----------
FROM php:8.2-fpm AS build

# 1. System deps untuk ekstensi PHP & Node
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev zip unzip git curl \
    libonig-dev libxml2-dev libicu-dev \
    nodejs npm \
 && docker-php-ext-configure intl \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# 2. Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# 3. Copy project
WORKDIR /var/www
COPY . .

# 4. Install PHP deps (tanpa dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 5. Install JS deps & build Vite (Filament asset)
RUN npm ci || npm install \
 && npm run build

# 6. Permission storage & cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# ---------- Stage 2: Production (ringan) ----------
FROM php:8.2-fpm

# 7. System deps runtime & ekstensi PHP (sama seperti di build)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev zip unzip git curl \
    libonig-dev libxml2-dev libicu-dev \
 && docker-php-ext-configure intl \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# 8. Composer (opsional, berguna untuk artisan command di runtime)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# 9. Copy hasil build dari stage 1
WORKDIR /var/www
COPY --from=build /var/www /var/www

# 10. Permission
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

# 11. Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
