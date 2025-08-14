# -----------------------------
# Stage 1: Build Vite assets
# -----------------------------
FROM node:20-slim AS node-builder

WORKDIR /app

# Copy package files dan install deps
COPY package.json package-lock.json* ./
RUN npm install --ignore-scripts --ignore-platform --legacy-peer-deps --unsafe-perm

# Copy seluruh source dan build
COPY . .
RUN npm run build

# -----------------------------
# Stage 2: Laravel app
# -----------------------------
FROM php:8.2-cli-bullseye

WORKDIR /var/www

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    git curl unzip pkg-config libicu-dev libzip-dev zlib1g-dev \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-install intl zip pdo_mysql bcmath pcntl gd mbstring opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel source
COPY . .

# Copy hasil build Vite
COPY --from=node-builder /app/dist ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permission storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Jalankan Laravel ketika container start (cache dibuat setelah env dari Railway dimuat)
CMD php artisan key:generate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan storage:link || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

EXPOSE 8080
