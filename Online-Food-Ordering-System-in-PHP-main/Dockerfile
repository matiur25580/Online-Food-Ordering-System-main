FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite \
    && printf "ServerName localhost\n" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . /var/www/html/

# Render routes incoming traffic to Apache on port 10000.
ENV PORT=10000
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/:80/:10000/' /etc/apache2/sites-available/000-default.conf

EXPOSE 10000
CMD ["apache2-foreground"]
