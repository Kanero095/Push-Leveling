# STAGE 1: Build Frontend Assets
FROM node:20-slim AS frontend-builder
WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
RUN npm run build

# STAGE 2: Build Backend & Production Image
FROM php:8.3-apache

# Install PHP dependencies & libraries (combining both setups)
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

# Configure & install PHP extensions (including pdo, pdo_mysql, gd, pdo_pgsql, pgsql, etc.)
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

# Copy built frontend assets from STAGE 1
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Install Composer dependencies
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Apache DocumentRoot → public/ (using your working configuration)
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Set permissions for storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port 80 (Render automatically detects this and maps public traffic)
EXPOSE 80

# Start command: run migrations, cache config, and start Apache
CMD php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground
