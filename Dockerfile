# 1. Base image PHP (Debian)
FROM php:8.2-cli

# 2. System deps untuk ekstensi PHP & build tools
RUN apt-get update && apt-get install -y \
    git curl unzip pkg-config \
    libzip-dev \
    libicu-dev \
    zlib1g-dev \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
       pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# 3. Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Workdir & copy source
WORKDIR /var/www
COPY . .

# 5. Install dependency PHP (production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 6. Install Node.js 20 (untuk build Vite/Filament)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 7. Build asset Vite (fix EBADPLATFORM)
#    - buang lockfile Windows & node_modules jika ikut ter-copy
#    - install di Linux, lalu build
RUN rm -rf node_modules package-lock.json \
    && npm install --no-audit --no-fund \
    && npm run build

# 8. Optimisasi cache Laravel + storage link (tidak gagal bila sudah ada)
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link || true

# 9. Permission untuk storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache

# 10. Expose port 8080 (Railway public networking)
EXPOSE 8080

# 11. Start Laravel sesuai PORT dari Railway (fallback ke 8080)
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
