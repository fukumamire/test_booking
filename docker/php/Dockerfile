FROM php:7.4.9-fpm

COPY php.ini /usr/local/etc/php/

RUN apt-get update \
  && apt-get install -y default-mysql-client zlib1g-dev libzip-dev unzip libpng-dev \
  && docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

RUN apt-get update && apt-get install -y \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd


RUN apt-get update && apt-get install -y \
  libmagickwand-dev \
  && pecl install imagick \
  && docker-php-ext-enable imagick

WORKDIR /var/www
