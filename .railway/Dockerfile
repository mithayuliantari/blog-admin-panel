FROM php:8.2-cli

# Install PHP dependencies yang sering error di Railway
RUN apt-get update && apt-get install -y \
    unzip git libicu-dev libpng-dev libjpeg-dev libwebp-dev libonig-dev \
    && docker-php-ext-install intl gd mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project
WORKDIR /var/www
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader \
    && npm install && npm run build \
    && php artisan config:cache && php artisan route:cache && php artisan view:cache

EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080
