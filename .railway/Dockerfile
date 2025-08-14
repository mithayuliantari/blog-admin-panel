FROM php:8.2-cli

# 1. Install PHP deps + Oniguruma + build tools + Node.js
RUN apt-get update && apt-get install -y \
    unzip git libicu-dev libpng-dev libjpeg-dev libwebp-dev libonig-dev \
    zlib1g-dev pkg-config curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install intl gd mbstring pdo_mysql zip bcmath pcntl opcache

# 2. Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3. Set workdir & copy project
WORKDIR /var/www
COPY . .

# 4. Install PHP deps + Node deps + Build
RUN composer install --no-dev --optimize-autoloader \
    && rm -rf node_modules package-lock.json \
    && npm install --no-audit --no-fund \
    && npm run build

# 5. Laravel cache & storage
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link || true \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
