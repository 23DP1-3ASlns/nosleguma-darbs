#!/bin/bash
php artisan config:clear
php artisan migrate --force --seed
php artisan serve --host=0.0.0.0 --port=8080
