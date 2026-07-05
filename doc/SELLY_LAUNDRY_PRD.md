# Selly Laundry — Product Requirements Document (PRD)

| | |
|---|---|
| **Produk** | Selly Laundry — PWA layanan laundry dengan pickup & delivery |
| **Versi** | 1.0 |
| **Status** | Draft untuk implementasi |
| **Tech stack** | Laravel 11/12, Livewire 3, Tailwind CSS, Alpine.js, MySQL 8, Laravel Reverb, Midtrans/Xendit |
| **Platform** | Progressive Web App (mobile-first), installable di Android & iOS |
| **Dokumen terkait** | `SELLY_LAUNDRY_DATABASE.md`, `SELLY_LAUNDRY_WORKFLOW.md`, `SELLY_LAUNDRY_UI_SPEC.md` |

---

## 1. Ringkasan & tujuan

Selly Laundry adalah aplikasi PWA layanan laundry yang memungkinkan pelanggan memesan layanan cuci dengan penjemputan (pickup) dan pengantaran (delivery) dari genggaman. Tampilannya mengadopsi pola UI marketplace populer (Shopee-style): warna cerah, grid kategori berikon, banner promo, dan navigasi bawah.

**Tujuan bisnis:**
- Memindahkan order dari WhatsApp/manual ke kanal digital yang terstruktur dan terukur.
- Menambah kapasitas order tanpa menambah beban admin (otomatisasi status & notifikasi).
- Membuka kanal promo (voucher, loyalty) untuk meningkatkan retensi pelanggan.
- Menyediakan data operasional (omzet per outlet/layanan/kurir) untuk pengambilan keputusan.

**Masalah utama yang dipecahkan:** harga layanan kiloan belum pasti saat order dibuat. Sistem harus menangani *estimasi → berat aktual → harga final → konfirmasi pelanggan* secara transparan dan dapat diaudit.

---

## 2. Sasaran pengguna & persona

| Persona | Deskripsi | Kebutuhan utama |
|---|---|---|
| **Rina (pelanggan B2C)** | Pekerja kantoran, sibuk, tidak sempat ke outlet | Pickup terjadwal, harga jelas, tracking status real-time |
| **Pak Budi (UMKM/kos B2B)** | Pemilik kos / kafe, order rutin volume besar | Order berulang, invoice, harga khusus |
| **Eko (kurir)** | Mengambil & mengantar cucian | Daftar tugas hari ini, navigasi alamat, bukti foto, mode offline |
| **Siti (operator outlet)** | Menimbang, memproses, QC | Antrian per status, input berat aktual, update status cepat |
| **Owner** | Pemilik bisnis multi-outlet | Laporan omzet, kelola harga/layanan/promo, kontrol outlet |

---

## 3. Ruang lingkup

### In-scope (MVP + v1)
- Registrasi/login pelanggan (OTP WhatsApp/SMS atau email).
- Katalog layanan (kiloan & satuan), penjadwalan pickup & delivery berbasis slot.
- State machine order end-to-end dengan riwayat status.
- Penimbangan berat aktual + konfirmasi harga oleh pelanggan.
- Pembayaran (Midtrans/Xendit): prabayar estimasi atau bayar setelah ditimbang.
- Perhitungan ongkir pickup/delivery berbasis jarak.
- Notifikasi (Web Push + WhatsApp/email) tiap perubahan status.
- Aplikasi kurir (modul dalam PWA yang sama) dengan mode offline.
- Dashboard operator & owner.
- Voucher/promo dan poin loyalty dasar.
- Multi-outlet.

### Out-of-scope (v1)
- Native app (iOS/Android store) — cukup PWA.
- Integrasi mesin cuci IoT / sensor.
- Marketplace multi-merchant (hanya brand Selly).
- Akuntansi penuh (cukup ekspor data transaksi).

---

## 4. Peran & RBAC

Tujuh-langkah hak akses, namun MVP fokus pada lima peran inti:

| Peran | Hak akses ringkas |
|---|---|
| **customer** | Buat order, bayar, tracking, rating, kelola alamat & voucher |
| **courier** | Lihat tugas pickup/delivery, update status, unggah bukti foto |
| **operator** | Terima order di outlet, timbang, input berat aktual, ubah status proses, QC |
| **outlet_admin** | Kelola order outlet, jadwal kurir, harga lokal, lihat laporan outlet |
| **owner / super_admin** | Akses penuh: outlet, layanan, harga global, promo, user, laporan |

Implementasi disarankan pakai `spatie/laravel-permission` dengan scoping `outlet_id` untuk peran outlet.

---

## 5. Model layanan & harga

Sistem harus mendukung dua tipe penetapan harga sejak desain database.

### 5.1 Kiloan (`pricing_type = weight`)
- Dijual per kilogram. Contoh: Cuci Kering, Cuci-Setrika, Setrika Saja.
- Saat order: pelanggan memasukkan **perkiraan berat** → menghasilkan `estimated_price`.
- Saat ditimbang di outlet: operator input `actual_weight` → `final_price`.
- Ada **minimum berat** per layanan (mis. min 3 kg).

### 5.2 Satuan (`pricing_type = unit`)
- Dijual per item dengan harga pasti sejak awal. Contoh: Bed Cover, Jas, Gaun, Sepatu, Karpet, Boneka.
- `final_price` = qty × `unit_price`, tidak berubah saat penimbangan.

### 5.3 Pengubah harga (modifier)
- **Tier kecepatan**: Reguler (x1.0), Express (x1.5), Kilat/1-day (x2.0) — disimpan sebagai `speed_multiplier`.
- **Parfum / treatment tambahan**: opsional, harga tetap per order/item.
- **Ongkir**: pickup + delivery, berbasis jarak dari outlet (gratis di atas threshold tertentu, mis. order > Rp50.000).

### 5.4 Aturan uang
Semua nominal disimpan sebagai **integer rupiah penuh** (`BIGINT UNSIGNED`), bukan float/decimal. Pembulatan harga kiloan mengikuti aturan outlet (mis. dibulatkan ke atas per 0,5 kg).

---

## 6. Kebutuhan fungsional per peran

### 6.1 Pelanggan
- **F-C1** Registrasi & login via OTP (WhatsApp/SMS) atau email/password.
- **F-C2** Melihat katalog layanan per kategori (grid ikon berwarna).
- **F-C3** Membuat order: pilih layanan, qty/estimasi berat, tier kecepatan, parfum.
- **F-C4** Mengelola alamat (dengan titik lokasi/lat-lng) untuk pickup & delivery.
- **F-C5** Memilih slot waktu pickup & delivery.
- **F-C6** Melihat estimasi total (subtotal + ongkir + diskon voucher).
- **F-C7** Membayar (prabayar estimasi atau bayar setelah ditimbang).
- **F-C8** Menerima notifikasi & konfirmasi harga final jika berat berubah signifikan.
- **F-C9** Melacak status order real-time + posisi kurir saat pickup/delivery.
- **F-C10** Melihat riwayat order, mengulang order (re-order), memberi rating.
- **F-C11** Mengklaim/menggunakan voucher; melihat saldo poin loyalty.

### 6.2 Kurir
- **F-K1** Melihat daftar tugas pickup & delivery hari ini (per slot).
- **F-K2** Navigasi ke alamat (deeplink ke Google Maps).
- **F-K3** Update status: berangkat → tiba → barang diambil/diserahkan.
- **F-K4** Unggah bukti foto saat pickup & delivery.
- **F-K5** Bekerja **offline**: aksi tersimpan lokal dan tersinkron saat online (idempoten via `client_uuid`).

### 6.3 Operator outlet
- **F-O1** Melihat antrian order masuk per status (board: Diterima → Ditimbang → Proses → Selesai → Siap antar).
- **F-O2** Input `actual_weight` dan menghitung `final_price`; memicu konfirmasi pelanggan bila selisih melewati ambang.
- **F-O3** Mengubah status proses (cuci, kering, setrika, QC).
- **F-O4** Mencetak/menampilkan label order (nomor + QR/barcode).

### 6.4 Outlet admin & owner
- **F-A1** CRUD layanan, kategori, harga, tier kecepatan, parfum.
- **F-A2** Kelola outlet, kurir, slot kapasitas pickup/delivery.
- **F-A3** Kelola voucher & aturan loyalty.
- **F-A4** Dashboard & laporan: omzet per outlet/layanan/kurir/periode, jumlah order per status, rata-rata waktu proses.

---

## 7. Kebutuhan non-fungsional

- **Performa**: First Contentful Paint < 2,5 dtk di 4G; halaman home interaktif < 3,5 dtk.
- **PWA**: installable (manifest + service worker), offline shell, Web Push.
- **Real-time**: update status & lokasi kurir via WebSocket (Laravel Reverb), latency < 2 dtk.
- **Keamanan**: validasi input ketat, rate limiting login/OTP, otorisasi berbasis policy per resource, tidak ada kredensial hardcoded, webhook pembayaran diverifikasi signature.
- **Audit**: setiap perubahan status order dicatat (`order_status_logs`) dengan aktor & waktu.
- **Skalabilitas**: multi-outlet sejak awal (`outlet_id` di tabel terkait), queue untuk notifikasi & job berat.
- **Ketersediaan data uang**: konsisten integer, tidak ada floating point pada nominal.

---

## 8. Pembayaran

Dua mode disediakan (dapat dikonfigurasi per outlet/layanan):

1. **Bayar setelah ditimbang (disarankan untuk kiloan)** — order dibuat, dijemput, ditimbang, harga final muncul, baru pelanggan membayar sebelum proses/antar.
2. **Prabayar estimasi** — pelanggan bayar estimasi di muka; selisih setelah penimbangan ditagih/di-refund (charge/refund difference) atau dicatat sebagai saldo.

Gateway: Midtrans atau Xendit. Status pembayaran disinkron via **webhook** (verifikasi signature). Mendukung VA, e-wallet (GoPay/OVO/Dana/ShopeePay), QRIS, dan kartu.

---

## 9. Logistik, slot & ongkir

- **Slot waktu**: pickup & delivery memakai slot (mis. 08–10, 10–12, 13–15, 15–17, 18–20) dengan kapasitas terbatas per outlet per slot. Slot penuh tidak bisa dipilih.
- **Penugasan kurir**: otomatis (round-robin/terdekat) atau manual oleh outlet admin.
- **Ongkir**: berbasis jarak (haversine dari outlet ke alamat). Tarif dasar + per km, dengan gratis ongkir di atas threshold order.

---

## 10. Promo, voucher & loyalty

- **Voucher**: kode, tipe (persen/nominal/gratis ongkir), minimum order, kuota, periode, batas per user.
- **Flash promo**: highlight di home (slot "Promo Hari Ini").
- **Loyalty**: poin per rupiah transaksi (mis. 1 poin / Rp1.000), dapat ditukar diskon. Nisab/aturan kedaluwarsa poin opsional.

---

## 11. Notifikasi

Kanal: **Web Push** (utama, dalam app), **WhatsApp** (via gateway, untuk OTP & status penting), **email** (struk/invoice). Pemicu detail ada di `SELLY_LAUNDRY_WORKFLOW.md` (matriks notifikasi). Setiap pelanggan dapat mengatur preferensi kanal.

---

## 12. Metrik sukses (KPI)

- Konversi: % visitor home → order dibuat.
- Order completion rate: % order selesai vs dibatalkan.
- Rata-rata waktu proses (pickup → siap antar).
- Repeat order rate (retensi 30 hari).
- GMV & average order value per outlet.
- Akurasi estimasi berat (selisih estimasi vs aktual) untuk perbaikan UX input berat.

---

## 13. Roadmap bertahap

| Fase | Cakupan |
|---|---|
| **MVP** | Auth, katalog, order kiloan & satuan, slot pickup/delivery, state machine, penimbangan + konfirmasi harga, pembayaran 1 mode, notifikasi push, dashboard operator dasar, 1 outlet |
| **v1** | Multi-outlet, app kurir offline + tracking real-time, voucher, ongkir berbasis jarak, dashboard owner & laporan |
| **v2** | Loyalty poin, B2B/invoice, re-order otomatis (langganan mingguan), rating & review publik, ekspor akuntansi |

---

## 14. Risiko & mitigasi

| Risiko | Mitigasi |
|---|---|
| Selisih berat estimasi vs aktual memicu komplain | Konfirmasi harga wajib bila selisih > ambang; foto timbangan sebagai bukti |
| iOS Web Push butuh "Add to Home Screen" | Onboarding mengarahkan install PWA; fallback WhatsApp untuk notif penting |
| Kurir offline → aksi dobel saat reconnect | Idempotency `client_uuid` + antrean sinkron lokal |
| Penyalahgunaan voucher | Batas per user, kuota global, validasi server-side |
| Konkruensi update status | Status transition tervalidasi server-side (guard pada state machine) |

---

## 15. Lampiran: glosarium

- **Kiloan**: layanan dibayar per kg, berat final ditentukan saat penimbangan.
- **Satuan**: layanan dibayar per item dengan harga tetap.
- **Slot**: rentang waktu pickup/delivery dengan kapasitas terbatas.
- **Idempotency key (`client_uuid`)**: ID unik aksi dari klien untuk mencegah pemrosesan ganda.
