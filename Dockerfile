# https://github.com/docker-library/php/blob/master/8.2/bookworm/fpm/Dockerfile
FROM php:8.2-fpm AS php-ext

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
        autoconf \
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
        && pecl install sqlsrv-5.12.0 \
        && pecl install pdo_sqlsrv-5.12.0

# Install APCu and APC backward compatibility
RUN export MAKEFLAGS="-j $(nproc)" \
        && pecl install apcu

RUN git clone --depth 1 -b v1.63.0 https://github.com/grpc/grpc /tmp/grpc && \
    cd /tmp/grpc/src/php/ext/grpc && \
    phpize && \
    ./configure && \
    make && \
    make install && \
    rm -rf /tmp/grpc

# opentelemetry & protobuf
RUN export MAKEFLAGS="-j $(nproc)" \
        && pecl install \
            opentelemetry \
            protobuf \
            redis 
            # grpc

FROM php:8.2-fpm AS main

WORKDIR /srv/www

ENV ACCEPT_EULA=Y

# Copy extensions from php-ext stage
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/grpc.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/grpc.so
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
COPY --from=php-ext /usr/local/lib/php/extensions/no-debug-non-zts-20220829/redis.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/redis.so

# packages required for php extensions and composer
#   MSSQL
#    -> https://learn.microsoft.com/en-gb/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-2017
RUN apt-get update \
    && apt-get install -y \
        gnupg \
        libfcgi-bin \
        util-linux \
        unzip \
    && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && apt-get install -y \
        libzip4 \
        libpng16-16 \
        msodbcsql18 \
        libpq5 \
        libgrpc29 \
        diffutils

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
        zip \
        grpc

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /srv/www/adminator2/ \
        && cd /srv/www/adminator2 \
        && mkdir temp log \
        && chown www-data:www-data temp log

RUN mkdir -p /srv/www/adminator3/ \
        && cd /srv/www/adminator3 \
        && mkdir temp logs export \
        && chown www-data:www-data temp logs export

COPY adminator2/composer.json /srv/www/adminator2/
COPY adminator3/composer.json /srv/www/adminator3/

RUN cd adminator2 \
     && composer install --no-dev

RUN cd adminator3 \
    && composer install --no-dev

# clean-up
RUN apt-get purge -y --allow-remove-essential \
    libgcc-12-dev \
    libstdc++-12-dev \
    linux-libc-dev \
    curl \
    gnupg \
    make \
    m4 \
    re2c \
    pkg-config \
    file \
    unzip \
    && apt autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# app code
COPY adminator2/ /srv/www/adminator2/
COPY adminator3/ /srv/www/adminator3/

# shared stuff
COPY adminator3/templates/inc.intro.category-ext.tpl /srv/www/adminator2/templates/inc.intro.category-ext.tpl
COPY adminator3/include/main.function.shared.php /srv/www//adminator2/include/main.function.shared.php

RUN chmod 1777 /tmp \
    && cd adminator3 \
    && chown www-data:www-data export \
    && mkdir -p logs \
    && chown www-data:www-data logs \
    && cd print \
    && mkdir -p temp \
    && chown www-data:www-data temp

# fpm conf

# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY configs/php-fpm/ /usr/local/etc/php-fpm.d

COPY ./configs/php/docker.ini /usr/local/etc/php/conf.d/

# Enable php fpm status page
RUN set -xe && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf

COPY ./configs/php-fpm-healthcheck /usr/local/bin/php-fpm-healthcheck

RUN chmod +x /usr/local/bin/php-fpm-healthcheck

# # dont run as root
# USER www-data:www-data

COPY docker-php-entrypoint /usr/local/bin/docker-php-entrypoint

RUN chmod 0775 /usr/local/bin/docker-php-entrypoint

# workaround for squash
#
FROM scratch
COPY --from=main / /

RUN rm -rf /usr/bin/composer

# # dont run as root
# USER www-data:www-data

# copy "original" statements for working image
#
ENV PHP_INI_DIR /usr/local/etc/php

ENTRYPOINT ["docker-php-entrypoint"]

WORKDIR /srv/www

# Override stop signal to stop process gracefully
# https://github.com/php/php-src/blob/17baa87faddc2550def3ae7314236826bc1b1398/sapi/fpm/php-fpm.8.in#L163
STOPSIGNAL SIGQUIT

EXPOSE 9000
CMD ["php-fpm"]

