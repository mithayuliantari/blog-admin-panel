FROM php:8.2-cli

# 1. Install system dependencies dulu
RUN apt-get update && apt-get install -y \
    git curl unzip pkg-config libzip-dev libicu-dev zlib1g-dev \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    libonig-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install PHP extensions (pakai docker-php-ext-install)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
       pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# 3. Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Workdir & copy project
WORKDIR /var/www
COPY . .

# 5. Install PHP & Node dependencies
RUN composer install --no-dev --optimize-autoloader \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
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
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
