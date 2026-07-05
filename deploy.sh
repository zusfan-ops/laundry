#!/usr/bin/env bash
# ============================================================
# Selly Laundry - Auto Deploy Script
# Dipanggil oleh aaPanel Git Manager setiap ada webhook dari GitHub.
#
# Cara pakai:
#   1. Taruh file ini di root project (sudah otomatis ada di sini).
#   2. chmod +x deploy.sh
#   3. Di aaPanel: Website -> (situs ini) -> Git Manager -> Script,
#      arahkan ke path file ini, lalu hubungkan ke Webhook URL
#      dan pasang URL tsb di GitHub -> Settings -> Webhooks.
#   4. Sesuaikan APP_USER di bawah jika bukan "www".
# ============================================================

set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_USER="www"
BRANCH="main"

cd "$APP_DIR"

log() { echo -e "\n==> $1"; }

log "[1/9] Masuk mode maintenance"
php artisan down --retry=15 || true

log "[2/9] Menarik perubahan terbaru dari GitHub (branch: $BRANCH)"
git fetch origin "$BRANCH"
git reset --hard "origin/$BRANCH"

log "[3/9] Install dependency PHP (production, tanpa dev)"
composer install --no-dev --optimize-autoloader --no-interaction

log "[4/9] Install & build asset frontend (Vite)"
npm ci
npm run build

log "[5/9] Menjalankan migrasi database"
php artisan migrate --force

log "[6/9] Membersihkan & membangun ulang cache konfigurasi"
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

log "[7/9] Restart queue worker (jika sedang berjalan)"
php artisan queue:restart || true

log "[8/9] Memperbaiki kepemilikan & permission file"
chown -R "$APP_USER":"$APP_USER" "$APP_DIR" || true
chmod -R ug+rwx "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true

log "[9/9] Keluar dari mode maintenance"
php artisan up

log "Deploy selesai pada $(date '+%Y-%m-%d %H:%M:%S')"
