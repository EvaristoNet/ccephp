FROM php:8.2-apache
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN apt-get update && apt-get install -y git unzip libcurl4-openssl-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install curl
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . .
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php \
 && composer install --no-dev --optimize-autoloader
COPY docker/run-apache.sh /usr/local/bin/run-apache.sh
RUN chmod +x /usr/local/bin/run-apache.sh
EXPOSE 8080
CMD ["/usr/local/bin/run-apache.sh"]