FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean

# Install Composer 2.5
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite and mod_headers
RUN a2enmod rewrite headers

# Set DocumentRoot to /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides and pass Authorization header through
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Add Directory block with Authorization header fix
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1\n\
</Directory>' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY composer.json ./
RUN COMPOSER_AUDIT_ABANDONED=ignore composer install --no-dev --optimize-autoloader

COPY . .