FROM serversideup/php:8.4-cli-alpine AS worker
ENV PHP_OPCACHE_ENABLE=1
USER root
RUN install-php-extensions intl bcmath
COPY --chown=www-data:www-data . /var/www/html
USER www-data
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN rm -rf /var/www/html/.composer/cache

FROM serversideup/php:8.4-fpm-nginx-alpine AS web
ENV PHP_OPCACHE_ENABLE=1
USER root
RUN install-php-extensions intl bcmath
RUN apk add --no-cache \
    nodejs \
    npm
COPY --chown=www-data:www-data . /var/www/html
USER www-data
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN rm -rf /var/www/html/.composer/cache
RUN npm ci \
    && npm run build \
    && rm -rf /var/www/html/.npm
