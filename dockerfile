FROM composer:latest AS composer
FROM php:8.1 AS php81
USER root
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY $PWD /var/www/
RUN apt update
RUN apt install -y git zlib1g-dev libzip-dev unzip zip
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip
USER $USER
RUN composer --version
WORKDIR /var/www/