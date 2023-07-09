FROM composer:2.5.8 AS composer

WORKDIR /app

COPY composer.json composer.json

RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --no-progress --no-suggest --prefer-dist

FROM php:8.2-cli-alpine3.18

WORKDIR /app

COPY . .
COPY --from=composer /app/vendor vendor

ENV TERM=xterm-256color

RUN ln -s /app/bin/ladoc /usr/local/bin/ladoc
RUN ln -s /app/bin/ladoc /usr/local/bin/zz
