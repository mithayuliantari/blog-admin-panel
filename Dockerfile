# 1. Base image Laravel + PHP
FROM php:8.2-cli

# 2. Install PHP extensions & dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    zip \
    libicu-dev \
    pkg-config \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl zip pdo_mysql bcmath exif gd opcache pcntl

# 3. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Set workdir
WORKDIR /var/www

# 5. Copy project files
COPY . .

# 6. Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# 7. Install Node.js & npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 8. Install JS deps & build Vite (Filament asset)
RUN rm -rf node_modules package-lock.json \
    && npm install --no-audit --no-fund \
    && npm run build

# 9. Laravel cache optimizations
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# 10. Permission
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 11. Expose port 8080 (biar sesuai Railway public networking)
EXPOSE 8080

# 12. Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=8080
