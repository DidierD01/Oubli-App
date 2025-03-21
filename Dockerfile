FROM php:8.2-apache

# Copie tout le projet dans le dossier web d'Apache
COPY . /var/www/html/
COPY app/tasks/.htaccess /var/www/html/app/tasks/.htaccess

# Active le module mod_rewrite
RUN a2enmod rewrite

# ✅ Change le DocumentRoot pour pointer sur /var/www/html/app/tasks
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/app/tasks|' /etc/apache2/sites-available/000-default.conf

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
