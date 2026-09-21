FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

COPY backend/ /var/www/backend/

RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/backend#' \
    /etc/apache2/sites-available/000-default.conf

RUN sed -i 's#/var/www/html#/var/www/backend#g' \
    /etc/apache2/apache2.conf

RUN mkdir -p \
    /var/www/backend/uploads/users \
    /var/www/backend/uploads/blogs \
    /var/www/backend/uploads/posts

RUN chown -R www-data:www-data /var/www/backend/uploads \
    && chmod -R 755 /var/www/backend/uploads

WORKDIR /var/www/backend/

EXPOSE 80