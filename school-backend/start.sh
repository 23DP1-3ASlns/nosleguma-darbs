#!/bin/bash
set -e
echo "Running migrations..."
php artisan migrate --force --seed
echo "Migrations done!"
echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=8080
