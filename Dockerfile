FROM composer:2 AS vendor

WORKDIR /app

COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

FROM dunglas/frankenphp:php8.3-bookworm

WORKDIR /app

RUN install-php-extensions \
    bcmath \
    opcache \
    pcntl \
    pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor --chown=www-data:www-data /app /app
COPY --chown=www-data:www-data docker/entrypoint.sh /usr/local/bin/docker-entrypoint

RUN chmod +x /usr/local/bin/docker-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache \
    && cp vendor/laravel/octane/src/Commands/stubs/frankenphp-worker.php public/frankenphp-worker.php \
    && chown -R www-data:www-data /app

USER www-data

EXPOSE 8001

ENTRYPOINT ["docker-entrypoint"]
CMD ["server"]
