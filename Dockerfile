FROM php:7.4-apache

ENV ACCEPT_EULA=Y

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
        libpng-dev \
        git \
        libldap2-dev \
        libzip-dev \
        gnupg \
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
            ldap \
            gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# PHP MSSQL stuff
# https://learn.microsoft.com/en-gb/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-2017
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
        && curl -sSL https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list \
        && apt-get update \
        && apt-get install -y \
            msodbcsql17 \
            unixodbc-dev \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install sqlsrv-5.10.1 \
        && pecl install pdo_sqlsrv-5.10.1 \
        && docker-php-ext-enable \
            sqlsrv \
            pdo_sqlsrv

# Install APCu and APC backward compatibility
RUN pecl install apcu \
        && docker-php-ext-enable apcu

# RUN pecl install apcu_bc-1.0.5 \
        # && docker-php-ext-enable apc --ini-name 20-docker-php-ext-apc.ini

# apache conf
RUN a2enmod ssl \
    && a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_http
# RUN mkdir -p /etc/apache2/ssl
# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY configs/apache2/vhosts/ /etc/apache2/sites-enabled/

COPY ./configs/php /usr/local/etc/php/conf.d/

# composer
#
RUN wget -O /usr/local/bin/composer "https://getcomposer.org/download/latest-2.2.x/composer.phar" \
    && chmod +x /usr/local/bin/composer \
    && mkdir -p /.composer/cache \
    && chmod -R 777 /.composer

RUN mkdir -p /var/www/html/adminator3/
RUN mkdir -p /var/www/html/adminator2/

COPY adminator2/composer.json /var/www/html/adminator2/
COPY adminator3/composer.json /var/www/html/adminator3/

RUN cd adminator2 \
     && composer install

# RUN cd adminator3 \
#      && composer update

RUN cd adminator3 \
    && composer install

# RUN cd adminator3 \
#     && composer require \
#         nette/robot-loader:^3.4 \
#         smarty/smarty:^3.1 \
#         slim/slim:3.* \
#         slim/twig-view:^2.5 \
#         slim/csrf:^0.8 \
#         slim/flash:^0.4.0 \
#         monolog/monolog:^1.27.1 \
#         respect/validation:^1.1 \
#         formr/formr:^1.4 \
#         doctrine/orm:^2.11.0 \
#         doctrine/annotations:^1.13.0 \
#         symfony/cache:^4.4 \
#         marcelbonnet/slim-auth:^2.0 \
#     && composer config --no-plugins allow-plugins.kylekatarnls/update-helper true \
#     && composer require \
#         illuminate/database:^5.8

#     # && docker-php-ext-enable xdebug \

# app code
COPY adminator2/ /var/www/html/adminator2/
COPY adminator3/ /var/www/html/adminator3/

# shared stuff
COPY adminator3/models/adminator2 /var/www/html/adminator2/include

COPY adminator3/templates/inc.intro.category-ext.tpl /var/www/html/adminator2/templates/inc.intro.category-ext.tpl
COPY adminator3/templates/inc.home.list-logged-users.tpl /var/www/html/adminator2/templates/inc.home.list-logged-users.tpl

COPY adminator3/include/main.function.shared.php /var/www/html/adminator2/include/main.function.shared.php
