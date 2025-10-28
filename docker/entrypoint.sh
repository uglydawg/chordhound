#!/bin/bash
set -e

echo "Starting ChordHound container..."

# Create required directories
mkdir -p /var/log/supervisor
mkdir -p /var/run/php

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run Laravel setup commands as www-data
su - www-data -s /bin/bash -c "cd /var/www/html && php artisan config:cache"
su - www-data -s /bin/bash -c "cd /var/www/html && php artisan route:cache"
su - www-data -s /bin/bash -c "cd /var/www/html && php artisan view:cache"

# Run migrations
su - www-data -s /bin/bash -c "cd /var/www/html && php artisan migrate --force"

echo "✅ ChordHound initialization complete"

# Execute the main command
exec "$@"
