# 1. Base image
FROM php:8.2-cli

# 2. Install dependencies untuk PHP dan Node
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# 4. Install Node.js & npm (versi LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs

# 5. Copy project ke container
WORKDIR /var/www
COPY . .

# 6. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 7. Install JS deps & build Vite (Filament asset) - fix npm Linux issue
RUN rm -f package-lock.json \
    && npm install --platform=linux --arch=x64 \
    && npm run build

# 8. Permission
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 9. Expose port dari Railway (default 8080)
EXPOSE 8080

# 10. Start Laravel pakai port Railway
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
