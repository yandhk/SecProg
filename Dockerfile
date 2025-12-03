# ---------- Stage: vendor ----------
FROM php:8.2-cli AS vendor

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip \
    && docker-php-ext-install zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-autoloader \
    --no-scripts \
    --no-interaction

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
RUN composer dump-autoload --optimize --no-dev

COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/storage \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public

# ---------- Stage: production ----------
FROM php:8.2-fpm AS production

RUN apt-get update && apt-get install -y \
    nginx supervisor libpng-dev libonig-dev libxml2-dev libzip-dev netcat-openbsd curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP config
COPY docker/php/custom.ini "$PHP_INI_DIR/conf.d/custom.ini"
COPY docker/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html
COPY --from=build --chown=www-data:www-data /var/www/html /var/www/html

# Copy Nginx config
RUN rm -f /etc/nginx/conf.d/default.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy supervisord config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint scripts
COPY --chmod=755 docker/wait-for-db.sh /usr/local/bin/wait-for-db.sh
COPY --chmod=755 docker/check-env.sh /usr/local/bin/check-env.sh
COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

