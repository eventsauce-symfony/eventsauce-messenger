FROM php:8.1-fpm-alpine3.15 AS php-dev

RUN docker-php-ext-install \
        opcache \
        pdo_mysql \
    && echo "date.timezone = Europe/Warsaw" > /usr/local/etc/php/conf.d/timezone.ini

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY docker/php.dev.ini /usr/local/etc/php/php.ini
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/docker-entrypoint.sh /usr/local/bin/

RUN apk add --no-cache shadow \
    && usermod -u 1000 www-data \
    && apk del shadow

USER www-data

WORKDIR /usr/share/nginx/html

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["php-fpm"]


## NGINX-DEV
FROM nginx:1.21-alpine AS nginx-dev

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /usr/share/nginx/html/public
