#!/bin/bash
set -e

# Move to the correct directory
cd /var/www/html

# CLEAR any existing "bad" key from the environment
# This ensures Laravel uses the one we generate in the .env file instead
unset APP_KEY

# Ensure the database directory exists and is writable
mkdir -p database
touch database/database.sqlite
chown -R www-data:www-data database
chmod -R 775 database/database.sqlite
chmod -R 775 database

# Create a fresh .env file for the container
echo "Generating fresh .env..."
cp .env.example .env

# Generate a perfect 32-character key specifically for this container session
php artisan key:generate --force --no-interaction

# Run migrations
php artisan migrate --force

# Final permission check for storage and cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Force logging to stderr for Render
export LOG_CHANNEL=stderr

# Start Apache
echo "Starting Apache... Key generation complete."
exec apache2-foreground
