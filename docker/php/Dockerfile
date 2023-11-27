# PHP
FROM arm64v8/php:8.2.9-fpm-alpine AS php
WORKDIR /var/www/app
RUN set -eux \
    && apk add --no-cache --virtual .pgsql-deps postgresql-dev \
    && docker-php-ext-install -j$(nproc) pcntl pdo_pgsql \
    && apk add --no-cache --virtual .pgsql-rundeps so:libpq.so.5 \
    && apk del .pgsql-deps

ENTRYPOINT ["/bin/sh", "-c"]
CMD ["php-fpm"]

# COMPOSER
FROM composer:latest AS composer
WORKDIR /var/www/app

# XDEBUG
FROM php AS xdebug
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN set -eux \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --update linux-headers \
    && pecl install xdebug-3.2.2 \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps

# LOCAL STAGE
FROM xdebug AS app-local
