FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    librabbitmq-dev \
    zip \
    unzip \
    && docker-php-ext-install \
        intl \
        mbstring \
        pdo \
        pdo_mysql \
        zip \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENV APP_ENV=dev

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-scripts --no-interaction --prefer-dist

COPY . .

RUN composer run-script post-install-cmd --no-interaction || true \
    && chown -R www-data:www-data var/

EXPOSE 9000
CMD ["php-fpm"]
