FROM php:8.9

RUN apt-get update && apt-get upgrade
RUN apt-get install curl git zip unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo mbstring

WORKDIR /app

COPY . /app
RUN composer install
CMD php artisan serve --host=0.0.0.0 --port=80
EXPOSE 80