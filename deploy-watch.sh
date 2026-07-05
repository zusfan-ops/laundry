#!/usr/bin/env bash
# ============================================================
# Selly Laundry — Cron watcher untuk auto-deploy via webhook.
#
# Dipanggil cron aaPanel tiap 1 menit. Kalau ada file penanda
# (dibuat oleh public/deploy-webhook.php saat GitHub push), file
# dihapus lalu deploy.sh dijalankan. Ringan saat tidak ada update.
#
# Cron (jalan sebagai root):
#   * * * * * bash /www/wwwroot/sellyclean.net/laundry/deploy-watch.sh >> /www/wwwroot/sellyclean.net/laundry/storage/logs/deploy.log 2>&1
# ============================================================
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TRIGGER="$APP_DIR/storage/app/deploy.trigger"

# Tidak ada penanda -> tidak ada yang perlu dideploy.
[ -f "$TRIGGER" ] || exit 0

# Ambil & hapus penanda dulu (hindari double-run).
rm -f "$TRIGGER"

echo "==> [$(date '+%Y-%m-%d %H:%M:%S')] Webhook memicu deploy"
bash "$APP_DIR/deploy.sh"
