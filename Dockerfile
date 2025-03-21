# Dockerfile
FROM php:8.2-apache

# Copie tous les fichiers de ton projet dans le dossier du serveur
COPY . /var/www/html/

# Active mod_rewrite si besoin
RUN a2enmod rewrite

# Change le dossier racine (si ton index est ailleurs)
WORKDIR /var/www/html/app/tasks

EXPOSE 80
