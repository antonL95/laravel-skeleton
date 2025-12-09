FROM oven/bun:1 AS bun-source

FROM serversideup/php:8.4-cli AS worker
ENV PHP_OPCACHE_ENABLE=1
USER root
RUN install-php-extensions intl bcmath
COPY --chown=www-data:www-data . /var/www/html
USER www-data
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN rm -rf /var/www/html/.composer/cache

FROM serversideup/php:8.4-frankenphp AS web
ENV PHP_OPCACHE_ENABLE=1
USER root
RUN install-php-extensions intl bcmath
COPY --from=bun-source /usr/local/bin/bun /usr/local/bin/bun
COPY --chown=www-data:www-data . /var/www/html
USER www-data
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN rm -rf /var/www/html/.composer/cache
RUN bun install && bun run build:ssr && rm -rf /var/www/html/.bun

FROM serversideup/php:8.4-cli AS ssr
ENV PHP_OPCACHE_ENABLE=1
USER root
RUN install-php-extensions intl bcmath
COPY --from=bun-source /usr/local/bin/bun /usr/local/bin/bun
COPY --chown=www-data:www-data . /var/www/html
USER www-data
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN rm -rf /var/www/html/.composer/cache
RUN bun install && bun run build:ssr && rm -rf /var/www/html/.bun
