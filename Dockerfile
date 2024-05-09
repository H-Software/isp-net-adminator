# https://github.com/docker-library/php/blob/master/8.2/bookworm/apache/Dockerfile
FROM php:8.2-apache AS php-ext

ENV ACCEPT_EULA=Y

#
# install tools & PHP extensions
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
# RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
#         && curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
RUN apt-get update \
        && apt-get install -y \
            unixodbc-dev \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN export MAKEFLAGS="-j $(nproc)" \
        && pecl install sqlsrv-5.11.1 \
        && pecl install pdo_sqlsrv-5.11.1

# Install APCu and APC backward compatibility
RUN export MAKEFLAGS="-j $(nproc)" \
        && pecl install apcu

# opentelemetry & grpc
RUN export MAKEFLAGS="-j $(nproc)" \
        && pecl install \
            opentelemetry \
            protobuf
            # grpc

FROM php:8.2-apache AS main

ENV ACCEPT_EULA=Y

# Copy extensions from php-ext stage
# COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/grpc.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/grpc.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/apcu.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/apcu.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/gd.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/gd.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/ldap.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/ldap.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/mysqli.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/mysqli.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/opcache.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/opcache.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/opentelemetry.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/opentelemetry.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo_mysql.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo_mysql.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo_pgsql.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo_pgsql.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo_sqlsrv.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo_sqlsrv.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pgsql.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/pgsql.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/protobuf.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/protobuf.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/sockets.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/sockets.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/sodium.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/sodium.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/sqlsrv.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/sqlsrv.so
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/zip.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/zip.so

# Enable extensions
RUN docker-php-ext-enable \
        apcu \
        gd \
        ldap \
        mysqli \
        opcache \
        opentelemetry \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        pdo_sqlsrv \
        pgsql \
        protobuf \
        sockets \
        sodium \
        sqlsrv \
        zip 
        # grpc

# packages required for php extensions
#   MSSQL
#    -> https://learn.microsoft.com/en-gb/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-2017
RUN apt-get update \
    && apt-get install -y \
        gnupg \
    && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && apt-get install -y \
        libzip4 \
        libpng16-16 \
        msodbcsql17 \
        libpq5 \
        diffutils \
    && apt-get purge -y --allow-remove-essential \
        libgcc-12-dev \
        libstdc++-12-dev \
        linux-libc-dev \
        util-linux \
        util-linux-extra \
        curl \
        gnupg \
        make \
        m4 \
        re2c \
        pkg-config \
        file \
    && apt autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# apache conf
RUN a2enmod ssl \
    && a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_http
# RUN mkdir -p /etc/apache2/ssl
# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY configs/apache2/vhosts/ /etc/apache2/sites-enabled/

COPY ./configs/php/docker.ini /usr/local/etc/php/conf.d/

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/html/adminator3/
RUN mkdir -p /var/www/html/adminator2/

COPY adminator2/composer.json /var/www/html/adminator2/
COPY adminator3/composer.json /var/www/html/adminator3/

RUN cd adminator2 \
     && composer install

RUN cd adminator3 \
    && composer install

# app code
COPY adminator2/ /var/www/html/adminator2/
COPY adminator3/ /var/www/html/adminator3/

# shared stuff
COPY adminator3/templates/inc.intro.category-ext.tpl /var/www/html/adminator2/templates/inc.intro.category-ext.tpl
COPY adminator3/include/main.function.shared.php /var/www/html/adminator2/include/main.function.shared.php

RUN chmod 1777 /tmp

# workaround for squash
#
FROM scratch
COPY --from=main / /

# copy "original" statements for working image
#
ENV PHP_INI_DIR /usr/local/etc/php
ENV APACHE_CONFDIR /etc/apache2
ENV APACHE_ENVVARS $APACHE_CONFDIR/envvars

ENTRYPOINT ["docker-php-entrypoint"]
# https://httpd.apache.org/docs/2.4/stopping.html#gracefulstop
STOPSIGNAL SIGWINCH

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
