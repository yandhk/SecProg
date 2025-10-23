#!/bin/sh
set -e

cd /var/www/html

# Install PHP dependencies jika belum ada atau kosong
if [ ! -d "vendor" ] || [ -z "$(ls -A vendor)" ]; then
    composer install
fi

# Buat symlink storage -> public jika belum ada
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# Jalankan migration (opsional, skip kalau sudah ada)
php artisan migrate 

# Jalankan Laravel dev server
php artisan serve --host=0.0.0.0 --port=8000
