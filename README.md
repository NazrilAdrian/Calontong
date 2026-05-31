# 🛒 Ca'lontong — Sistem Informasi Manajemen Warung Kelontong

> Proyek Akhir Semester — Pemrograman Berbasis Web (PHP & MySQL)

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat&logo=bootstrap&logoColor=white)
![Status](https://img.shields.io/badge/Status-In%20Development-orange?style=flat)

---

## 📋 Deskripsi Proyek

**Ca'lontong** adalah sistem informasi manajemen internal berbasis web yang dirancang khusus untuk digitalisasi operasional harian **Warung Ananta**. Nama "Ca'lontong" diambil dari kombinasi kata "Catatan" atau "Aplikasi" dan "Kelontong", yang merepresentasikan fungsi utama sistem sebagai media pencatatan digital usaha warung kelontong keluarga.

Berbeda dengan aplikasi e-commerce pada umumnya, Ca'lontong bersifat **internal bisnis (Back-Office System)**. Sistem ini tidak menyediakan halaman untuk pelanggan umum, melainkan sepenuhnya digunakan oleh internal keluarga pemilik Warung Ananta melalui smartphone agar pengelolaan stok, harga barang, pencatatan hutang, dan transaksi dapat dilakukan secara terstruktur, transparan, dan efisien.

**Masalah yang dipecahkan:**

- Stok dan harga barang hanya diingat atau dicatat seadanya — anggota keluarga harus menelepon pemilik hanya untuk menanyakan harga
- Struk pembelian dari agen sering hilang atau rusak sehingga riwayat harga modal tidak tersimpan
- Pencatatan hutang pelanggan (kasbon tetangga) dilakukan di buku fisik yang rawan terlewat, rusak, atau hilang
- Tidak ada rekap penjualan harian yang akurat dan cepat

---

## 👥 Anggota Kelompok

| #   | Nama                         | NIM           | Modul                                  | 
| --- | ---------------------------- | ------------- | -------------------------------------- | 
| A   | Rafli Rizqi Fadillah         | 2410631170099 | Auth & Manajemen Pengguna              | 
| B   | Defry Ananta Perangin Angin  | 2410631170066 | Manajemen Produk & Kategori            | 
| C   | Nazril Adrian                | 2410631170097 | Transaksi Penjualan                    |
| D   | Muhammad Rizky Rajabi        | 2410631170039 | Supplier & Pembelian (Restock)         | 
| E   | Syahid Ahmad Yasin           | 2410631170170 | Manajemen Hutang Pelanggan             | 

> **Dosen Pengampu:** Kamal Prihandani, S.Kom., M.Kom.
> **Program Studi:** Informatika — Fakultas Ilmu Komputer, Universitas Singaperbangsa Karawang

---

## ✨ Fitur Aplikasi

### Fitur Utama (MVP)

- 🔐 **Autentikasi** — Login/logout dengan session PHP, password di-hash dengan `password_hash()`
- 👥 **Manajemen Pengguna** — CRUD akun + assign role (khusus Owner)
- 📦 **Manajemen Produk** — CRUD lengkap + pencarian cepat via HP + indikator stok kritis
- 🗂️ **Manajemen Kategori** — CRUD kategori produk (10 kategori sesuai kondisi warung)
- 🧾 **Transaksi Penjualan** — Input transaksi, pilih produk, hitung kembalian otomatis, stok berkurang otomatis
- 📜 **Riwayat Transaksi** — Daftar transaksi, detail per nota, filter berdasarkan role (kasir hanya lihat transaksi sendiri)
- 🚚 **Supplier & Restock** — CRUD data agen + catat restock (menggantikan struk fisik), stok bertambah otomatis
- 💸 **Manajemen Hutang Pelanggan** — Catat hutang, catat pembayaran sebagian/lunas, status otomatis berubah
- 📊 **Dashboard Per Role** — Statistik bisnis untuk Owner/Admin; halaman kerja harian untuk Kasir

### Fitur Bonus

- 🎭 **Multi-Role Access** — Menu dan hak akses berbeda: `owner` / `admin` / `kasir`
- 🔍 **Search & Filter** — Pencarian produk berdasarkan nama (kebutuhan utama warung)
- 📋 **Log Login** — Riwayat aktivitas login seluruh pengguna (khusus Owner)
- 🖼️ **Upload Foto Produk** — Form produk menerima file gambar
- 📥 **Export PDF / Excel** — Cetak laporan stok, penjualan, restock, dan hutang

---

## 🎭 Sistem Role & Pengguna Nyata

| Role Sistem | Pengguna Nyata         | Hak Akses                                                                                           |
| ----------- | ---------------------- | --------------------------------------------------------------------------------------------------- |
| **Owner**   | Ayah & Ibu (Pemilik)   | Akses penuh — laporan keuntungan, total hutang aktif, manajemen semua akun, seluruh fitur sistem   |
| **Admin**   | Anak Pertama           | Kelola produk, kategori, transaksi, supplier, restock, hutang, dan laporan — tidak bisa kelola user |
| **Kasir**   | Anak Kedua             | Buat transaksi, cari harga produk real-time, catat hutang pelanggan — akses terbatas               |

---

## 🛠️ Tech Stack

| Komponen        | Teknologi                    |
| --------------- | ---------------------------- |
| Backend         | PHP Native (tanpa framework) |
| Database        | MySQL 8.0                    |
| Frontend        | HTML5 + CSS3 + Bootstrap 5   |
| PDF Export      | FPDF / TCPDF                 |
| Local Dev       | XAMPP / Laragon              |
| Version Control | Git + GitHub                 |

---

## 🗂️ Struktur Folder

```
calontong/
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
│   ├── dashboard.php                # Dashboard berbeda per role
│   │
│   ├── users/                       # Modul 1 — Rafli
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── categories/                  # Modul 2 — Defry
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── products/                    # Modul 2 — Defry
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── transactions/                # Modul 3 — Nazril
│   │   ├── kasir.php                # Halaman input transaksi baru
│   │   ├── index.php                # Riwayat transaksi
│   │   ├── detail.php               # Detail satu nota
│   │   └── batal.php                # Batalkan transaksi
│   │
│   ├── suppliers/                   # Modul 4 — Rajabi
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── purchases/                   # Modul 4 — Rajabi
│   │   ├── index.php                # Riwayat restock
│   │   ├── tambah.php               # Catat restock baru
│   │   ├── detail.php               # Detail satu restock
│   │   └── hapus.php
│   │
│   ├── customers/                   # Modul 5 — Syahid
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── debts/                       # Modul 5 — Syahid
│   │   ├── index.php                # Daftar hutang
│   │   ├── tambah.php               # Catat hutang baru
│   │   ├── detail.php               # Detail hutang + catat pembayaran
│   │   └── hapus.php
│   │
│   └── reports/
│       ├── index.php                # Halaman laporan dengan filter
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

Database: `calontong` — **11 tabel** dengan relasi foreign key.

```
users ──────────────┬──────────────────┬───────────────────┐
                    ↓                  ↓                   ↓
kategori ──→ produk ──┬─→ detail_transaksi ←── transaksi  │
                      ├─→ detail_pembelian ←── pembelian ←─┘
                      └─→ detail_hutang   ←── hutang ←── pelanggan
                                                    ↑
                                                  users
supplier ──→ pembelian
```

| Tabel                | Fungsi                                          | Modul |
| -------------------- | ----------------------------------------------- | ----- |
| `users`              | Akun login + role (owner/admin/kasir)           | 1     |
| `kategori`           | Kategori produk                                 | 2     |
| `produk`             | Data barang, stok, harga beli & jual, foto      | 2     |
| `transaksi`          | Header nota penjualan                           | 3     |
| `detail_transaksi`   | Rincian item per nota penjualan                 | 3     |
| `supplier`           | Data agen/pemasok barang                        | 4     |
| `pembelian`          | Header catatan restock dari supplier            | 4     |
| `detail_pembelian`   | Rincian produk dalam satu restock               | 4     |
| `pelanggan`          | Data pelanggan yang pernah berutang             | 5     |
| `hutang`             | Header catatan hutang dan status pembayaran     | 5     |
| `detail_hutang`      | Rincian produk dalam satu catatan hutang        | 5     |

---

## ⚙️ Cara Instalasi & Menjalankan

### Prasyarat

- XAMPP / Laragon / MAMP sudah terinstall
- PHP >= 7.4
- MySQL >= 5.7

### Langkah-langkah

**1. Clone repository**

```bash
git clone https://github.com/[username]/calontong.git
```

Letakkan folder di dalam `htdocs/` (XAMPP) atau `www/` (Laragon).

**2. Import database**

Buka phpMyAdmin di browser:

```
http://localhost/phpmyadmin
```

- Buat database baru bernama `calontong`
- Klik tab **Import**
- Pilih file `calontong.sql`
- Klik **Go**

**3. Konfigurasi koneksi**

Buka file `includes/koneksi.php` dan sesuaikan:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // sesuaikan
define('DB_PASS', '');           // sesuaikan
define('DB_NAME', 'calontong');
```

**4. Jalankan aplikasi**

```
http://localhost/calontong
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
- Semua halaman dilindungi dengan `auth_check.php` (cek session)
- Validasi input dilakukan di **sisi server (PHP)**, bukan hanya HTML `required`
- Hak akses per fitur dikontrol berdasarkan `$_SESSION['role']`
- File sensitif (`.php` di `/includes`) tidak bisa diakses langsung dari URL

---

## 📋 Pembagian Kerja Tim

| Anggota | Modul                          | Entitas Database                          | CRUD Utama                        |
| ------- | ------------------------------ | ----------------------------------------- | --------------------------------- |
| Rafli   | Auth + Manajemen Pengguna      | `users`                                   | CRUD Users + Auth                 |
| Defry   | Manajemen Produk & Kategori    | `produk`, `kategori`                      | CRUD Produk + CRUD Kategori       |
| Nazril  | Transaksi Penjualan            | `transaksi`, `detail_transaksi`           | CRUD Transaksi                    |
| Rajabi  | Supplier & Pembelian (Restock) | `supplier`, `pembelian`, `detail_pembelian` | CRUD Supplier + CRUD Pembelian  |
| Syahid  | Manajemen Hutang Pelanggan     | `pelanggan`, `hutang`, `detail_hutang`    | CRUD Pelanggan + CRUD Hutang      |

---

## 🔑 Matriks Hak Akses

| Modul / Fitur                  | Owner | Admin | Kasir        |
| ------------------------------ | :---: | :---: | :----------: |
| Login & Logout                 | ✅    | ✅    | ✅           |
| Manajemen Pengguna             | ✅    | ❌    | ❌           |
| Produk (Lihat & Cari)          | ✅    | ✅    | ✅           |
| Produk (Tambah/Edit/Hapus)     | ✅    | ✅    | ❌           |
| Transaksi (Buat)               | ✅    | ✅    | ✅           |
| Transaksi (Edit/Hapus)         | ✅    | ✅    | ❌           |
| Riwayat Transaksi              | ✅    | ✅    | Milik sendiri |
| Supplier & Restock             | ✅    | ✅    | ❌           |
| Hutang (Lihat & Catat)         | ✅    | ✅    | ✅           |
| Hutang (Edit/Hapus)            | ✅    | ✅    | ❌           |
| Dashboard Statistik Bisnis     | ✅    | ✅    | ❌           |
| Dashboard Kasir                | ✅    | ✅    | ✅ (berbeda)  |
| Laporan & Export               | ✅    | ✅    | ❌           |
| Log Login                      | ✅    | ❌    | ❌           |

---

## 📊 Status Pengerjaan

| Sprint                | Scope                                           | Status      |
| --------------------- | ----------------------------------------------- | ----------- |
| Sprint 1 (Hari 1–4)   | Auth, layout dasar, koneksi DB                  | 🔵 Todo     |
| Sprint 2 (Hari 5–10)  | CRUD Produk, Kategori, User, Supplier           | 🔵 Todo     |
| Sprint 3 (Hari 11–16) | Transaksi, Restock, Hutang, Dashboard           | 🔵 Todo     |
| Sprint 4 (Hari 17–18) | Fitur Bonus (export, upload foto, log login)    | ⚪ Opsional |
| Testing (Hari 19–20)  | QA, test security, test import SQL              | 🔵 Todo     |
| Submit (Hari 21)      | ZIP, SQL final, laporan PDF                     | 🔵 Todo     |

---

## 🔀 Konvensi Commit Message

| Prefix       | Kapan dipakai                              |
| ------------ | ------------------------------------------ |
| `feat:`      | Menambah fitur baru                        |
| `fix:`       | Memperbaiki bug                            |
| `style:`     | Perubahan CSS / tampilan saja              |
| `refactor:`  | Refactor kode tanpa mengubah fungsi        |
| `docs:`      | Update dokumentasi / README                |

---

## 📁 Berkas yang Dikumpulkan

- [ ] Source code dalam format `.zip` atau link GitHub repo
- [ ] File `calontong.sql` lengkap dengan data dummy (siap import ulang)
- [ ] Laporan PDF (5–10 halaman): deskripsi kasus, ERD, pembagian tugas, screenshot
- [ ] Presentasi & demo langsung di hadapan dosen

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik — Tugas Akhir Semester mata kuliah Pemrograman Berbasis Web, Universitas Singaperbangsa Karawang.
