#!/bin/bash

# Ensure the database file exists
mkdir -p database
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# Start Apache
apache2-foreground
