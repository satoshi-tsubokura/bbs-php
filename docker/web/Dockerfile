FROM php:8.2-apache
RUN apt-get update -y 
RUN a2enmod rewrite
RUN docker-php-ext-install pdo_mysql
RUN ln -s /var/www/public /var/www/html
RUN mkdir /var/log/app && chown -R www-data:www-data /var/log/app