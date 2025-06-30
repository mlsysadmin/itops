#!/bin/bash

echo "Composer install"
composer install

echo "copy .env"
cp .env.example .env

echo "Generate key"
php artisan key:generate

echo "Migrate"
php artisan migrate

echo "nmp install"
npm install

echo "Downloading dependencies is complete. Run the command 'php artisan serve' to start the server. Run the command 'npm run dev' to start the front-end server."
echo "Run those commands in separate terminal windows."
