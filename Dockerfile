# ──────────────────────────────────────────────
# STAGE 1: COMPOSER (PHP Dependencies)
# ──────────────────────────────────────────────
FROM composer:2.7 as composer_stage
WORKDIR /app
COPY composer.json composer.lock ./
# Note: composer.lock must match composer.json exactly, or run with --ignore-platform-reqs if unsure.
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-dev

# ──────────────────────────────────────────────
# STAGE 2: NODE (Vite Frontend Assets)
# ──────────────────────────────────────────────
FROM node:20-alpine as node_stage
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# ──────────────────────────────────────────────
# STAGE 3: FINAL PRODUCTION IMAGE
# ──────────────────────────────────────────────
FROM php:8.2-fpm-alpine

LABEL name="SmartGate Hybrid Service"
LABEL type="Laravel-Python-Deployment"

# 1. Install system dependencies
# Install Nginx, Python3, and Pip for bridge service
RUN apk add --no-cache \
    nginx \
    python3 \
    py3-pip \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    mysql-client \
    linux-headers \
    zlib-dev \
    oniguruma-dev \
    icu-dev

# 2. Install PHP Extensions (Laravel Essentials)
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# 3. Setup Project Workdir
WORKDIR /var/www/html

# 4. Copy backend and frontend from previous stages
COPY --from=composer_stage /app /var/www/html
COPY --from=node_stage /app/public /var/www/html/public

# 5. Setup Python Requirements for bridge service
COPY requirements.txt .
# Railway uses Alpine, we need to allow system package break if pip refuses
RUN pip install --no-cache-dir -r requirements.txt --break-system-packages || true

# 6. Setup Configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# 7. Configure Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Environment Handling for Railway
# Railway sets $PORT automatically for web traffic
ENV PORT=80
EXPOSE $PORT

# 9. Start Laravel and Python Services via startup script
CMD ["/usr/local/bin/start.sh"]
