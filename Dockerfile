FROM php:8.2-apache

# Copie uniquement les fichiers dans app/tasks/
COPY ./app/tasks /var/www/html

# Active mod_rewrite
RUN a2enmod rewrite

# Ajoute index.php comme page d'accueil
RUN echo 'DirectoryIndex index.php' >> /etc/apache2/apache2.conf

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
