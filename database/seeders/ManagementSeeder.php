<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Outlet;
use App\Models\PromoBanner;
use App\Models\User;
use Illuminate\Database\Seeder;

class ManagementSeeder extends Seeder
{
    public function run(): void
    {
        // Promo banners shown on landing & home.
        $banners = [
            ['title' => 'Diskon 10% Order Pertama', 'subtitle' => 'Pakai kode SELLY10, hemat sampai Rp15.000.', 'cta_label' => 'Pakai Kode', 'cta_url' => '/promo', 'art' => 'sparkles', 'color_from' => '#FFB020', 'color_to' => '#FF6B57', 'sort_order' => 1],
            ['title' => 'Gratis Ongkir Antar-Jemput', 'subtitle' => 'Untuk order minimal Rp50.000.', 'cta_label' => 'Pesan Sekarang', 'cta_url' => '/layanan', 'art' => 'waves', 'color_from' => '#0EA5A4', 'color_to' => '#0B7E7D', 'sort_order' => 2],
            ['title' => 'Cuci Express 1 Hari', 'subtitle' => 'Cucian selesai dalam 24 jam.', 'cta_label' => 'Coba Express', 'cta_url' => '/layanan', 'art' => 'clothes', 'color_from' => '#6366F1', 'color_to' => '#0EA5A4', 'sort_order' => 3],
        ];
        foreach ($banners as $b) {
            PromoBanner::firstOrCreate(['title' => $b['title']], $b + ['is_active' => true]);
        }

        // Default base salaries & positions for existing staff.
        $salaries = [
            'owner@selly.test' => ['position' => 'Pemilik', 'base_salary' => 12000000],
            'admin@selly.test' => ['position' => 'Admin Outlet', 'base_salary' => 5000000],
            'operator@selly.test' => ['position' => 'Operator Cuci', 'base_salary' => 3500000],
            'kurir@selly.test' => ['position' => 'Kurir', 'base_salary' => 3000000],
        ];
        foreach ($salaries as $email => $data) {
            User::where('email', $email)->update($data + ['hired_at' => now()->subMonths(6)]);
        }

        // Outlet operating hours + Google Maps link for the seeded outlet.
        Outlet::query()->update([
            'opening_hours' => '07.00 - 21.00 (Senin–Minggu)',
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=-7.7626,110.3795',
        ]);

        // FAQ / Q&A shown on the landing page.
        $faqs = [
            ['Bagaimana cara memesan laundry?', 'Pilih layanan di aplikasi, tentukan estimasi berat atau jumlah item, atur jadwal pickup, lalu kurir akan menjemput cucian Anda.', 1],
            ['Bagaimana harga dihitung untuk cucian kiloan?', 'Anda memasukkan perkiraan berat saat memesan. Harga final dihitung setelah cucian ditimbang di outlet, dan Anda akan dimintai konfirmasi bila selisihnya signifikan.', 2],
            ['Apakah ada biaya antar-jemput?', 'Ongkir dihitung berdasarkan jarak. Gratis ongkir untuk order mulai Rp50.000 (dapat berbeda tiap cabang).', 3],
            ['Berapa lama proses laundry?', 'Reguler 2 hari, Express 1 hari, dan Kilat selesai dalam 24 jam tergantung layanan yang dipilih.', 4],
            ['Metode pembayaran apa saja yang didukung?', 'Anda bisa bayar setelah ditimbang atau prabayar estimasi melalui QRIS, e-wallet, virtual account, dan kartu.', 5],
            ['Bagaimana jika hasil cucian kurang memuaskan?', 'Hubungi outlet melalui aplikasi. Kami akan mencuci ulang sesuai kebijakan layanan kami.', 6],
        ];
        foreach ($faqs as [$q, $a, $sort]) {
            Faq::firstOrCreate(['question' => $q], ['answer' => $a, 'sort_order' => $sort, 'is_active' => true]);
        }
    }
}
