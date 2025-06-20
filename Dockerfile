FROM php:8.2-fpm

# Cài extension cần thiết
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libpq-dev libcurl4-openssl-dev libssl-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

# Cài Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy source code
WORKDIR /var/www
COPY . .

# Cài đặt Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set quyền cho Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# EXPOSE port (Railway mặc định port 8080)
EXPOSE 8080

# Chạy Laravel với Artisan
CMD php artisan serve --host=0.0.0.0 --port=8080
