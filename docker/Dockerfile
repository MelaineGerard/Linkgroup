FROM alpine:3.16
LABEL maintainer="Melaine Gérard <pro@melaine-gerard.fr>"
LABEL version="0.1"

WORKDIR /var/www/linkgroup

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf
COPY ./docker/nginx/vhost.conf /etc/nginx/sites-available/default.conf
RUN echo 'http://dl-cdn.alpinelinux.org/alpine/edge/main' > /etc/apk/repositories
RUN echo 'http://dl-cdn.alpinelinux.org/alpine/edge/community' >> /etc/apk/repositories
RUN echo 'http://dl-cdn.alpinelinux.org/alpine/edge/testing' >> /etc/apk/repositories

RUN apk update && apk add --update --no-cache \
tzdata \
nginx \
nano \
neovim \
supervisor \
php82 \
php82-fpm \
php82-pdo \
php82-iconv \
php82-pdo_mysql \
php82-sodium \
php82-mbstring \
php82-xml \
php82-openssl \
php82-phar \
php82-zip \
php82-curl \
php82-dom \
php82-session \
php82-bcmath \
php82-pecl-redis \
php82-ctype \
php82-tokenizer \
php82-session \
php82-fileinfo \
php82-xmlwriter \
php82-simplexml \
nodejs \
npm

RUN php82 -r "copy('http://getcomposer.org/installer', 'composer-setup.php');" && \
php82 composer-setup.php --install-dir=/usr/bin --filename=composer && \
php82 -r "unlink('composer-setup.php');" && \
ln -s /etc/php82/php.ini /etc/php82/conf.d/php.ini

# Configuration de NGINX
RUN mkdir -p /etc/nginx && \
mkdir -p /etc/nginx/sites-available && \
mkdir -p /etc/nginx/sites-enabled && \
mkdir -p /run/nginx && \
ln -s /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/default.conf && \
mkdir -p /var/log/supervisor

# Configurationd de PHP-FPM
RUN sed -i -e "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g" \
-e "s/variables_order = \"GPCS\"/variables_order = \"EGPCS\"/g" \
/etc/php82/php.ini && \
sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" \
-e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" \
-e "s/user = nobody/user = nginx/g" \
-e "s/group = nobody/group = nginx/g" \
-e "s/;listen.mode = 0660/listen.mode = 0666/g" \
-e "s/;listen.owner = nobody/listen.owner = nginx/g" \
-e "s/;listen.group = nobody/listen.group = nginx/g" \
-e "s/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm.sock/g" \
-e "s/^;clear_env = no$/clear_env = no/" \
/etc/php82/php-fpm.d/www.conf

RUN ln -s /usr/bin/php82 /usr/bin/php

COPY . .

RUN chown -R nginx:nginx /var/www/linkgroup/

RUN composer install

RUN wget https://github.com/tailwindlabs/tailwindcss/releases/download/v3.3.3/tailwindcss-linux-x64 -O /usr/bin/tailwindcss && \
chmod +x /usr/bin/tailwindcss

RUN tailwindcss build -i assets/styles/app.css -o assets/styles/app.built.css --minify


VOLUME [ "/var/log/nginx" ]

RUN chown -R nginx:nginx /var/www/linkgroup/

ENV TZ Europe/Paris
EXPOSE 443 80
CMD [ "/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf" ]