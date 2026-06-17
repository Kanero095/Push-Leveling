# STAGE 1: Build Frontend Assets
FROM node:20-slim AS frontend-builder
WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
RUN npm run build

# STAGE 2: Build Backend & Production Image
FROM php:8.3-apache

# Install system dependencies & Postgres development libraries
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        bcmath \
        gd \
        intl \
        zip \
        opcache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set Apache document root to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache to listen on Render's dynamic $PORT (defaults to 10000)
ENV PORT=10000
RUN sed -s -i -e "s/Listen 80/Listen \${PORT}/" /etc/apache2/ports.conf
RUN sed -s -i -e "s/<VirtualHost \*:80>/<VirtualHost *:\${PORT}>/" /etc/apache2/sites-available/*.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files (excluding things in .dockerignore)
COPY . .

# Copy built frontend assets from STAGE 1
COPY --from=frontend-builder /app/public/build ./public/build

# Install PHP dependencies for production
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Adjust folder permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 10000

# Start command: run migrations, cache config, and start Apache
CMD php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground
