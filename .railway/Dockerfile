# Stage 1: Build frontend assets
FROM node:20 AS frontend-build

WORKDIR /app

# Copy package.json & lock file
COPY package*.json ./

# Install dependencies sesuai lock file
RUN npm ci

# Copy semua file frontend (Vite/Tailwind)
COPY . .

# Build CSS & JS
RUN npm run build

# Stage 2: PHP / Laravel
FROM php:8.2-fpm

WORKDIR /var/www

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Copy Laravel project
COPY . .

# Copy hasil build frontend dari stage 1
COPY --from=frontend-build /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Expose port & entrypoint
EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
