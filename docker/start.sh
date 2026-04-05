#!/bin/sh

# Set the port in Nginx config from Railway's $PORT env
sed -i "s/\[PORT_PLACEHOLDER\]/${PORT:-80}/g" /etc/nginx/http.d/default.conf

# ──────────────────────────────────────────────
# 1. OPTIMIZE LARAVEL
# ──────────────────────────────────────────────
echo "[LARAVEL] Optimizing caches..."
# Run migrations if database is ready (optional - user can opt to run manually)
# php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ──────────────────────────────────────────────
# 2. START PYTHON SERVICE
# ──────────────────────────────────────────────
echo "[BRIDGE] Starting Python bridge service..."
# Note: bridge_service.py has hardcoded COM5 and 127.0.0.1:8000
# On Railway, we pass the local app URL. Nginx listens on $PORT.
export PYTHONUNBUFFERED=1
export LARAVEL_URL="http://127.0.0.1:${PORT:-80}"
python3 bridge_service.py >> /var/log/bridge.log 2>&1 &

# ──────────────────────────────────────────────
# 3. START SERVICES
# ──────────────────────────────────────────────
echo "[SERVER] Starting PHP-FPM..."
php-fpm -D

echo "[SERVER] Starting Nginx on port ${PORT:-80}..."
nginx -g "daemon off;"
