FROM php:8.2-cli-bullseye

# install deps tambahan tapi mbstring & intl sudah ada
RUN apt-get update && apt-get install -y \
    git curl unzip zlib1g-dev libzip-dev libpng-dev libjpeg-dev libwebp-dev \
    libfreetype6-dev libicu-dev nodejs npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Composer + npm build
RUN composer install --no-dev --optimize-autoloader \
    && npm install --no-audit --no-fund \
    && npm run build

# Laravel cache & storage
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link || true \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
