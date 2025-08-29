FROM serversideup/php:8.4-fpm-nginx-alpine

# Enable Opcache
ENV PHP_OPCACHE_ENABLE=1

# Switch to root user for installation tasks
USER root

# Install PHP extensions
RUN install-php-extensions intl bcmath

# Install Node.js
RUN apk add --no-cache \
    nodejs \
    npm

# Copy application code to the container
COPY --chown=www-data:www-data . /var/www/html

# Switch back to www-data user
USER www-data

# Install Composer dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Remove Composer cache
RUN rm -rf /var/www/html/.composer/cache

# Install Node.js dependencies and build assets
RUN npm ci \
    && npm run build \
    && rm -rf /var/www/html/.npm
