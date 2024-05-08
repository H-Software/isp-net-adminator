FROM php:8.2-apache

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
        libgrpc-dev \
        libgrpc++-dev \
        gnupg \
        vim \
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
            sockets \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# PHP MSSQL stuff
# https://learn.microsoft.com/en-gb/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-2017
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
        && curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
        && apt-get update \
        && apt-get install -y \
            msodbcsql17 \
            unixodbc-dev \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install sqlsrv-5.11.1 \
        && pecl install pdo_sqlsrv-5.11.1 \
        && docker-php-ext-enable \
            sqlsrv \
            pdo_sqlsrv

# Install APCu and APC backward compatibility
RUN export MAKEFLAGS="-j $(nproc)" \
        && pecl install apcu \
        && docker-php-ext-enable apcu

# RUN pecl install apcu_bc-1.0.5 \
        # && docker-php-ext-enable apc --ini-name 20-docker-php-ext-apc.ini

# opentelemetry & grpc
RUN export MAKEFLAGS="-j $(nproc)" \
        && pecl install \
            opentelemetry \
            grpc \
        && docker-php-ext-enable \
            opentelemetry \
            grpc

# apache conf
RUN a2enmod ssl \
    && a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_http
# RUN mkdir -p /etc/apache2/ssl
# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY configs/apache2/vhosts/ /etc/apache2/sites-enabled/

COPY ./configs/php /usr/local/etc/php/conf.d/

# ssh for composer custom repo(s)
RUN mkdir /root/.ssh/ \
    && touch /root/.ssh/known_hosts \
    && ssh-keyscan github.com >> /root/.ssh/known_hosts
COPY configs/ssh/* /root/.ssh
RUN cd /root/.ssh/ \
    && base64 -d priv > id_rsa \
    && mv pub id_rsa.pub \
    && chmod 600 /root/.ssh/id_rsa

# composer
#
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

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

# app code
COPY adminator2/ /var/www/html/adminator2/
COPY adminator3/ /var/www/html/adminator3/

# shared stuff
COPY adminator3/templates/inc.intro.category-ext.tpl /var/www/html/adminator2/templates/inc.intro.category-ext.tpl
COPY adminator3/include/main.function.shared.php /var/www/html/adminator2/include/main.function.shared.php

RUN chmod 1777 /tmp

RUN ls -lh /usr/local/lib/php/extensions
