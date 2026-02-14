#!/bin/bash

# Ensure the database directory exists and is writable
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database
chown www-data:www-data /var/www/html/database/database.sqlite

# Run migrations as www-data to ensure correct permissions
su -s /bin/bash -c "php artisan migrate --force" www-data

# Ensure logs are visible in Render's dashboard
export LOG_CHANNEL=stderr

# Start Apache
apache2-foreground
