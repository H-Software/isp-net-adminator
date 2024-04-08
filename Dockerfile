FROM php:5.6-apache

ENV ACCEPT_EULA=Y

# fix dead mirrors
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list

#
# PHP stuff
#

RUN apt-get update \
    && apt-get install -y \
        libpq-dev \
        wget \
        zip \
        unzip \
        zlib1g-dev \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN echo 'date.timezone = "UTC"' > /usr/local/etc/php/conf.d/timezone.ini

# PHP MSSQL stuff
# https://github.com/petersonwsantos/docker-php5.6-mssql/blob/master/Dockerfile
# https://github.com/Namoshek/docker-php-mssql/blob/master/8.1/fpm/Dockerfile
RUN apt-get update && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libedit-dev \
        libsqlite3-dev \
        libssl-dev \
        libxml2-dev \
        zlib1g-dev \
        freetds-dev \
        freetds-bin \
        freetds-common \
        libdbd-freetds \
        libsybdb5 \
        libqt4-sql-tds \
        libqt5sql5-tds \
        libqxmlrpc-dev \
      && apt-get clean \
      && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
      && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/libsybdb.so \
      && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/libsybdb.a \
      && docker-php-ext-install   mssql \
      && docker-php-ext-configure mssql

# apache conf
RUN a2enmod ssl && a2enmod rewrite
# RUN mkdir -p /etc/apache2/ssl
# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY configs/apache2/vhosts/ /etc/apache2/sites-enabled/

# development stuff
RUN wget -O /usr/local/bin/composer "https://getcomposer.org/download/latest-2.2.x/composer.phar" \
    && chmod +x /usr/local/bin/composer \
    && mkdir -p /.composer/cache \
    && chmod -R 777 /.composer

# app code
COPY adminator2/ /var/www/html/adminator2/
COPY adminator3/ /var/www/html/adminator3/
# shared stuff
COPY libs/smarty/ /var/www/html/adminator2/smarty/
# COPY libs/smarty/ /var/www/html/adminator3/smarty/

COPY adminator3/templates/inc.intro.category-ext.tpl /var/www/html/adminator2/templates/inc.intro.category-ext.tpl
COPY adminator3/templates/inc.home.list-logged-users.tpl /var/www/html/adminator2/templates/inc.home.list-logged-users.tpl

COPY adminator3/include/main.function.shared.php /var/www/html/adminator2/include/main.function.shared.php

RUN cd adminator3 \
    && composer require nette/robot-loader:3.1.4 \
    && composer require smarty/smarty:2.6.33
    # && pecl install xdebug-2.5.5 \
    # && docker-php-ext-enable xdebug \


