FROM php:7.3-cli-alpine

RUN set -xe; \
    apk add --update --no-cache ca-certificates tzdata;

ENV RR_VERSION 1.4.1
RUN : "Install RoadRunner ..."; \
    mkdir -p /tmp/roadrunner; \
    curl \
        -L https://github.com/spiral/roadrunner/releases/download/v${RR_VERSION}/roadrunner-${RR_VERSION}-linux-amd64.tar.gz \
        -o /tmp/roadrunner/roadrunner.tar.gz; \
    tar -oxxf /tmp/roadrunner/roadrunner.tar.gz -C "/tmp/roadrunner" --strip-components=1; \
    mv /tmp/roadrunner/rr /usr/local/bin/rr; \
    chmod u=rwx,g=rx,o=rx /usr/local/bin/rr; \
    rm -rf /tmp/roadrunner;

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
    : "Cleanup"; \
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

ENTRYPOINT ["/usr/local/bin/rr"]
CMD ["serve", "-d", "-c", "/var/www/html/.rr.yml"]

EXPOSE 80
