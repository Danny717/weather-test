FROM php:8.4-cli AS vendor
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git unzip zip libicu-dev zlib1g-dev libzip-dev libxml2-dev libpng-dev libonig-dev libcurl4-openssl-dev \
    && docker-php-ext-install intl pdo pdo_mysql xml zip gd opcache bcmath curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev --prefer-dist --no-progress --no-scripts

FROM ubuntu:24.04
ENV DEBIAN_FRONTEND=noninteractive TZ=Etc/UTC XDEBUG_MODE=develop,debug XDEBUG_CONFIG="client_host=host.docker.internal"
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates curl gnupg2 lsb-release software-properties-common unzip zip \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update \
    && apt-get install -y --no-install-recommends php8.4-fpm php8.4-intl php8.4-mbstring php8.4-xml php8.4-zip php8.4-gd php8.4-pdo php8.4-pdo-mysql php8.4-opcache php8.4-bcmath php8.4-curl php8.4-xdebug \
    && rm -rf /var/lib/apt/lists/*

RUN sed -i "s|listen = .*|listen = 0.0.0.0:9000|" /etc/php/8.4/fpm/pool.d/www.conf

RUN echo "xdebug.mode=debug,develop" >> /etc/php/8.4/mods-available/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /etc/php/8.4/mods-available/xdebug.ini

COPY --from=vendor /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY --from=vendor /app/composer.lock ./composer.lock
COPY . .

RUN mkdir -p var/cache var/log && chown -R www-data:www-data var

EXPOSE 9000
CMD ["php-fpm8.4", "-F"]