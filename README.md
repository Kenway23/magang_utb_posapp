<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <a href="https://github.com/Kenway23/magang_utb_posapp"><img src="https://img.shields.io/badge/status-active-brightgreen" alt="Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Laravel Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Sistem POS (Point of Sale) — Magang UTB | PT PROGANTARA TEKNOLOGI INDONESIA

Aplikasi **Point of Sale** berbasis web yang dibangun menggunakan **Laravel (PHP)** sebagai bagian dari program magang di **PT PROGANTARA TEKNOLOGI INDONESIA**. Sistem ini dirancang untuk membantu pengelolaan transaksi penjualan, stok barang, dan pelaporan secara digital.

## Fitur Sistem

### 🛍️ Manajemen Produk
Mengelola data produk yang dijual, termasuk nama, harga, kategori, dan stok awal.

### 🗂️ Manajemen Kategori
Mengelompokkan produk ke dalam kategori untuk mempermudah pencarian dan pengelolaan barang.

### 📦 Manajemen Stok
Memantau ketersediaan stok barang secara real-time. Stok otomatis berkurang setiap kali terjadi transaksi penjualan.

### 💳 Transaksi / Kasir
Memproses transaksi penjualan, menghitung total belanja, dan menyelesaikan pembayaran dengan cepat dan akurat.

### 🧾 Cetak Struk / Invoice
Menghasilkan struk atau invoice setelah transaksi selesai sebagai bukti pembayaran untuk pelanggan.

### 📊 Laporan Penjualan
Menampilkan rekap data transaksi penjualan berdasarkan periode tertentu untuk kebutuhan evaluasi dan monitoring bisnis.

### 👥 Manajemen Pengguna & Role
Mengatur akses pengguna berdasarkan peran masing-masing dalam sistem.

## Role Pengguna

| Role | Hak Akses |
|------|-----------|
| **Owner/Pemilik** | Akses penuh: laporan, produk, kategori, stok, dan manajemen pengguna |
| **Kasir** | Memproses transaksi penjualan dan mencetak struk |
| **Gudang** | Mengelola dan memperbarui data stok barang |

## Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP — Laravel Framework |
| Frontend | Blade Template Engine |
| Database | MySQL |
| Build Tool | Vite |

## Cara Instalasi

**1. Clone repositori**
```bash
git clone https://github.com/Kenway23/magang_utb_posapp.git
cd magang_utb_posapp
```

**2. Install dependensi**
```bash
composer install
npm install
```

**3. Salin dan konfigurasi environment**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Sesuaikan konfigurasi database di file `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=posapp_db
DB_USERNAME=root
DB_PASSWORD=
```

**5. Jalankan migrasi dan seeder**
```bash
php artisan migrate --seed
```

**6. Jalankan aplikasi**
```bash
npm run dev
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## Struktur Direktori
magang_utb_posapp/
├── app/
│   ├── Http/Controllers/   # Logika setiap fitur
│   └── Models/             # Model database (Produk, Transaksi, dll)
├── database/
│   ├── migrations/         # Struktur tabel database
│   └── seeders/            # Data awal
├── resources/views/        # Tampilan antarmuka (Blade)
├── routes/web.php          # Routing seluruh halaman
└── public/                 # Asset yang dapat diakses publik

## Informasi Proyek

- **Jenis Proyek:** Aplikasi Web — Point of Sale (POS)
- **Framework:** Laravel
- **Dibuat untuk:** Mini Project Magang
- **Repository:** [https://github.com/Kenway23/magang_utb_posapp](https://github.com/Kenway23/magang_utb_posapp)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
