FROM php:8.2-apache

# Copie tout le projet dans le dossier web
COPY . /var/www/html/

# Active mod_rewrite
RUN a2enmod rewrite

# Change le dossier racine d’Apache vers /app/tasks
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/app/tasks|' /etc/apache2/sites-available/000-default.conf

# Assure-toi que le dossier a les bons droits
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
