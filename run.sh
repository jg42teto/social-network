#!/bin/bash
set -e

composer install
npm install
npm run dev
php artisan config:cache
php artisan storage:link
while ! (curl -o - $DB_HOST:$DB_PORT &> /dev/null); do sleep 1; done
php artisan migrate
echo "Adding super admin user to the database... (if he's not already added)"
php artisan db:seed --class=SuperAdminSeeder | true # after the first seeding, db constraints will cause the seeding script to fail 
/opt/bitnami/scripts/laravel/run.sh
