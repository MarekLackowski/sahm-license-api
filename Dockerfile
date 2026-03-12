FROM php:8.2-apache

# Instaluj rozszerzenia PDO MySQL
RUN docker-php-ext-install pdo_mysql

# Kopiuj pliki do serwera Apache
COPY . /var/www/html/

# Ustaw uprawnienia
RUN chown -R www-data:www-data /var/www/html