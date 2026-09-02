# ============================================================
# Stage 1: Node — Frontend Asset Compilation
# ============================================================
FROM node:lts-alpine AS node-build

WORKDIR /app

# Install Node deps (--legacy-peer-deps handles vite/laravel-vite-plugin peer dep)
COPY package.json package-lock.json ./
RUN npm install --legacy-peer-deps

# Copy only files needed for the build
COPY vite.config.js ./
COPY resources/ resources/
COPY public/ public/

# Build Vite/Tailwind assets
RUN npm run build

# ============================================================
# Stage 2: Composer — PHP Dependencies
# ============================================================
FROM composer:2 AS composer-deps

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# ============================================================
# Stage 3: Production Runtime
# ============================================================
FROM php:8.4-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    git curl zip unzip libpng-dev libjpeg-turbo-dev libwebp-dev \
    freetype-dev icu-dev oniguruma-dev libxml2-dev postgresql-dev \
    nginx supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl opcache xml

# Copy Composer binary into production stage
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy Nginx, Supervisor, PHP config from deploy/docker/
COPY deploy/docker/nginx.conf        /etc/nginx/http.d/default.conf
COPY deploy/docker/supervisord.conf  /etc/supervisor/conf.d/supervisord.conf
COPY deploy/docker/php.ini           /usr/local/etc/php/conf.d/99-production.ini

# Set working directory
WORKDIR /var/www/html

# Copy the full application source
COPY . /var/www/html/

# Copy Composer vendor from stage 2
COPY --from=composer-deps /app/vendor /var/www/html/vendor

# Copy pre-compiled Vite frontend assets from stage 1
COPY --from=node-build /app/public/build /var/www/html/public/build

# Dump optimized autoloader now that source is present
RUN composer dump-autoload --optimize --no-dev --no-scripts

# Set correct storage permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy entrypoint script from deploy/docker/
COPY deploy/docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
