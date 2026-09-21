FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apt-get update \
    && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY frontend/ /var/www/html/

WORKDIR /var/www/html/

EXPOSE 80