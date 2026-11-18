FROM php:8.2-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache port dynamically for Render (defaults to 80 if PORT is not set)
RUN sed -i 's/80/${PORT:-80}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Set file permissions for Apache web server
RUN chown -R www-data:www-data /var/www/html

# Default port
EXPOSE 80

CMD ["apache2-foreground"]
