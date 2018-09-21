FROM wordpress:php7.1-fpm-alpine

WORKDIR /var/www/html
COPY . .

