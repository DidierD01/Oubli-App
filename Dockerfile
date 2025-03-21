# Utilise l'image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# Active le module rewrite d'Apache
RUN a2enmod rewrite

# Autorise l'utilisation des fichiers .htaccess
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Ajoute le nom du serveur pour éviter un avertissement Apache
RUN echo "ServerName metro.proxy.rlwy.net" >> /etc/apache2/apache2.conf

# Installe les extensions PHP nécessaires (mysqli, pdo, pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Crée le dossier de logs Apache et les fichiers de logs
RUN mkdir -p /var/log/apache2/ && \
    touch /var/log/apache2/access.log /var/log/apache2/error.log && \
    chown www-data:www-data /var/log/apache2/access.log /var/log/apache2/error.log

# Définit le répertoire de travail
WORKDIR /var/www/html

# Copie les fichiers nécessaires
COPY tasks/ /var/www/html/tasks/
COPY controllers/ /var/www/html/controllers/
COPY apache-config.conf /etc/apache2/conf-available/custom.conf

# Active la configuration Apache personnalisée
RUN a2enconf custom

# Donne les permissions nécessaires à Apache
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Expose le port 80 pour Apache
EXPOSE 80
