FROM php:7.4-apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
libzip-dev nano

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

COPY start-apache.sh /var/www/script
RUN chmod +x /tmp/start-apache.sh

CMD [/var/www/script/start-apache.sh"]