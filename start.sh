#!/bin/bash
set -e

# Ensure database directory exists and is writable
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database/database.sqlite
chmod -R 775 /var/www/html/database

# Run migrations as root to ensure full environment access
php artisan migrate --force

# Reset permissions for Laravel folders
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Force logging to stderr for Render
export LOG_CHANNEL=stderr

# Start Apache
echo "Starting Apache..."
apache2-foreground
