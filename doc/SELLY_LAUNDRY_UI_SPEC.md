# Selly Laundry — UI/UX Design Spec

| | |
|---|---|
| **Versi** | 1.0 |
| **Gaya** | Marketplace-style (Shopee-like), mobile-first, warna cerah |
| **Frontend** | Livewire 3 + Tailwind + Alpine, PWA |

---

## 1. Prinsip desain

- **Mobile-first & PWA**: dirancang untuk layar 360–430px, installable, terasa seperti app native.
- **Cerah & ramah**: warna brand dominan, kartu rounded, ikon flat berwarna, banyak whitespace.
- **Pola marketplace**: header + search, banner promo, grid kategori berikon, kartu status order, bottom navigation tetap.
- **Jelas soal harga**: karena harga kiloan bisa berubah, selalu beri label "estimasi" vs "final" yang eksplisit.
- **Bukan jiplakan**: ambil pola informasi marketplace, tapi pakai identitas warna & radius sendiri agar tidak terlihat seperti template default.

---

## 2. Design tokens

### 2.1 Warna

| Token | Hex | Penggunaan |
|---|---|---|
| `--selly-primary` | `#0EA5A4` (teal) | Warna brand utama, header, tombol utama |
| `--selly-primary-dark` | `#0B7E7D` | Hover/aktif, teks pada bg terang |
| `--selly-primary-soft` | `#E1F5F4` | Latar lembut, chip aktif |
| `--selly-accent` | `#FFB020` (kuning hangat) | CTA sekunder, badge promo, highlight |
| `--selly-coral` | `#FF6B57` | Flash promo, label diskon, status urgent |
| `--selly-success` | `#16A34A` | Status selesai/sukses |
| `--selly-warning` | `#F59E0B` | Menunggu konfirmasi |
| `--selly-danger` | `#DC2626` | Error, batal |
| `--selly-bg` | `#F4FBFB` | Latar halaman |
| `--selly-surface` | `#FFFFFF` | Kartu |
| `--selly-text` | `#1F2937` | Teks utama |
| `--selly-text-muted` | `#6B7280` | Teks sekunder |

Teal dipilih karena berasosiasi dengan air & kebersihan (cocok untuk laundry), dengan aksen kuning hangat agar tetap "cerah".

### 2.2 Tipografi

- Font: Inter / Plus Jakarta Sans (lokal, ramah Bahasa Indonesia).
- Skala: H1 22px/600, H2 18px/600, body 15px/400, caption 13px/400, label tombol 15px/600.

### 2.3 Bentuk & spasi

- Radius: kartu `16px`, tombol `12px`, chip `999px` (pill), input `12px`.
- Bayangan: halus (`0 2px 8px rgba(0,0,0,0.06)`), tidak berat.
- Grid kategori: ikon 56px dalam lingkaran berwarna lembut, label 12px di bawah.

### 2.4 Komponen inti

Tombol (primary/secondary/ghost), kartu layanan, kartu status order, chip kategori, banner carousel, bottom sheet (untuk pilih slot/alamat), stepper status order (timeline vertikal), empty state, skeleton loading, toast notifikasi.

---

## 3. Navigasi

Bottom navigation tetap (5 ikon):

| Ikon | Label | Tujuan |
|---|---|---|
| `ti-home` | Beranda | Home |
| `ti-list-check` | Pesanan | Daftar & tracking order |
| `ti-plus` (FAB tengah, menonjol) | Pesan | Buat order baru |
| `ti-ticket` | Promo | Voucher & promo |
| `ti-user` | Akun | Profil & pengaturan |

Tombol "Pesan" di tengah dibuat menonjol (FAB) karena aksi utama.

---

## 4. Halaman pelanggan (page-by-page)

### 4.1 Splash & onboarding
- Logo Selly Laundry di tengah, latar teal.
- 2–3 slide onboarding ringkas (pickup mudah, harga transparan, tracking real-time).
- CTA "Mulai" → halaman auth. Prompt "Tambah ke Layar Utama" muncul di sesi berikutnya (penting untuk iOS push).

### 4.2 Login / Registrasi / OTP
- Input nomor HP → kirim OTP (WhatsApp/SMS). Alternatif email/password.
- Layar OTP: 4–6 kotak digit, auto-focus, timer kirim ulang.
- Validasi jelas, error inline. Rate limit di server.

### 4.3 Beranda (Home)
Susunan dari atas:
1. **Header**: salam + nama, lokasi/outlet terpilih, ikon notifikasi.
2. **Search bar**: cari layanan.
3. **Banner carousel**: promo (auto-slide).
4. **Grid kategori layanan**: ikon berwarna (Cuci Kiloan, Cuci-Setrika, Setrika, Express, Dry Clean, Sepatu, Bed Cover, Lainnya).
5. **CTA besar "Jadwalkan Pickup"** (tombol primary lebar).
6. **Kartu order berjalan** (jika ada): nomor order, status + progress, tombol "Lacak".
7. **Section "Promo Hari Ini"**: kartu voucher horizontal-scroll.
8. **Layanan populer**: kartu layanan.

### 4.4 Daftar layanan per kategori
- Header kategori + filter (tier kecepatan).
- Daftar kartu layanan: nama, ikon, harga (`Rp x/kg` atau `Rp x/pcs`), estimasi durasi, badge "Min 3 kg" bila ada.

### 4.5 Detail layanan
- Nama, deskripsi, harga, durasi.
- Pilih **tier kecepatan** (Reguler/Express/Kilat) → menampilkan perubahan harga.
- Pilih **parfum/treatment** (opsional).
- Untuk kiloan: input **perkiraan berat** (stepper 0,5 kg) + catatan "harga final setelah ditimbang".
- Untuk satuan: input **jumlah item**.
- Tombol "Tambah ke pesanan".

### 4.6 Keranjang / ringkasan pesanan
- Daftar item (layanan, qty/estimasi berat, harga estimasi).
- Subtotal estimasi, ongkir (sementara), voucher, total estimasi.
- Banner info: "Total final dihitung setelah cucian ditimbang."
- Tombol "Lanjut atur jadwal".

### 4.7 Atur jadwal & alamat (pickup + delivery)
- Pilih **alamat** (bottom sheet: daftar alamat tersimpan / tambah baru dengan peta).
- Pilih **tanggal & slot pickup** (slot penuh disabled).
- Pilih **tanggal & slot delivery**.
- Tampilkan estimasi ongkir berbasis jarak.

### 4.8 Pilih pembayaran / checkout
- Ringkasan akhir (estimasi).
- Pilih **mode**: bayar setelah ditimbang (default) atau prabayar estimasi.
- Jika prabayar: pilih metode (QRIS, e-wallet, VA, kartu) → redirect/Snap gateway.
- Konfirmasi → order dibuat, masuk ke layar status.

### 4.9 Status & tracking order
- **Timeline vertikal** status (Order dibuat → Pickup → Ditimbang → Diproses → Diantar → Selesai), tahap aktif disorot.
- Saat pickup/delivery aktif: **peta** posisi kurir real-time + ETA.
- Kartu detail: item, harga (estimasi → final saat tersedia), alamat, slot.
- **Banner konfirmasi harga** (bila status `awaiting_price_confirm`): tampilkan berat aktual, foto timbangan, harga final, tombol "Setuju" / "Tolak".
- Tombol bantuan / hubungi outlet.

### 4.10 Riwayat pesanan
- Tab: Berjalan / Selesai / Dibatalkan.
- Kartu order: nomor, tanggal, status, total. Aksi: "Lacak", "Pesan lagi", "Beri rating".

### 4.11 Rating
- Bintang 1–5 + komentar opsional setelah `completed`.

### 4.12 Promo / voucher
- Daftar voucher tersedia + input kode. Detail syarat (min order, periode).

### 4.13 Akun / profil
- Data profil, alamat tersimpan, saldo poin loyalty, preferensi notifikasi, bahasa, logout.
- Kartu poin loyalty dengan progress menuju reward berikutnya.

### 4.14 State khusus
- **Empty state** tiap daftar (ilustrasi + CTA).
- **Loading**: skeleton, bukan spinner penuh layar.
- **Offline**: banner "Anda sedang offline" + konten ter-cache.
- **Error**: pesan ramah + tombol coba lagi.

---

## 5. Layar operator (ringkas)

- **Board antrian** (kolom per status: Diterima → Ditimbang → Proses → Selesai → Siap antar) dengan drag/aksi cepat.
- **Detail order**: tombol "Timbang" (input berat + foto), hitung harga final otomatis, tombol ubah status.
- **Cetak label**: nomor order + QR/barcode.
- Filter per tanggal/slot. Desktop/tablet friendly (lebih lebar dari PWA pelanggan).

---

## 6. Layar kurir (ringkas)

- **Daftar tugas hari ini** dikelompokkan per slot, tipe (pickup/delivery), alamat, jarak.
- **Detail tugas**: tombol Berangkat → Tiba → Selesai, tombol navigasi (deeplink Maps), unggah foto bukti.
- **Indikator offline**: badge bila aksi tersimpan lokal menunggu sinkron.
- Tampilan besar, tombol lebar (dipakai sambil bergerak).

---

## 7. Aset PWA

- `manifest.json`: `name: "Selly Laundry"`, `short_name: "Selly"`, `theme_color: #0EA5A4`, `background_color: #F4FBFB`, ikon 192/512px (maskable), `display: standalone`.
- Service worker: cache app shell + aset statis; strategi network-first untuk data, cache-first untuk aset.
- Prompt install custom (Android `beforeinstallprompt`; iOS instruksi "Add to Home Screen" agar Web Push aktif).
- Splash screen mengikuti theme color.

---

## 8. Aksesibilitas & lokalisasi

- Kontras teks memadai (WCAG AA), target sentuh ≥ 44px.
- Bahasa default Indonesia; siapkan struktur i18n bila perlu English.
- Format angka & rupiah lokal (Rp, pemisah ribuan).
