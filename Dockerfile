# syntax=docker/dockerfile:1
FROM oven/bun:1 AS bun-source

FROM serversideup/php:8.5-cli AS worker-base
USER root
RUN install-php-extensions intl bcmath gd gmp
COPY --chmod=644 docker/php/zzz-custom-php.ini /usr/local/etc/php/conf.d/
USER www-data

FROM serversideup/php:8.5-frankenphp AS web-base
USER root
RUN install-php-extensions intl bcmath gd gmp
COPY --chmod=644 docker/php/zzz-custom-php.ini /usr/local/etc/php/conf.d/
USER www-data

# vendor: composer deps cached on composer.lock, then full source + scripts
FROM worker-base AS vendor
COPY --chown=www-data:www-data composer.json composer.lock /var/www/html/
RUN --mount=type=cache,target=/composer/cache,uid=33,gid=33 \
    composer install --no-dev --no-interaction --no-scripts --no-autoloader
COPY --chown=www-data:www-data . /var/www/html
RUN --mount=type=cache,target=/composer/cache,uid=33,gid=33 \
    composer install --no-dev --no-interaction --optimize-autoloader

# assets: bun build once for web+ssr; throwaway APP_KEY never leaves this RUN
FROM vendor AS assets
ARG VITE_APP_NAME
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME
ENV BUN_INSTALL_CACHE_DIR=/tmp/bun-cache
COPY --from=bun-source /usr/local/bin/bun /usr/local/bin/bun
RUN --mount=type=cache,target=/tmp/bun-cache,uid=33,gid=33 \
    bun install --frozen-lockfile
RUN printf "APP_KEY=base64:%s\nAPP_ENV=production\nCACHE_STORE=array\nSESSION_DRIVER=array\nQUEUE_CONNECTION=sync\nDB_CONNECTION=sqlite\nLOG_CHANNEL=stderr\n" \
      "$(php -r 'echo base64_encode(random_bytes(32));')" > .env \
 && touch database/database.sqlite \
 && bun run build:ssr \
 && rm -f .env database/database.sqlite

# worker: PHP + vendor only (no bun, no node_modules, no built assets)
FROM worker-base AS worker
ENV PHP_OPCACHE_ENABLE=1
COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html

# web: PHP + built assets (no node_modules, no bun)
FROM web-base AS web
ENV PHP_OPCACHE_ENABLE=1
COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=assets --chown=www-data:www-data /var/www/html/public/build /var/www/html/public/build

# ssr: PHP + ssr bundle + bun + node_modules (Vite SSR externalizes deps at runtime)
FROM web-base AS ssr
ENV PHP_OPCACHE_ENABLE=1
COPY --from=bun-source /usr/local/bin/bun /usr/local/bin/bun
COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=assets --chown=www-data:www-data /var/www/html/bootstrap/ssr /var/www/html/bootstrap/ssr
COPY --from=assets --chown=www-data:www-data /var/www/html/node_modules /var/www/html/node_modules
