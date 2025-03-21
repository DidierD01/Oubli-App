# Utilise l'image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# Active le module rewrite d'Apache
RUN a2enmod rewrite

# Autorise l'utilisation des fichiers .htaccess
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Ajoute le nom du serveur pour éviter un avertissement Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installe les extensions PHP nécessaires (mysqli, pdo, pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Définit le répertoire de travail
WORKDIR /var/www/html

# Copie le fichier index.php dans le répertoire racine d'Apache
COPY Oubli-App/tasks/index.php /var/www/html/

# Copie le fichier .htaccess s'il est dans app/tasks/
COPY Oubli-App/tasks/.htaccess /var/www/html/

# Donne les permissions nécessaires à Apache
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Crée les fichiers de logs et redirige-les vers la sortie standard
RUN touch /var/log/apache2/access.log /var/log/apache2/error.log && \
    chown www-data:www-data /var/log/apache2/access.log /var/log/apache2/error.log && \
    ln -sf /dev/stdout /var/log/apache2/access.log && \
    ln -sf /dev/stderr /var/log/apache2/error.log

# Expose le port 80 pour Apache
EXPOSE 80
