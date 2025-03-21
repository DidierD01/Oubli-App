FROM php:8.2-apache

# Copie le contenu dans Apache
COPY . /var/www/html/

# Active mod_rewrite
RUN a2enmod rewrite

# Change le dossier racine d'Apache
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/app/tasks|' /etc/apache2/sites-available/000-default.conf

# Ajoute index.php comme page d'accueil
RUN echo 'DirectoryIndex index.php' >> /etc/apache2/apache2.conf

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
