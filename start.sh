#!/bin/bash
set -e

# Move to the correct directory
cd /var/www/html

# Ensure the database directory exists and is writable
mkdir -p database
touch database/database.sqlite
chown -R www-data:www-data database
chmod -R 775 database

# Create a temporary .env if it doesn't exist (Laravel needs it for key:generate)
if [ ! -f .env ]; then
    echo "Creating .env from example..."
    cp .env.example .env
fi

# Ensure we have a valid APP_KEY in the .env file
# This will write the key to the .env file which Laravel will definitely pick up
php artisan key:generate --force --no-interaction

# Run migrations
php artisan migrate --force

# Final permission check for storage and cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Force logging to stderr for Render
export LOG_CHANNEL=stderr

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
