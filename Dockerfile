# Base image PHP + extensions PDO + Composer
FROM php:8.2-apache

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite Apache
RUN a2enmod rewrite

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier composer et installer les dépendances
COPY composer.json composer.lock ./
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader \
    && php -r "unlink('composer-setup.php');"

# Copier tout le code source
COPY . .

# Donner les droits
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80
EXPOSE 80

# Commande de lancement
CMD ["apache2-foreground"]
