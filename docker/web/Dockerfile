FROM php:8.2-apache

# Instalación de dependencias críticas: LDAP para identidades y Python para MFA [cite: 5, 23]
RUN apt-get update && apt-get install -y \
    libldap2-dev \
    python3 \
    python3-pip \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap session \
    && pip3 install pyotp --break-system-packages

# Asegurar que Apache tenga los permisos correctos en el volumen [cite: 42]
RUN chown -R www-data:www-data /var/www/html