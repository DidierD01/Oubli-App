FROM php:8.2-apache

# Copie uniquement les fichiers de app/tasks dans /var/www/html
COPY ./tasks /var/www/html

# Active mod_rewrite
RUN a2enmod rewrite

# Ajoute index.php comme page d'accueil par défaut
RUN echo 'DirectoryIndex index.php' >> /etc/apache2/apache2.conf

# Donne les bons droits à Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
