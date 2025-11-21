# ---------- Stage: vendor ----------
FROM php:8.2-cli AS vendor

# Install dependencies dan Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
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

# Copy package files
COPY package*.json ./
RUN npm ci --silent

# Copy necessary files for Vite build
COPY . .

# Build assets dengan Vite
RUN npm run build && \
    echo "Build completed. Checking output:" && \
    ls -lah public/build/ || echo "Build folder not found!"


# ---------- Stage: build ----------
FROM php:8.2-fpm AS build

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy vendor dari stage vendor
COPY --from=vendor /app/vendor ./vendor

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Copy hasil build Vite dari stage frontend
COPY --from=frontend /app/public/build ./public/build

# Set permissions
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    public/storage \
    && chown -R www-data:www-data \
    storage \
    bootstrap/cache \
    public \
    && chmod -R 775 storage bootstrap/cache public


# ---------- Stage: production ----------
FROM php:8.2-fpm AS production

# Install runtime dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    netcat-openbsd \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install opcache dan redis extension untuk performa production
RUN docker-php-ext-install opcache && \
    pecl install redis && \
    docker-php-ext-enable redis

# Configure PHP-FPM untuk production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Custom PHP configuration
COPY docker/php/custom.ini "$PHP_INI_DIR/conf.d/custom.ini"
COPY docker/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# PHP-FPM pool configuration
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Copy built application
COPY --from=build --chown=www-data:www-data /var/www/html /var/www/html

# Copy startup scripts
COPY --chmod=755 docker/wait-for-db.sh /usr/local/bin/wait-for-db.sh
COPY --chmod=755 docker/check-env.sh /usr/local/bin/check-env.sh
COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]