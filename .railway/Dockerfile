# -----------------------------
# Stage 1: Node build
# -----------------------------
FROM node:20 AS node-builder

WORKDIR /app

# Copy package.json & package-lock.json jika ada
COPY package*.json ./

# Install Node dependencies di Linux (paksa platform)
RUN npm install --ignore-scripts --ignore-platform --legacy-peer-deps

# Copy semua source code
COPY . .

# Build Vite
RUN npm run build

# -----------------------------
# Stage 2: PHP / Laravel
# -----------------------------
FROM php:8.2-cli

WORKDIR /var/www

# Install PHP system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip pkg-config libicu-dev libzip-dev zlib1g-dev \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-install intl zip pdo_mysql bcmath pcntl gd mbstring opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel source
COPY . .

# Copy hasil build Vite dari stage Node
COPY --from=node-builder /app/dist ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel cache & storage
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link || true \
 && chown -R www-data:www-data storage bootstrap/cache

# Expose port & start Laravel
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
