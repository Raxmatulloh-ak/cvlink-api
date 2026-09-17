FROM php:8.4.24-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql \
	&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html
