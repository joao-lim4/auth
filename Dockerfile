FROM php:7.4-cli
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN composer install
RUN cp ./env.exemple ./.env

EXPOSE 5000

CMD php -S 0.0.0.0:3000 -t public
