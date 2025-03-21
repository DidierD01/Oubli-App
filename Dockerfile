FROM php:8.2-apache

# Copie uniquement les fichiers de app/tasks dans /var/www/html
COPY ./tasks /var/www/html

# Active mod_rewrite
RUN a2enmod rewrite

# Autoriser les .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Ajouter index.php par défaut
RUN echo 'DirectoryIndex index.php' >> /etc/apache2/apache2.conf

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
