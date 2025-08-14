# -----------------------------
# Stage 1: Node build
# -----------------------------
FROM node:20-slim AS node-builder

WORKDIR /app

# Copy package.json & package-lock.json baru (jangan ikut lockfile Windows lama)
COPY package.json package-lock.json* ./

# Install node deps di Linux (paksa platform)
RUN npm install --ignore-scripts --ignore-platform --legacy-peer-deps --unsafe-perm

# Copy semua source code
COPY . .

# Build Vite / LightningCSS
RUN npm run build

# -----------------------------
# Stage 2: PHP / Laravel
# -----------------------------
FROM php:8.2-cli-bullseye

WORKDIR /var/www

# Install PHP dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip pkg-config libicu-dev libzip-dev zlib1g-dev \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-install intl zip pdo_mysql bcmath pcntl gd mbstring opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel source (selalu termasuk folder public)
COPY . .

# Copy hasil build Vite dari stage Node ke public/build
COPY --from=node-builder /app/dist ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel cache & storage
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link || true \
 && chown -R www-data:www-data storage bootstrap/cache

# Expose port & jalankan Laravel di public
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080} --public
