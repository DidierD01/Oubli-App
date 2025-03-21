FROM php:8.2-apache

# Activer le mod_rewrite
RUN a2enmod rewrite

# Autoriser .htaccess dans /var/www/html
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Copier tous les fichiers dans le bon dossier
COPY . /var/www/html/

# Droits corrects
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80 (important pour Render)
EXPOSE 80
