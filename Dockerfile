# ---------- Stage: vendor ----------
FROM php:8.2-cli AS vendor

# Install dependencies dan Composer
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-autoloader --no-scripts --no-interaction

# ---------- Stage: frontend ----------
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci --silent
COPY . .
RUN npm run build

# ---------- Stage: build ----------
FROM php:8.2-fpm AS build

RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev

# Set permissions
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/storage \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public

# ---------- Stage: production ----------
FROM php:8.2-fpm AS production

RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libzip-dev netcat-openbsd curl supervisor nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy Laravel build
COPY --from=build --chown=www-data:www-data /var/www/html /var/www/html

# 🔥 PENTING: Hapus SEMUA config default Nginx
RUN rm -rf /etc/nginx/sites-enabled/* \
    && rm -rf /etc/nginx/sites-available/* \
    && rm -f /etc/nginx/conf.d/default.conf \
    && rm -f /etc/nginx/conf.d/*.conf

# Copy Nginx config untuk Laravel
COPY docker/nginx/default.conf /etc/nginx/conf.d/laravel.conf

# Test nginx config validity
RUN nginx -t || { echo "❌ Nginx config invalid!"; exit 1; }

# Copy supervisord config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint scripts
COPY --chmod=755 docker/wait-for-db.sh /usr/local/bin/wait-for-db.sh
COPY --chmod=755 docker/check-env.sh /usr/local/bin/check-env.sh
COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]