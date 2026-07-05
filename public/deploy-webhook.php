<?php
/**
 * GitHub webhook receiver for auto-deploy.
 *
 * Alih-alih menjalankan shell dari PHP (sering diblokir aaPanel), endpoint ini
 * hanya MEMVERIFIKASI signature GitHub lalu menulis file penanda. Sebuah cron
 * (deploy-watch.sh) yang berjalan sebagai root akan mendeteksi penanda itu dan
 * menjalankan deploy.sh. Aman meski exec/shell_exec dinonaktifkan.
 *
 * Setup:
 *   1. .env  ->  GITHUB_WEBHOOK_SECRET=isi-rahasia-acak
 *   2. GitHub -> repo -> Settings -> Webhooks:
 *        Payload URL : https://sellyclean.net/deploy-webhook.php
 *        Content type: application/json
 *        Secret      : (sama dengan GITHUB_WEBHOOK_SECRET)
 *        Events      : Just the push event
 *   3. Pasang cron deploy-watch.sh (lihat panduan).
 */

header('Content-Type: text/plain');

// --- Ambil secret dari .env (tanpa bootstrap Laravel penuh) ---
$secret = getenv('GITHUB_WEBHOOK_SECRET') ?: '';
if ($secret === '' && is_file(__DIR__ . '/../.env')) {
    foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES) as $line) {
        if (str_starts_with(trim($line), 'GITHUB_WEBHOOK_SECRET=')) {
            $secret = trim(substr(trim($line), strlen('GITHUB_WEBHOOK_SECRET=')));
            $secret = trim($secret, "\"'");
            break;
        }
    }
}

if ($secret === '') {
    http_response_code(500);
    exit('webhook secret not configured');
}

// --- Verifikasi signature HMAC dari GitHub ---
$payload = file_get_contents('php://input') ?: '';
$received = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if ($received === '' || ! hash_equals($expected, $received)) {
    http_response_code(403);
    exit('invalid signature');
}

// --- Hanya proses push ke branch main ---
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
$data = json_decode($payload, true) ?: [];

if ($event === 'ping') {
    http_response_code(200);
    exit('pong');
}

if ($event !== 'push' || ($data['ref'] ?? '') !== 'refs/heads/main') {
    http_response_code(202);
    exit('ignored (event=' . $event . ')');
}

// --- Tulis penanda; cron yang akan mendeploy ---
$trigger = __DIR__ . '/../storage/app/deploy.trigger';
@file_put_contents($trigger, gmdate('c') . ' ' . ($data['after'] ?? '') . "\n");

http_response_code(200);
echo 'queued for deploy';
