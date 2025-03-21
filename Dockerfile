FROM php:8.2-apache

# Active le module rewrite
RUN a2enmod rewrite

# Autorise .htaccess
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Définit le dossier /app/tasks comme racine du site
WORKDIR /var/www/html
COPY tasks/ /var/www/html/

# Copie le fichier .htaccess s’il est dans /tasks
COPY tasks/.htaccess /var/www/html/

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

# Expose le port
EXPOSE 80
