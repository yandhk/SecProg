#!/bin/bash
set -e

echo "========================================="
echo "🚀 Starting Laravel Application Setup"
echo "========================================="

# 🔥 TAMBAHKAN INI - Force clean & verify nginx config
echo "🔧 Configuring Nginx..."
rm -rf /etc/nginx/sites-enabled/* /etc/nginx/sites-available/* 2>/dev/null || true
rm -f /etc/nginx/conf.d/default.conf 2>/dev/null || true

# Verify nginx config
if ! nginx -t; then
    echo "❌ Nginx configuration is invalid!"
    cat /etc/nginx/conf.d/laravel.conf
    exit 1
fi
echo "✅ Nginx configuration valid"

# Check environment variables
if [ -f /usr/local/bin/check-env.sh ]; then
    /usr/local/bin/check-env.sh
fi

# Wait for database
echo "⏳ Waiting for database connection..."
/usr/local/bin/wait-for-db.sh

# Ensure .env exists - CRITICAL untuk production
if [ ! -f /var/www/html/.env ]; then
    echo "📝 Creating .env from .env.example..."
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
        echo "✅ .env file created from .env.example"
    else
        echo "❌ ERROR: .env.example not found!"
        echo "⚠️  Please create .env file manually or ensure .env.example exists"
        exit 1
    fi
else
    echo "✅ .env file already exists"
fi

# Generate APP_KEY if not set
if ! grep -q "APP_KEY=base64:" /var/www/html/.env 2>/dev/null; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction || {
        echo "❌ Failed to generate APP_KEY"
        exit 1
    }
    echo "✅ APP_KEY generated successfully"
else
    echo "✅ APP_KEY already set"
fi

# Create storage directories if they don't exist
echo "📁 Creating storage directories..."
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Create storage link if needed
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link --force || true
else
    echo "✅ Storage link already exists"
fi

# Fix permissions
echo "🔧 Setting proper permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create session/cache/queue tables if using database driver
if grep -q "SESSION_DRIVER=database" /var/www/html/.env 2>/dev/null; then
    echo "📊 Creating session table..."
    php artisan session:table --force 2>/dev/null || true
fi

if grep -q "CACHE_STORE=database" /var/www/html/.env 2>/dev/null; then
    echo "📊 Creating cache table..."
    php artisan cache:table --force 2>/dev/null || true
fi

if grep -q "QUEUE_CONNECTION=database" /var/www/html/.env 2>/dev/null; then
    echo "📊 Creating queue tables..."
    php artisan queue:table --force 2>/dev/null || true
    php artisan queue:failed-table --force 2>/dev/null || true
fi

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction

# Seed database (optional - uncomment if needed)
echo "🌱 Seeding database..."
php artisan db:seed --force --no-interaction

# Clear and cache config for production
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Final permission fix after all operations
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================="
echo "✅ Application started successfully!"
echo "========================================="

# Execute CMD
exec "$@"