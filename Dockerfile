# STAGE 1: Install Composer Dependencies
FROM composer:2 AS composer-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

# STAGE 2: Build Frontend Assets
FROM node:20-slim AS frontend-builder
WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
# Copy vendor folder from composer-builder so Vite can resolve Flux CSS
COPY --from=composer-builder /app/vendor ./vendor
RUN npm run build

# STAGE 3: Build Backend & Production Image
FROM php:8.3-apache

# Install PHP dependencies & libraries
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    gd \
    pdo_pgsql \
    pgsql \
    bcmath \
    intl \
    zip \
    opcache

# Enable Apache modules (rewrite, headers)
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Copy composer vendor from STAGE 1
COPY --from=composer-builder /app/vendor /var/www/html/vendor

# Copy built frontend assets from STAGE 2
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Install Composer binary & dump autoloader
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# Apache DocumentRoot → public/ (using your working configuration)
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Configure Apache to listen on Render's dynamic $PORT (defaults to 80)
ENV PORT=80
RUN sed -s -i -e "s/Listen 80/Listen \${PORT}/" /etc/apache2/ports.conf
RUN sed -s -i -e "s/<VirtualHost \*:80>/<VirtualHost *:\${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Set permissions for storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port 80 (or $PORT at runtime)
EXPOSE 80

# Start command: run migrations, cache config, and start Apache
CMD php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground
