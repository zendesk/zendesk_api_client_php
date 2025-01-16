FROM php:8.2-cli

# OS requirements for PHP
RUN apt-get update && apt-get install -y git unzip

# Safe requirements to allow Makefile tasks to run
RUN git config --global --add safe.directory /app

# PHP requirements
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
COPY composer.json composer.lock /app/
RUN composer install

COPY . /app
