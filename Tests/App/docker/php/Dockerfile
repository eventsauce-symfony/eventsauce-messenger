FROM php:8.2.0RC2-fpm-alpine3.16 AS php-dev
ARG TIMEZONE

RUN docker-php-ext-install opcache pdo_mysql intl zip; \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    printf 'date.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini

RUN apk add --no-cache git curl unzip zip

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions amqp

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY docker/composer/auth.json /home/www-data/.composer/auth.json

COPY docker/php/php.dev.ini /usr/local/etc/php/php.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php-ext-xdebug.dev.ini /usr/local/etc/php/conf.d/php-ext-xdebug.ini
COPY docker/php/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini
COPY docker/php/php-ext-opcache.dev.ini /usr/local/etc/php/conf.d/php-ext-opcache.ini

RUN apk add --no-cache shadow \
    && usermod -u 1000 www-data \
    && apk del shadow \
    && chown -R www-data /home/www-data /usr/share \
    && chgrp -R www-data /home/www-data /usr/share

USER www-data

WORKDIR /usr/share/nginx/html

CMD ["php-fpm"]
