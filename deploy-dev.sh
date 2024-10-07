#!/bin/bash
set -e

echo "Deployment started ..."

cd /var/www/wolfshop
# Enter maintenance mode or return true
# if already is in maintenance mode
echo "exec: (php artisan down) || true"
(php artisan down) || true

# Pull the latest version of the app
echo "exec: git checkout dev"
git checkout dev
echo "exec: git pull origin dev"
git pull origin dev

# Install composer dependencies
echo "exec: composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
echo "exec: php artisan clear-compiled"
php artisan clear-compiled

# Recreate cache
echo "exec: php artisan optimize"
php artisan optimize

# Run database migrations
echo "exec: php artisan migrate --force"
php artisan migrate --force

# (commeting out => as this are not required for every deployment)

# Run database seeder
# php artisan db:seed

# Run commands
#echo "exec: php artisan app:import-inventory"
#php artisan app:import-inventory

# Exit maintenance mode
echo "exec: php artisan up"
php artisan up

echo "Deployment finished!"
