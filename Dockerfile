# Base image PHP official
FROM php:8.2-cli

# 1. System dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip pkg-config libicu-dev libzip-dev zlib1g-dev \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libonig-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. PHP extensions
RUN docker-php-ext-install intl zip pdo_mysql bcmath pcntl gd mbstring opcache

# 3. Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Workdir & copy source
WORKDIR /var/www
COPY . .

# 5. Composer & Node build
RUN composer install --no-dev --optimize-autoloader \
    && rm -rf node_modules package-lock.json \
    && npm install --no-audit --no-fund \
    && npm run build

# 6. Laravel cache & storage
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link || true \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080
