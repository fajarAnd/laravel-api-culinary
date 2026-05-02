FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        zip \
        bcmath

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
