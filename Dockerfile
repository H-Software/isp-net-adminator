FROM php:5.6-apache

# fix dead mirrors
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list

#
# PHP stuff
#

RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN echo 'date.timezone = "UTC"' > /usr/local/etc/php/conf.d/timezone.ini

# apache config
COPY configs/apache2/vhosts/ /etc/apache2/sites-enabled/

# app code
COPY adminator2/ /var/www/html/adminator2/
COPY adminator3/ /var/www/html/adminator3/
