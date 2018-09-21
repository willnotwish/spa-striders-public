FROM wordpress:php7.1-apache

WORKDIR /var/www/html
COPY . .

