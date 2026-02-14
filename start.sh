#!/bin/bash
set -e

# Ensure database directory exists and is writable
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database/database.sqlite
chmod -R 775 /var/www/html/database

# Check if APP_KEY is set. If not, generate one.
if [ -z "$APP_KEY" ]; then
    echo "No APP_KEY found, generating one..."
    # Generate a key and export it so Laravel can see it
    export APP_KEY=$(php artisan key:generate --show --no-ansi)
    echo "Generated APP_KEY: $APP_KEY"
fi

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
