FROM composer:2.5.8 AS composer

WORKDIR /app

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install --no-dev --no-scripts --no-interaction --no-progress --prefer-dist

FROM php:8.2-cli-alpine3.18

RUN apk add --no-cache git grep

WORKDIR /app

COPY . .
COPY --from=composer /app/vendor vendor

ENV TERM=xterm-256color

RUN ln -s /app/bin/ladoc /usr/local/bin/ladoc
RUN ln -s /app/bin/ladoc /usr/local/bin/zz
