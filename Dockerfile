FROM php:8.2-apache

# ------------------------------------------------------
# 1. Install PHP dependencies
# ------------------------------------------------------
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd pdo_pgsql pgsql

# Enable Apache modules
RUN a2enmod rewrite headers

# ------------------------------------------------------
# 2. Set working directory
# ------------------------------------------------------
WORKDIR /var/www/html

# ------------------------------------------------------
# 3. Copy project
# ------------------------------------------------------
COPY . /var/www/html

# ------------------------------------------------------
# 4. Install Composer dependencies
# ------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# ------------------------------------------------------
# 5. Apache DocumentRoot → public/
# ------------------------------------------------------
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# ------------------------------------------------------
# 6. Permissions
# ------------------------------------------------------
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
