FROM php:7.1-apache

ENV ACCEPT_EULA=Y

# fix dead mirrors
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list

#
# PHP stuff
#

RUN apt-get update \
    && apt-get install -y --allow-downgrades \
        zlib1g=1:1.2.8.dfsg-5 \
    && apt-get install -y \
        libpq-dev \
        wget \
        zip \
        unzip \
        zlib1g-dev \
        git \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install \
            opcache \
            pgsql \
            pdo_pgsql \
            zip \
            pdo \
            pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
    # && pecl install apcu \
    # && docker-php-ext-enable apcu \
    # && docker-php-ext-install intl 

# PHP MSSQL stuff
# https://github.com/petersonwsantos/docker-php5.6-mssql/blob/master/Dockerfile
# https://github.com/Namoshek/docker-php-mssql/blob/master/8.1/fpm/Dockerfile
# RUN apt-get update && apt-get install -y --no-install-recommends \
#         libcurl4-openssl-dev \
#         libedit-dev \
#         libsqlite3-dev \
#         libssl-dev \
#         libxml2-dev \
#         freetds-dev \
#         freetds-bin \
#         freetds-common \
#         libdbd-freetds \
#         libsybdb5 \
#         libqt4-sql-tds \
#         libqt5sql5-tds \
#         libqxmlrpc-dev \
#       && apt-get clean \
#       && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
#       && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/libsybdb.so \
#       && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/libsybdb.a \
#       && docker-php-ext-install   mssql \
#       && docker-php-ext-configure mssql

# apache conf
RUN a2enmod ssl \
    && a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_http
# RUN mkdir -p /etc/apache2/ssl
# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY configs/apache2/vhosts/ /etc/apache2/sites-enabled/

COPY ./configs/php /usr/local/etc/php/conf.d/

# development stuff
RUN wget -O /usr/local/bin/composer "https://getcomposer.org/download/latest-2.2.x/composer.phar" \
    && chmod +x /usr/local/bin/composer \
    && mkdir -p /.composer/cache \
    && chmod -R 777 /.composer

RUN mkdir -p /var/www/html/adminator3/

RUN cd adminator3 \
    && composer require \
        nette/robot-loader:^3.4 \
        smarty/smarty:^3.1 \
        slim/slim:3.* \
        slim/twig-view:^2.5 \
        slim/csrf:^0.8 \
        slim/flash:^0.4.0 \
        monolog/monolog:^1.27.1 \
        respect/validation:^1.1 \
        formr/formr:^1.4 \
    && composer config --no-plugins allow-plugins.kylekatarnls/update-helper true \
    && composer require \
        illuminate/database:^5.8

#     # && docker-php-ext-enable xdebug \

# app code
COPY adminator2/ /var/www/html/adminator2/
COPY adminator3/ /var/www/html/adminator3/

# shared stuff
COPY libs/smarty/ /var/www/html/adminator2/smarty/
# COPY libs/smarty/ /var/www/html/adminator3/smarty/
COPY adminator3/models/adminator2 /var/www/html/adminator2/include

COPY adminator3/templates/inc.intro.category-ext.tpl /var/www/html/adminator2/templates/inc.intro.category-ext.tpl
COPY adminator3/templates/inc.home.list-logged-users.tpl /var/www/html/adminator2/templates/inc.home.list-logged-users.tpl

COPY adminator3/include/main.function.shared.php /var/www/html/adminator2/include/main.function.shared.php


# RUN cd adminator3 \
#      && composer update

# RUN cd adminator3 \
#     && composer install
