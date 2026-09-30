FROM php:7.4-apache

RUN printf '%s\n' \
        'deb [check-valid-until=no] https://snapshot.debian.org/archive/debian/20260401T000000Z bullseye main' \
        'deb [check-valid-until=no] https://snapshot.debian.org/archive/debian/20260401T000000Z bullseye-updates main' \
        'deb [check-valid-until=no] https://snapshot.debian.org/archive/debian-security/20260401T000000Z bullseye-security updates/main' \
        > /etc/apt/sources.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        curl \
        gd \
        gettext \
        iconv \
        mbstring \
        mysqli \
        pdo_mysql \
        simplexml \
        zip \
    && pecl install apcu-5.1.23 \
    && docker-php-ext-enable apcu \
    && a2enmod rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/opencaching.ini
COPY docker/entrypoint.sh /usr/local/bin/opencaching-entrypoint
RUN chmod +x /usr/local/bin/opencaching-entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["opencaching-entrypoint"]
CMD ["apache2-foreground"]
