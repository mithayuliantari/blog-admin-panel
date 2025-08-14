# Base PHP CLI
FROM php:8.2-cli

WORKDIR /var/www

# Install system dependencies + Node
RUN apt-get update && apt-get install -y \
    git curl unzip pkg-config libicu-dev libzip-dev zlib1g-dev \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libonig-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install intl zip pdo_mysql bcmath pcntl gd mbstring opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Hapus node_modules & lock Windows, install Node deps & build Vite
RUN rm -rf node_modules package-lock.json \
    && npm install --ignore-scripts --ignore-platform --legacy-peer-deps --unsafe-perm \
    && npm run build

# Laravel cache & storage
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link || true \
 && chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080} --public
