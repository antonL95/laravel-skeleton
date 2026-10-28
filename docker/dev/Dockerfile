# syntax=docker/dockerfile:1
FROM oven/bun:1.4.3-debian AS bun-source

FROM serversideup/php:8.5-frankenphp-v5.0.3
USER root
ARG PLAYWRIGHT_VERSION=1.64.1
ENV PLAYWRIGHT_BROWSERS_PATH=/ms-playwright
RUN install-php-extensions intl bcmath gd gmp pcov sockets
RUN apt-get update \
 && apt-get install -y --no-install-recommends git procps libnss3-tools jq ghostscript postgresql-common \
 && /usr/share/postgresql-common/pgdg/apt.postgresql.org.sh -y \
 && apt-get install -y --no-install-recommends postgresql-client-18 \
 && rm -rf /var/lib/apt/lists/*
COPY --from=bun-source /usr/local/bin/bun /usr/local/bin/bun
RUN ln -s bun /usr/local/bin/bunx \
 && ln -s bun /usr/local/bin/node
RUN bunx playwright@${PLAYWRIGHT_VERSION} install --with-deps chromium \
 && rm -rf /root/.bun /root/.cache /var/lib/apt/lists/*
RUN mkdir -p /var/www/.pest /var/www/.pki /composer/cache /tmp/rector_cached_files /tmp/phpstan \
 && chown -R www-data:www-data /var/www/.pest /var/www/.pki /composer /ms-playwright /tmp/rector_cached_files /tmp/phpstan
USER www-data
