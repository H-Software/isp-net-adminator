FROM php:5.6-apache

RUN docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli

# apache config
COPY configs/apache2/vhosts/ /etc/apache2/sites-enabled/

# app code
COPY adminator2/ /var/www/html/adminator2/
COPY adminator3/ /var/www/html/adminator3/
