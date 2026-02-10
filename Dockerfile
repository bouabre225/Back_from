FROM php:8.2-apache

# Dépendances système + SSL
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    openssl \
    ca-certificates \
    && docker-php-ext-install pdo pdo_mysql

# Apache rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Installer Composer
COPY composer.json composer.lock ./
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader \
    && php -r "unlink('composer-setup.php');"

# Copier le code
COPY . .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]