# https://hub.docker.com/_/nginx
# FROM: nginx:stable-otel
FROM nginx:mainline-otel

# WORKDIR /srv/www

# config
COPY configs/nginx/vhosts /etc/nginx/conf.d/

# app code
COPY adminator2/ /srv/www/adminator2/
COPY adminator3/ /srv/www/adminator3/

# shared stuff
COPY adminator3/templates/inc.intro.category-ext.tpl /srv/www/adminator2/templates/inc.intro.category-ext.tpl
COPY adminator3/include/main.function.shared.php /srv/www//adminator2/include/main.function.shared.php
