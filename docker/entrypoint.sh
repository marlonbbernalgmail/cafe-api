#!/bin/sh

set -eu

cd /app

if [ ! -f vendor/autoload.php ]; then
  composer install \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache
chmod -R ug+rw storage bootstrap/cache || true

if [ "${APP_ENV:-local}" != "production" ]; then
  rm -f bootstrap/cache/packages.php bootstrap/cache/services.php
fi

if [ "${1:-server}" = "server" ]; then
  exec php artisan octane:frankenphp \
    --host="${OCTANE_HOST:-0.0.0.0}" \
    --port="${PORT:-${OCTANE_PORT:-8000}}" \
    --admin-host=0.0.0.0 \
    --admin-port="${OCTANE_ADMIN_PORT:-2019}" \
    --workers="${OCTANE_WORKERS:-auto}" \
    --max-requests="${OCTANE_MAX_REQUESTS:-500}"
fi

exec "$@"
