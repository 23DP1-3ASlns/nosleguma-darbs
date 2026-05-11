#!/bin/bash
echo "Clearing config..."
php artisan config:clear
echo "Running migrations..."
php artisan migrate --force --seed
echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=8080
