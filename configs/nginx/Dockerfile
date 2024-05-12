# https://hub.docker.com/_/nginx
# FROM: nginx:stable-otel
FROM nginx:mainline-otel

# WORKDIR /srv/www

# config
COPY configs/nginx/vhosts/ /etc/nginx/conf.d/

RUN mkdir -p /srv/www/adminator2/ \
        && cd /srv/www/adminator2 \
        && mkdir temp log \
        && chown www-data:www-data temp log

RUN mkdir -p /srv/www/adminator3/ \
        && cd /srv/www/adminator3 \
        && mkdir temp log logs \
        && chown www-data:www-data temp log logs

# app code
COPY adminator2/ /srv/www/adminator2/
COPY adminator3/ /srv/www/adminator3/

# shared stuff
COPY adminator3/templates/inc.intro.category-ext.tpl /srv/www/adminator2/templates/inc.intro.category-ext.tpl
COPY adminator3/include/main.function.shared.php /srv/www//adminator2/include/main.function.shared.php
