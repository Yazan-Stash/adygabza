#!/usr/bin/env sh
set -eu

prepare_runtime() {
    mkdir -p \
        storage/app/public \
        storage/database \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache

    if [ "${DB_CONNECTION:-}" = "sqlite" ] && [ -n "${DB_DATABASE:-}" ] && [ "${DB_DATABASE}" != ":memory:" ]; then
        mkdir -p "$(dirname "${DB_DATABASE}")"
        touch "${DB_DATABASE}"
    fi

    php artisan storage:link --force --no-interaction
    php artisan optimize --no-interaction
}

case "${1:-web}" in
    web)
        prepare_runtime
        exec php artisan octane:frankenphp \
            --host="${OCTANE_HOST:-0.0.0.0}" \
            --port="${OCTANE_PORT:-8080}" \
            --workers="${OCTANE_WORKERS:-auto}" \
            --max-requests="${OCTANE_MAX_REQUESTS:-500}" \
            --admin-host="${OCTANE_ADMIN_HOST:-127.0.0.1}" \
            --admin-port="${OCTANE_ADMIN_PORT:-2019}" \
            --caddyfile="${OCTANE_CADDYFILE:-/etc/caddy/Caddyfile}" \
            --log-level="${OCTANE_LOG_LEVEL:-WARN}"
        ;;
    queue)
        prepare_runtime
        exec php artisan queue:work \
            --tries="${QUEUE_TRIES:-3}" \
            --timeout="${QUEUE_TIMEOUT:-90}" \
            --sleep="${QUEUE_SLEEP:-3}" \
            --max-time="${QUEUE_MAX_TIME:-3600}"
        ;;
    schedule)
        prepare_runtime
        exec php artisan schedule:work
        ;;
    migrate)
        prepare_runtime
        exec php artisan migrate --force --no-interaction
        ;;
    artisan)
        shift
        exec php artisan "$@"
        ;;
    *)
        exec "$@"
        ;;
esac
