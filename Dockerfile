FROM richarvey/nginx-php-fpm:3.1.6

# Set working directory
WORKDIR /var/www/html

# Copy all application files
COPY . .

# Install PHP dependencies via Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js, install dependencies, build assets, and clean up
RUN apk add --no-cache nodejs npm \
    && npm ci \
    && npm run build \
    && rm -rf node_modules

# Image configuration
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel configuration
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Make deployment script executable
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh

CMD ["/start.sh"]
