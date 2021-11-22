FROM php:7.4-cli
COPY . /usr/src/auth
WORKDIR /usr/src/auth

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN chmod +x composer.phar
RUN mv composer.phar /usr/bin/composer

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    # php7.4-zip \
    zip \
    unzip

RUN composer install
RUN cp ./.env.example ./.env

EXPOSE 8000

CMD php -S 0.0.0.0:8000 -t public
