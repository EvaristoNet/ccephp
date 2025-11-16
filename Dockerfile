FROM php:8.2-apache
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN apt-get update && apt-get install -y git unzip && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . .
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php \
 && composer install --no-dev --optimize-autoloader || true
RUN sed -ri 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
 && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf
EXPOSE 8080
CMD ["apache2-foreground"]