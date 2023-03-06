FROM composer:latest AS composer
FROM php:8.1-alpine AS php81
COPY --from=composer /usr/bin/composer /usr/bin/composer