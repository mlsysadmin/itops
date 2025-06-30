#!/bin/bash

echo "Filling the database with data..."

echo "Seeding Users..."
php artisan db:seed --class=UserSeeder

echo "Assigning Roles..."
php artisan users:assign-positions

echo "Seeding EOD Logs for users..."
php artisan db:seed --class=EodLogSeeder

echo "Seeding schedules..."
php artisan db:seed --class=AssignShiftsSeeder


echo "Database filled with data!"
echo "You may run again the sh file to re-fill the database with data."

