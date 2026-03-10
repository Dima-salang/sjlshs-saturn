# ============================================================
# Stage 1: Composer dependencies
# ============================================================
FROM composer:2.8 AS composer-deps

WORKDIR /app

COPY composer.json composer.lock ./

# Install production PHP dependencies only (no dev)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .

RUN composer dump-autoload --optimize --no-dev

# ============================================================
# Stage 2: Final production image
# ============================================================
FROM php:8.4-fpm-alpine AS production

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    su-exec \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite \
    sqlite-dev \
    libpq-dev \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        pdo_pgsql \
        pgsql \
        mbstring \
        zip \
        exif \
        pcntl \
        gd \
    && rm -rf /var/cache/apk/*

# Install opcache for performance
RUN docker-php-ext-install opcache

WORKDIR /var/www/html

# Copy application files
COPY --from=composer-deps /app /var/www/html

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create SQLite database directory (used when DB_CONNECTION=sqlite)
RUN mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/database

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
