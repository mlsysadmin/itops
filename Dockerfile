FROM php:8.2-fpm

# Install system dependencies required by Laravel and Dompdf
RUN apt-get update && apt-get install -y \
    zip unzip git curl \
    libzip-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    libxrender1 libfontconfig1 libfreetype6 \
    libicu-dev libgd-dev \
    fonts-dejavu-core fonts-dejavu-extra \
    nodejs npm \
    && docker-php-ext-install \
    pdo pdo_mysql mbstring zip exif pcntl intl gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application source
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install and build frontend assets
RUN npm install && npm run build

# Set permissions for storage and bootstrap
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Expose port for Laravel dev server (if used)
EXPOSE 80

# Start Laravel using artisan serve (you can change this to use PHP-FPM + Nginx in production)
CMD php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan serve --host=0.0.0.0 --port=80
