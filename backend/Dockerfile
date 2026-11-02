FROM php:8.4-fpm AS build

RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        libfreetype-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        librabbitmq-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd zip pdo_mysql \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm /tmp/composer-setup.php

WORKDIR /application
COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --classmap-authoritative

FROM php:8.4-fpm AS production

ENV APP_ENV=production

RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6 \
        libjpeg62-turbo \
        libpng16-16 \
        libzip5 \
        librabbitmq4 \
    && rm -rf /var/lib/apt/lists/* \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=build /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /application
COPY --from=build /application /application

RUN mkdir -p /application/log /application/var \
    && chown -R www-data:www-data /application/log /application/var

RUN ln -s /application/var/storage /application/public/storage

CMD ["php-fpm"]
