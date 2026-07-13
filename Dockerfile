ARG FRANKENPHP_IMAGE=dunglas/frankenphp:1-php8.4-bookworm

FROM ${FRANKENPHP_IMAGE} AS base

WORKDIR /app

RUN install-php-extensions \
    intl \
    opcache \
    pcntl \
    pdo_mysql \
    pdo_sqlite \
    zip

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

FROM node:22-bookworm-slim AS node

FROM base AS build

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist

RUN php artisan route:clear \
    && npm ci \
    && npm run build \
    && rm -rf node_modules

RUN mkdir -p \
    storage/app/public \
    storage/database \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && ln -snf ../storage/app/public public/storage

FROM base AS runtime

ARG APP_UID=10001
ARG APP_GID=10001

RUN groupadd --gid "${APP_GID}" app \
    && useradd --uid "${APP_UID}" --gid "${APP_GID}" --home-dir /app --shell /usr/sbin/nologin app \
    && mkdir -p /config /data \
    && chown app:app /config /data

COPY --from=build --chown=app:app /app /app
COPY --chown=app:app docker/Caddyfile /etc/caddy/Caddyfile
COPY --chown=app:app docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint

ENV APP_ENV=production \
    OCTANE_SERVER=frankenphp \
    OCTANE_HOST=0.0.0.0 \
    OCTANE_PORT=8080 \
    OCTANE_WORKERS=auto \
    OCTANE_MAX_REQUESTS=500 \
    XDG_CONFIG_HOME=/config \
    XDG_DATA_HOME=/data

USER app:app

EXPOSE 8080

ENTRYPOINT ["entrypoint"]
CMD ["web"]
