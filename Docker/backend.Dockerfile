FROM php:8.2-apache

# unzip lets Composer extract packages; pdo_mysql is Laravel's MySQL driver
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

# Install dependencies first so this layer stays cached until composer.lock changes
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY backend/ ./

# Build the autoloader, then let Apache write logs/cache and uploaded images
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p public/uploads/users public/uploads/blogs public/uploads/posts \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads

# Serve only public/ (keeps .env and app code unreachable);
# public/.htaccess sends every request to Laravel's index.php
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/backend/public#' \
        /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/backend/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
        >> /etc/apache2/apache2.conf

EXPOSE 80
