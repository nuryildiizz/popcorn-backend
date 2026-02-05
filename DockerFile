FROM php:8.2-cli

# sistem paketleri
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    && docker-php-ext-install zip

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# dosyalar
COPY . .

# bağımlılıklar
RUN composer install --no-dev --optimize-autoloader

# laravel optimize
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan route:clear || true

EXPOSE 8080

CMD php -S 0.0.0.0:8080 -t public
