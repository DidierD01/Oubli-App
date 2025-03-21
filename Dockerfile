FROM php:8.2-apache

# Activer le mod_rewrite
RUN a2enmod rewrite

# Autoriser .htaccess dans /var/www/html
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Copier le projet dans /var/www/html
COPY . /var/www/html/

# Donner les bons droits (optionnel mais propre)
RUN chown -R www-data:www-data /var/www/html
