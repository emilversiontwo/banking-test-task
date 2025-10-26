FROM php:8.3-fpm

ARG UID=1000
ARG GID=1000

RUN set -eux; \
    apt-get update && apt-get install -y --no-install-recommends \
      ca-certificates apt-transport-https gnupg2 zip unzip git \
      libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
      libonig-dev libxml2-dev curl build-essential pkg-config libicu-dev \
      zlib1g-dev \
    ; \
    mkdir -p /tmp/pear/cache && chown -R www-data:www-data /tmp/pear; \
    pecl config-set temp_dir /tmp/pear || true; \
    docker-php-ext-configure gd --with-jpeg --with-freetype; \
    docker-php-ext-install -j"$(nproc)" gd pdo pdo_pgsql pgsql zip bcmath intl sockets opcache; \
    pecl clear-cache || true; \
    rm -rf /var/lib/apt/lists/* /tmp/pear/cache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

RUN groupadd -g ${GID} appuser || true \
 && useradd -u ${UID} -g ${GID} -m appuser || true \
 && chown -R appuser:appuser /var/www/html

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/composer

EXPOSE 9000
CMD ["php-fpm"]
