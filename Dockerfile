ARG ALPINE_TAG="3.8"
ARG PHP_TAG="7.3-cli-alpine3.8"

FROM php:$PHP_TAG as ext-builder
RUN docker-php-source extract && \
    apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS

FROM ext-builder as ext-pdo-mysql
RUN docker-php-ext-install pdo_mysql

FROM ext-builder as ext-bcmath
RUN docker-php-ext-install bcmath

FROM ext-builder as ext-swoole
ARG SWOOLE_VERSION=4.2.11
RUN pecl install swoole-$SWOOLE_VERSION && \
    docker-php-ext-enable swoole

FROM ext-builder as ext-amqp
RUN apk add --no-cache rabbitmq-c-dev
ARG AMQP_VERSION=1.9.4
RUN pecl install amqp-$AMQP_VERSION && \
    docker-php-ext-enable amqp

FROM composer:latest as composer-installer
ENV COMPOSER_HOME="/root/.composer" \
    COMPOSER_ALLOW_SUPERUSER=1
RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative --ansi

FROM composer-installer as app-installer
WORKDIR /usr/src/app
COPY composer.json composer.lock symfony.lock ./
RUN composer validate
ARG COMPOSER_ARGS=install
RUN composer "$COMPOSER_ARGS" --prefer-dist --ignore-platform-reqs --no-progress --no-suggest --no-scripts --no-autoloader --ansi
COPY . ./
RUN composer dump-autoload --classmap-authoritative --ansi

FROM php:$PHP_TAG as base-no-source
RUN apk add --no-cache libaio libstdc++ zip openssl tzdata rabbitmq-c
WORKDIR /usr/src/app

ARG TIMEZONE="Europe/Warsaw"
RUN ln -snf /usr/share/zoneinfo/$TIMEZONE /etc/localtime && echo $TIMEZONE > /etc/timezone && \
    "date" && \
    apk del tzdata

COPY --from=ext-swoole /usr/local/lib/php/extensions/no-debug-non-zts-20180731/swoole.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/swoole.so
COPY --from=ext-swoole /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini
COPY --from=ext-bcmath /usr/local/lib/php/extensions/no-debug-non-zts-20180731/bcmath.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/bcmath.so
COPY --from=ext-bcmath /usr/local/etc/php/conf.d/docker-php-ext-bcmath.ini /usr/local/etc/php/conf.d/docker-php-ext-bcmath.ini
COPY --from=ext-pdo-mysql /usr/local/lib/php/extensions/no-debug-non-zts-20180731/pdo_mysql.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/pdo_mysql.so
COPY --from=ext-pdo-mysql /usr/local/etc/php/conf.d/docker-php-ext-pdo_mysql.ini /usr/local/etc/php/conf.d/docker-php-ext-pdo_mysql.ini
COPY --from=ext-amqp /usr/local/lib/php/extensions/no-debug-non-zts-20180731/amqp.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/amqp.so
COPY --from=ext-amqp /usr/local/etc/php/conf.d/docker-php-ext-amqp.ini /usr/local/etc/php/conf.d/docker-php-ext-amqp.ini

FROM base-no-source as base
COPY --from=app-installer /usr/src/app ./
RUN mv .env.docker .env && \
    bin/console cache:clear --env docker && \
    bin/console assets:install public --symlink --relative --env docker

FROM base as SymfonyConsole
ENTRYPOINT ["php", "-d", "memory_limit=-1", "bin/console"]
CMD ["list"]

FROM base-no-source as WorkerSymfonyConsole
RUN apk add --no-cache git
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer-installer /usr/bin/composer /usr/local/bin/composer
COPY --from=composer-installer /root/.composer /root/.composer
RUN composer global require "phpstan/phpstan" "friendsofphp/php-cs-fixer" \
    --prefer-dist --no-progress --no-suggest --classmap-authoritative --ansi
COPY --from=app-installer /usr/src/app ./
RUN mv .env.docker .env && \
    bin/console cache:clear --env docker && \
    bin/console assets:install public --symlink --relative --env docker

ENTRYPOINT ["php", "-d", "memory_limit=-1", "bin/console"]
CMD ["list"]

#FROM base as Composer
#ENV COMPOSER_ALLOW_SUPERUSER=1
#COPY --from=composer-installer /usr/bin/composer /usr/local/bin/composer
#ENTRYPOINT ["composer"]
#CMD ["list"]

#HEALTHCHECK --interval=5s --timeout=1s --start-period=1m \
#  CMD curl --fail http://${HOST}:${PORT}/status || exit 1
