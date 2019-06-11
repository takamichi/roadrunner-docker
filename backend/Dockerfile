# syntax = docker/dockerfile:1.0-experimental

FROM composer:latest AS composer

ENV APP_ROOT="/var/www/html"

COPY composer.json composer.lock ${APP_ROOT}/

WORKDIR ${APP_ROOT}
RUN --mount=type=cache,target=/tmp/cache \
    set -xe; \
    : "Validate composer.json ..."; \
    composer validate --working-dir=${APP_ROOT} --strict --no-check-publish --no-interaction; \
    : "Install dependency packages ..."; \
    composer install \
        --working-dir=${APP_ROOT} \
        --ignore-platform-reqs \
        --no-dev \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --optimize-autoloader \
        --prefer-dist;

RUN ./vendor/bin/rr get-binary --location=/usr/local/bin

#COPY config ${APP_ROOT}/config
COPY public ${APP_ROOT}/public
#COPY src ${APP_ROOT}/src
#COPY storage ${APP_ROOT}/storage

VOLUME ["/tmp"]


FROM php:7.3-cli-alpine

RUN set -xe; \
    apk add --update --no-cache ca-certificates inotify-tools tzdata;

RUN : "Install PHP extensions ..."; \
    apk add --update --no-cache -t .php-rundeps \
        bzip2 \
        c-client \
        freetype \
        icu-libs \
        libbz2 \
        libltdl \
        libxslt \
        libzip; \
    \
    apk add --update --no-cache -t .build-deps \
        autoconf \
        cmake \
        build-base \
        bzip2-dev \
        freetype-dev \
        icu-dev \
        libtool \
        libxslt-dev \
        libzip-dev \
        openssl-dev \
        pcre-dev; \
    \
    docker-php-source extract; \
    \
    NPROC=$(getconf _NPROCESSORS_ONLN); \
    docker-php-ext-install -j${NPROC} \
        bcmath \
        bz2 \
        ftp \
        intl \
        opcache \
        pcntl \
        pdo \
        pdo_mysql \
        sockets \
        xmlrpc \
        xsl \
        zip; \
    \
    : "Install PECL extensions ..."; \
    pecl config-set php_ini "${PHP_INI_DIR}/php.ini"; \
    pecl install \
        redis \
        xdebug; \
    docker-php-ext-enable \
        redis; \
    \
    : "Cleanup ..."; \
    docker-php-source delete; \
    apk del --purge .build-deps; \
    pecl clear-cache; \
    rm -rf \
        /usr/include/php \
        /usr/lib/php/build \
        /usr/lib/mysqld* \
        /tmp/* \
        /var/cache/apk/*; \
    rm -rf /usr/src/php.tar.xz; \
    \
    : "Report"; \
    php -v; \
    php -i;

WORKDIR /var/www/html

COPY --from=composer /usr/local/bin/rr /usr/local/bin/rr

ENTRYPOINT ["/usr/local/bin/rr"]
CMD ["serve", "-d", "-c", "/var/www/html/.rr.yml"]

EXPOSE 80
