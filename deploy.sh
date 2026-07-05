#!/usr/bin/env bash
# ============================================================
# Selly Laundry — Auto Deploy (zero-downtime, self-healing)
#
# Dipanggil oleh deploy-watch.sh (cron) saat webhook GitHub memicu update.
#
# Prinsip:
#   * TANPA maintenance panjang: asset di-build saat situs masih online.
#   * Cache dibersihkan tepat setelah git reset supaya kode baru konsisten
#     (menghindari 500 "Undefined variable" akibat route/view cache lama).
#   * migrate dijalankan sebelum build berat agar skema & kode sinkron.
#   * `trap ... EXIT` menjamin `php artisan up` selalu dipanggil — situs tidak
#     akan pernah nyangkut di mode maintenance meski ada langkah gagal.
#
# Seluruh isi dibungkus main(){...} lalu dipanggil di akhir, supaya bash sudah
# membaca penuh script SEBELUM `git reset` (yang bisa mengubah file ini).
# ============================================================

main() {
    set -euo pipefail

    APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    APP_USER="www"
    BRANCH="main"

    # PATH aman untuk cron (php/composer/node). Sesuaikan bila versi berbeda.
    export PATH="/usr/local/bin:/usr/bin:/bin:/www/server/nodejs/current/bin:$PATH"

    cd "$APP_DIR"
    git config --global --add safe.directory "$APP_DIR" 2>/dev/null || true

    # Jaring pengaman: apa pun yang terjadi, jangan tinggalkan situs "down".
    trap 'php artisan up >/dev/null 2>&1 || true' EXIT

    log() { echo -e "\n==> $1"; }

    # Bersihkan sisa maintenance dari run sebelumnya (jika ada).
    php artisan up >/dev/null 2>&1 || true

    log "[1/7] Menarik kode terbaru (branch: $BRANCH)"
    git fetch origin "$BRANCH"
    git reset --hard "origin/$BRANCH"

    log "[2/7] Composer (produksi)"
    composer install --no-dev --optimize-autoloader --no-interaction

    log "[3/7] Bersihkan cache lama (agar kode baru konsisten)"
    php artisan optimize:clear

    log "[4/7] Migrasi database"
    php artisan migrate --force

    log "[5/7] Build asset Vite (situs tetap ONLINE)"
    npm ci
    npm run build

    log "[6/7] Bangun ulang cache (optimasi)"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    log "[7/7] Perbaiki kepemilikan & permission"
    chown -R "$APP_USER":"$APP_USER" "$APP_DIR" 2>/dev/null || true
    chmod -R ug+rwx "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

    php artisan queue:restart >/dev/null 2>&1 || true

    log "Deploy selesai pada $(date '+%Y-%m-%d %H:%M:%S')"
}

main "$@"
