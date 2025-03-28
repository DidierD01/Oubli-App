# Utilise l'image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# Active le module rewrite (utile pour les routes propres)
RUN a2enmod rewrite

# Désactive totalement les .htaccess (gain de performance)
RUN sed -i 's|AllowOverride All|AllowOverride None|g' /etc/apache2/apache2.conf

# Configure le ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installe les extensions PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configure le répertoire de travail
WORKDIR /var/www/html

# Copie sélective des fichiers (exclut les fichiers inutiles)
COPY compte/ ./compte/
COPY config/ ./config/
COPY controllers/ ./controllers/
COPY css/ ./css/
COPY js/ ./js/
COPY models/ ./models/
COPY public/ ./public/
COPY tasks/ ./tasks/

# Configure les permissions (sécurisé)
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Expose le port 80
EXPOSE 80
