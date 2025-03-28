# Utilise l'image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# Active le module rewrite d'Apache
RUN a2enmod rewrite

# Autorise les .htaccess
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Configure le ServerName (mettez votre domaine réel ou localhost)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installe les extensions PHP nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Crée les logs Apache
RUN mkdir -p /var/log/apache2 && \
    touch /var/log/apache2/{access,error}.log && \
    chown www-data:www-data /var/log/apache2/*.log

# Définit le répertoire de travail
WORKDIR /var/www/html

# Copie TOUTE l'application (en excluant les fichiers inutiles via .dockerignore)
COPY . .

# Copie la configuration Apache personnalisée (si elle existe)
COPY apache-config.conf /etc/apache2/conf-available/custom.conf
RUN a2enconf custom

# Configure les permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    chmod -R 755 /var/www/html/tasks # Pour permettre l'exécution via Apache

# Expose le port 80
EXPOSE 80
