FROM php:7.4-apache as build-stage

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
libzip-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-enable apcu

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

COPY app /var/www/
RUN chown -R www-data:www-data /var/www

WORKDIR /var/www/

RUN ["composer", "install", "--no-dev"]

FROM nginx:alpine

RUN apk install envsubst

COPY --from=build-stage /var/www/ /var/www/app/

COPY nginx.conf /etc/nginx/conf.d/default.conf

CMD /bin/bash -c "envsubst '\$PORT \$HEROKU_APP_CLIENT_URL \$HEROKU_APP_BACKEND_URL' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf" && nginx -g 'daemon off;'