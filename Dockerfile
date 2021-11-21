FROM php:7.4-cli
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN chmod +x composer.phar
RUN mv composer.phar /usr/bin/composer
RUN yum install zip unzip php7.4-zip

RUN composer install
RUN cp ./env.exemple ./.env

EXPOSE 5000

CMD php -S 0.0.0.0:3000 -t public
