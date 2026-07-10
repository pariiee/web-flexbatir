#!/bin/bash

# =============================================================
# Deploy Script - flexbatir.web.id
# Jalankan script ini di VPS setiap kali mau deploy manual,
# atau dipanggil otomatis oleh GitHub Actions.
# =============================================================

set -e  # stop kalau ada error

APP_DIR="/var/www/flexbatir"
PHP="php8.3"

echo "=============================="
echo " Deploying flexbatir.web.id"
echo "=============================="

cd $APP_DIR

# 1. Pull latest code dari GitHub
echo "[1/7] Pulling latest code..."
git pull origin main

# 2. Install/update PHP dependencies (skip dev)
echo "[2/7] Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Clear semua cache dulu
echo "[3/7] Clearing cache..."
$PHP artisan config:clear
$PHP artisan cache:clear
$PHP artisan route:clear
$PHP artisan view:clear
$PHP artisan event:clear

# 4. Jalankan migration (aman, skip kalau tidak ada perubahan)
echo "[4/7] Running migrations..."
$PHP artisan migrate --force

# 5. Optimize untuk production
echo "[5/7] Optimizing for production..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache
$PHP artisan optimize

# 6. Build assets frontend (kalau ada perubahan)
echo "[6/7] Building frontend assets..."
npm ci
npm run build

# 7. Fix permissions storage & cache
echo "[7/7] Fixing permissions..."
chmod -R 775 storage bootstrap/cache

echo ""
echo "=============================="
echo " Deploy selesai!"
echo "=============================="
