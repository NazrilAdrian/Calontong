# 🛒 Sistem Informasi Toko / Warung Kelontong (CALONTONG)

> Proyek Akhir Semester — Pemrograman Web Dasar (PHP & MySQL)

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Status](https://img.shields.io/badge/Status-In%20Development-orange?style=flat)

---

## 📋 Deskripsi Proyek

Sistem informasi berbasis web untuk membantu pengelolaan **toko / warung kelontong** secara digital. Sistem ini menggantikan pencatatan manual dengan fitur manajemen produk, transaksi kasir, data pelanggan, dan laporan rekap harian yang terintegrasi.

**Masalah yang dipecahkan:**

- Stok dan transaksi selama ini dicatat manual (buku tulis / kalkulator)
- Tidak ada rekap transaksi harian yang akurat dan cepat
- Sulit mengetahui produk mana yang laris atau stok mana yang hampir habis

---

## 👥 Anggota Kelompok

| #   | Nama             | NIM   | Modul                  | Role    |
| --- | ---------------- | ----- | ---------------------- | ------- |
| A   | [Nama Anggota A] | [NIM] | Auth & User Management | Ketua   |
| B   | [Nama Anggota B] | [NIM] | Produk & Kategori      | Anggota |
| C   | [Nama Anggota C] | [NIM] | Pelanggan & Layout     | Anggota |
| D   | [Nama Anggota D] | [NIM] | Transaksi & Kasir      | Anggota |
| E   | [Nama Anggota E] | [NIM] | Dashboard & Laporan    | Anggota |

---

## ✨ Fitur Aplikasi

### Fitur MVP (Wajib)

- 🔐 **Autentikasi** — Login/logout dengan session PHP, password di-hash dengan `password_hash()`
- 📦 **Manajemen Produk** — CRUD lengkap + upload foto + indikator stok minimum
- 🗂️ **Manajemen Kategori** — CRUD kategori produk
- 👤 **Manajemen Pelanggan** — CRUD data pelanggan tetap
- 👥 **Manajemen User** — CRUD akun + assign role (khusus owner)
- 🧾 **Kasir / Transaksi** — Input nota, pilih produk, hitung kembalian otomatis
- 📜 **Riwayat Transaksi** — Daftar nota, detail per transaksi, filter tanggal
- 📊 **Dashboard Rekap** — Total stok, transaksi hari ini, grafik 7 hari, produk hampir habis

### Fitur Bonus

- 🎭 **Multi-Role Access** — Menu dan hak akses berbeda: `owner` / `admin` / `kasir` (+10 poin)
- 🔍 **Search & Filter** — Cari data berdasarkan kata kunci di semua modul (+5 poin)
- 📄 **Paginasi Data** — Tampilkan N baris per halaman di semua tabel (+5 poin)
- 📥 **Export PDF / Excel** — Cetak laporan stok dan transaksi (+10 poin)
- 🖼️ **Upload Foto Produk** — Form produk menerima file gambar (+10 poin)

---

## 🛠️ Tech Stack

| Komponen        | Teknologi                    |
| --------------- | ---------------------------- |
| Backend         | PHP Native (tanpa framework) |
| Database        | MySQL 8.0                    |
| Frontend        | HTML5 + CSS3                 |
| PDF Export      | FPDF / TCPDF                 |
| Local Dev       | XAMPP / Laragon              |
| Version Control | Git + GitHub                 |

---

## 🗂️ Struktur Folder

```
warung_kelontong/
│
├── index.php                        # Redirect ke login atau dashboard
├── login.php                        # Halaman login
├── logout.php                       # Proses logout & destroy session
├── README.md
│
├── includes/
│   ├── koneksi.php                  # Koneksi database MySQL
│   ├── header.php                   # Navbar + Bootstrap CDN + session check
│   ├── footer.php                   # Closing HTML tags
│   └── auth_check.php               # Proteksi halaman: redirect jika belum login
│
├── pages/
│   ├── dashboard.php                # Rekap statistik & grafik
│   │
│   ├── users/
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── categories/
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── products/
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── customers/
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── transactions/
│   │   ├── kasir.php                # Halaman input transaksi baru
│   │   ├── index.php                # Riwayat transaksi
│   │   ├── detail.php               # Detail satu nota
│   │   └── batal.php                # Batalkan transaksi
│   │
│   └── reports/
│       ├── index.php                # Halaman laporan dengan filter tanggal
│       └── export.php               # Proses export PDF / Excel
│
└── assets/
    ├── css/
    │   └── custom.css
    ├── js/
    │   └── custom.js
    └── uploads/
        └── products/                # Foto produk yang di-upload
```

---

## 🗄️ Database

Database: `calontong` — 6 tabel dengan relasi foreign key.

```
users ──────────────────┐
categories ─────┐       │
                ↓       ↓
            products   transactions ──── transaction_details
                           ↑
customers ─────────────────┘
```

| Tabel                 | Fungsi                                |
| --------------------- | ------------------------------------- |
| `users`               | Akun login + role (owner/admin/kasir) |
| `categories`          | Kategori produk                       |
| `products`            | Data produk, stok, harga, foto        |
| `customers`           | Data pelanggan tetap                  |
| `transactions`        | Header nota transaksi                 |
| `transaction_details` | Rincian item per nota                 |

---

## ⚙️ Cara Instalasi & Menjalankan

### Prasyarat

- XAMPP / Laragon / MAMP sudah terinstall
- PHP >= 7.4
- MySQL >= 5.7

### Langkah-langkah

**1. Clone repository**

```bash
git clone https://github.com/[username]/warung_kelontong.git
```

Letakkan folder di dalam `htdocs/` (XAMPP) atau `www/` (Laragon).

**2. Import database**

Buka phpMyAdmin di browser:

```
http://localhost/phpmyadmin
```

- Buat database baru bernama `warung_kelontong`
- Klik tab **Import**
- Pilih file `warung_kelontong.sql`
- Klik **Go**

**3. Konfigurasi koneksi**

Buka file `includes/koneksi.php` dan sesuaikan:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // sesuaikan
define('DB_PASS', '');           // sesuaikan
define('DB_NAME', 'warung_kelontong');
```

**4. Jalankan aplikasi**

```
http://localhost/warung_kelontong
```

### Akun Default

| Role  | Username | Password      |
| ----- | -------- | ------------- |
| Owner | `owner`  | `password123` |
| Admin | `admin1` | `password123` |
| Kasir | `kasir1` | `password123` |

> ⚠️ Ganti password semua akun setelah pertama kali login.

---

## 🔒 Keamanan yang Diimplementasikan

- Password di-hash menggunakan `password_hash()` dengan algoritma `PASSWORD_DEFAULT`
- SQL Injection dicegah dengan **Prepared Statements** (`mysqli_prepare`)
- Semua halaman admin dilindungi dengan `auth_check.php` (cek session)
- Validasi input dilakukan di **sisi server (PHP)**, bukan hanya HTML `required`
- File sensitif (`.php` di `/includes`) tidak bisa diakses langsung dari URL

---


**Konvensi commit message:**
| Prefix | Kapan dipakai |
|--------|--------------|
| `feat:` | Menambah fitur baru |
| `fix:` | Memperbaiki bug |
| `style:` | Perubahan CSS / tampilan saja |
| `refactor:` | Refactor kode tanpa mengubah fungsi |
| `docs:` | Update dokumentasi / README |

---

## 📋 Status Pengerjaan

| Sprint                | Scope                                  | Status      |
| --------------------- | -------------------------------------- | ----------- |
| Sprint 1 (Hari 1–4)   | Auth, layout dasar, koneksi DB         | 🔵 Todo     |
| Sprint 2 (Hari 5–10)  | CRUD Produk, Kategori, Pelanggan, User | 🔵 Todo     |
| Sprint 3 (Hari 11–16) | Kasir, Riwayat Transaksi, Dashboard    | 🔵 Todo     |
| Sprint 4 (Hari 17–18) | Fitur Bonus                            | ⚪ Opsional |
| Testing (Hari 19–20)  | QA, test security, test import SQL     | 🔵 Todo     |
| Submit (Hari 21)      | ZIP, SQL final, laporan PDF            | 🔵 Todo     |

---

## 📁 Berkas yang Dikumpulkan

- [ ] Source code dalam format `.zip` atau link GitHub repo
- [ ] File `warung_kelontong.sql` lengkap dengan data dummy (siap import ulang)
- [ ] Laporan PDF (5–10 halaman): deskripsi kasus, ERD, pembagian tugas, screenshot
- [ ] Presentasi & demo langsung di hadapan dosen

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik — Tugas Akhir Semester mata kuliah Pemrograman Web Dasar.
