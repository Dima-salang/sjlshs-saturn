#!/bin/sh
set -e

cd /var/www/html

# Ensure storage directories exist with correct permissions
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# If using SQLite, ensure the database file exists
if [ "${DB_CONNECTION}" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-database/database.sqlite}"
    if [ ! -f "$DB_PATH" ]; then
        touch "$DB_PATH"
        chown www-data:www-data "$DB_PATH"
    fi
fi

# Run as www-data from this point onwards
su-exec www-data sh -c "
    # Cache config & routes for performance
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    # Run migrations
    php artisan migrate --force --no-interaction
"

# Start all processes via supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
