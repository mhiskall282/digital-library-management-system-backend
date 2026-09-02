FROM php:8.3-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git curl zip unzip libpng-dev libjpeg-turbo-dev libwebp-dev \
    freetype-dev icu-dev oniguruma-dev libxml2-dev postgresql-dev \
    nginx supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl opcache xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js for Vite asset compilation
RUN apk add --no-cache nodejs npm

WORKDIR /var/www/html

# ============================================
# Composer dependencies (cached layer)
# ============================================
FROM base AS composer-deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# ============================================
# Node dependencies & asset build
# ============================================
FROM base AS node-build
COPY package.json package-lock.json ./
RUN npm ci
COPY resources/ resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# ============================================
# Production image
# ============================================
FROM base AS production

# Copy Nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy PHP production config
COPY docker/php.ini /usr/local/etc/php/conf.d/99-production.ini

# Copy application
COPY . /var/www/html/

# Copy Composer dependencies
COPY --from=composer-deps /var/www/html/vendor /var/www/html/vendor

# Copy built assets
COPY --from=node-build /var/www/html/public/build /var/www/html/public/build

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy and set entrypoint
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
