# 📋 Dokumen Perancangan Sistem Informasi Warung Kelontong
# **Ca'lontong** — Sistem Informasi Manajemen Toko Kelontong

> **Status:** Draft v5 — Penambahan Tabel log_login  
> **Mata Kuliah:** Pemrograman Web Dasar (PHP & MySQL)  
> **Topik:** Sistem Informasi Toko / Warung Kelontong  
> **Objek Nyata:** Warung **Ananta** (warung kelontong keluarga anggota)  
> **Jumlah Anggota:** 5 orang

---

## DAFTAR ISI

1. [Latar Belakang & Deskripsi Proyek](#1-latar-belakang--deskripsi-proyek)
2. [Tujuan Sistem](#2-tujuan-sistem)
3. [Analisis Kebutuhan Fungsional](#3-analisis-kebutuhan-fungsional)
4. [Analisis Kebutuhan Non-Fungsional](#4-analisis-kebutuhan-non-fungsional)
5. [Peran Pengguna (User Roles)](#5-peran-pengguna-user-roles)
6. [Pembagian Modul & Anggota](#6-pembagian-modul--anggota)
7. [Rancangan Database](#7-rancangan-database)
8. [Rancangan Halaman (Sitemap)](#8-rancangan-halaman-sitemap)
9. [Fitur Bonus yang Direncanakan](#9-fitur-bonus-yang-direncanakan)
10. [Rencana Teknologi & Struktur Folder](#10-rencana-teknologi--struktur-folder)
11. [Checklist Kesesuaian Spesifikasi UAS](#11-checklist-kesesuaian-spesifikasi-uas)

---

## 1. Latar Belakang & Deskripsi Proyek

### Latar Belakang

Warung **Ananta** adalah warung kelontong keluarga yang dikelola oleh kedua orang tua beserta dua anaknya. Seperti warung kelontong pada umumnya di Indonesia, pengelolaan warung ini masih dilakukan secara manual, antara lain:

- Pencatatan stok dan harga masih mengandalkan ingatan atau catatan di buku/kertas
- Struk pembelian/restock dari agen sering hilang atau rusak
- Anggota keluarga yang menjaga warung seringkali tidak hafal harga semua produk, sehingga harus menghubungi orang tua saat mereka tidak ada di tempat
- Pencatatan hutang pelanggan (tetangga/warga sekitar) dilakukan secara manual dan rawan terlewat

Karena itu, dibutuhkan sistem informasi berbasis web yang dapat diakses dari **HP** (mobile-friendly) untuk membantu pengelolaan warung secara lebih terstruktur dan efisien.

### Deskripsi Proyek

**Ca'lontong** adalah sistem informasi berbasis web internal bisnis yang dirancang untuk warung **Ananta**. Sistem ini hanya digunakan oleh pihak internal (keluarga pemilik warung) dan bukan untuk pelanggan umum.

Sistem dibangun menggunakan **PHP native** dan **MySQL**, tanpa framework, sesuai ketentuan UAS.

**Pemetaan Role ke Objek Nyata (Warung Ananta):**

| Role Sistem | Pengguna Nyata |
|---|---|
| Owner | Ayah & Ibu (pemilik warung) |
| Admin | Anak pertama (anggota kelompok) |
| Kasir | Anak kedua (adik) |

---

## 2. Tujuan Sistem

- Menyimpan data seluruh produk beserta harga jualnya agar mudah dicari kapan saja dari HP
- Mencatat setiap transaksi penjualan secara digital
- Mencatat pembelian/restock dari supplier/agen agar struk tidak tercecer
- Mengelola data hutang pelanggan (tetangga/warga sekitar) secara terstruktur
- Menyediakan ringkasan data (dashboard dan laporan) untuk memantau kondisi warung
- Memudahkan kolaborasi antar anggota keluarga yang bergantian menjaga warung

---

## 3. Analisis Kebutuhan Fungsional

### 🔐 AUTENTIKASI (Dikerjakan Bersama / Anggota 1)

| Fitur | Deskripsi |
|---|---|
| Login | Masuk dengan username & password |
| Logout | Hapus session dan kembali ke halaman login |
| Proteksi Halaman | Semua halaman hanya bisa diakses setelah login |
| Hak Akses per Role | Menu dan fitur yang tampil disesuaikan per role |
| Hash Password | Password disimpan dengan `password_hash()` |

---

### 👥 MODUL 1 — MANAJEMEN PENGGUNA

| Fitur | Deskripsi | CRUD |
|---|---|---|
| Daftar Pengguna | Lihat semua akun terdaftar beserta role-nya | Read |
| Tambah Pengguna | Buat akun baru (kasir, admin, dll.) | Create |
| Edit Pengguna | Ubah nama, username, role, atau password | Update |
| Hapus Pengguna | Hapus akun dengan konfirmasi | Delete |
| Laporan Modul | Daftar aktivitas login (opsional) | Read |

**Entitas:** `users`

---

### 📦 MODUL 2 — MANAJEMEN PRODUK & KATEGORI

> Kebutuhan utama: semua anggota keluarga bisa **mencari harga barang dengan cepat dari HP**.

| Fitur | Deskripsi | CRUD |
|---|---|---|
| Daftar Produk | Tampilkan semua produk dengan harga dan stok | Read |
| **Pencarian Produk** | Cari produk berdasarkan nama (kebutuhan utama warung) | Read |
| Tambah Produk | Input produk baru: nama, kategori, harga beli, harga jual, stok, satuan | Create |
| Edit Produk | Ubah data produk | Update |
| Hapus Produk | Hapus produk dengan konfirmasi | Delete |
| Daftar Kategori | Tampilkan semua kategori | Read |
| Tambah Kategori | Tambah kategori baru | Create |
| Edit Kategori | Ubah nama kategori | Update |
| Hapus Kategori | Hapus kategori | Delete |
| Peringatan Stok Minimum | Tandai produk yang stoknya di bawah batas minimum | Read |
| **Laporan Stok** | Rekap stok semua produk saat ini | Read |

**Kategori Produk (sesuai wawancara):**
1. Makanan Ringan / Snack
2. Minuman
3. Sembako (Sembilan Bahan Pokok)
4. Bumbu Dapur
5. Produk Instan
6. Produk Kebersihan Rumah Tangga
7. Perawatan / Kebersihan Pribadi (Toiletries)
8. Rokok & Produk Dewasa
9. Gas & Kebutuhan Dapur
10. Obat Ringan / P3K

**Entitas:** `produk`, `kategori`

---

### 💳 MODUL 3 — TRANSAKSI PENJUALAN

| Fitur | Deskripsi | CRUD |
|---|---|---|
| Buat Transaksi | Pilih produk, masukkan jumlah, hitung total & kembalian | Create |
| Riwayat Transaksi | Daftar semua transaksi dengan filter tanggal | Read |
| Detail Transaksi | Lihat rincian satu transaksi | Read |
| Edit Transaksi | Koreksi transaksi (hanya Admin/Owner) | Update |
| Batal Transaksi | Batalkan transaksi dan kembalikan stok | Delete |
| Kalkulasi Kembalian | Input uang bayar, hitung kembalian otomatis | — |
| Stok Otomatis Berkurang | Stok produk berkurang saat transaksi selesai | — |
| **Laporan Penjualan** | Rekap penjualan harian / per periode | Read |

**Entitas:** `transaksi`, `detail_transaksi`

---

### 🚚 MODUL 4 — SUPPLIER & PEMBELIAN (RESTOCK)

> Kebutuhan utama: menggantikan struk restock fisik yang sering tercecer/rusak.

| Fitur | Deskripsi | CRUD |
|---|---|---|
| Daftar Supplier | Tampilkan semua agen/supplier | Read |
| Tambah Supplier | Input data agen baru (nama, kontak) | Create |
| Edit Supplier | Ubah data supplier | Update |
| Hapus Supplier | Hapus supplier | Delete |
| Catat Restock | Input pembelian dari supplier: produk, jumlah, harga beli | Create |
| Riwayat Restock | Daftar semua catatan pembelian/restock | Read |
| Detail Restock | Lihat rincian satu catatan restock | Read |
| Edit Restock | Koreksi catatan restock | Update |
| Hapus Restock | Hapus catatan restock + rollback stok | Delete |
| Stok Otomatis Bertambah | Stok produk bertambah saat restock dicatat | — |
| **Laporan Restock** | Rekap riwayat pembelian per periode atau per supplier | Read |

**Entitas:** `supplier`, `pembelian`, `detail_pembelian`

---

### 💰 MODUL 5 — MANAJEMEN HUTANG PELANGGAN

> Kebutuhan utama: mencatat warga/tetangga yang mengambil barang dulu (ngutang) dan pembayarannya.

| Fitur | Deskripsi | CRUD |
|---|---|---|
| Daftar Pelanggan | Tampilkan semua pelanggan yang pernah tercatat (termasuk yang ngutang) | Read |
| Tambah Pelanggan | Input data pelanggan baru (nama, alamat/keterangan) | Create |
| Edit Pelanggan | Ubah data pelanggan | Update |
| Hapus Pelanggan | Hapus data pelanggan | Delete |
| Catat Hutang Baru | Input hutang baru: pelanggan, produk/keterangan, jumlah, tanggal | Create |
| Daftar Hutang | Tampilkan semua hutang yang masih aktif (belum lunas) | Read |
| Detail Hutang | Lihat rincian hutang satu pelanggan | Read |
| Catat Pembayaran | Tandai sebagian atau seluruh hutang sudah dibayar | Update |
| Hapus / Batalkan Hutang | Hapus catatan hutang (misal salah input) | Delete |
| **Laporan Hutang** | Rekap total hutang aktif per pelanggan | Read |

**Entitas:** `pelanggan`, `hutang`

---

### 📊 DASHBOARD — SATU FILE, DUA TAMPILAN

> File `dashboard.php` adalah satu file yang sama, namun sistem memeriksa `$_SESSION['role']` dan merender konten yang berbeda sesuai role pengguna yang sedang login.

#### Tampilan Owner & Admin (Dashboard Statistik Bisnis)

> Dikerjakan bersama — setiap anggota menyumbang satu kartu ringkasan dari modulnya.

| Kartu / Widget | Data dari Modul |
|---|---|
| Total Produk Aktif | Modul 2 (Produk) |
| Produk Stok Kritis | Modul 2 (Produk) |
| Penjualan Hari Ini (Rp) | Modul 3 (Transaksi) |
| Jumlah Transaksi Hari Ini | Modul 3 (Transaksi) |
| Total Hutang Belum Lunas (Rp) | Modul 5 (Hutang) |
| Total Pelanggan Berutang | Modul 5 (Hutang) |

#### Tampilan Kasir (Dashboard Kasir)

> Fokus pada kemudahan akses ke tugas sehari-hari Kasir. Tidak menampilkan data finansial atau statistik bisnis.

| Elemen | Deskripsi |
|---|---|
| Sapaan personal | "Halo, [Nama Kasir]! Selamat berjaga." |
| Tombol utama | Tombol besar "➕ Buat Transaksi Baru" |
| Pencarian produk cepat | Kolom cari produk langsung di halaman dashboard (tanpa buka halaman lain) — kebutuhan utama: cek harga barang dengan cepat |
| Ringkasan harian | Jumlah transaksi yang dibuat oleh kasir ini hari ini |
| Shortcut menu | Link cepat ke: Riwayat Transaksi Saya, Daftar Produk, Hutang Pelanggan |

---

## 4. Analisis Kebutuhan Non-Fungsional

| Aspek | Ketentuan |
|---|---|
| **Mobile-Friendly** | Tampilan harus bisa digunakan dari HP (prioritas utama sesuai kondisi objek) |
| **Keamanan** | Password di-hash, prepared statement, validasi sisi server |
| **Kemudahan Penggunaan** | Antarmuka sederhana untuk pengguna yang tidak terbiasa teknologi (ortu) |
| **Pencarian Cepat** | Fitur cari produk berdasarkan nama harus responsif dan mudah digunakan |
| **Konsistensi UI** | Navigasi yang sama di seluruh halaman |
| **Performa** | Halaman ringan dan cepat dimuat di jaringan lokal/HP |

---

## 5. Peran Pengguna (User Roles)

| Role | Pengguna Nyata (Warung Ananta) | Akses |
|---|---|---|
| **Owner** | Ayah & Ibu | Akses penuh ke semua fitur |
| **Admin** | Anak pertama | Kelola produk, transaksi, supplier, hutang, laporan. Tidak bisa kelola pengguna lain |
| **Kasir** | Anak kedua (adik) | Buat transaksi, lihat daftar & cari produk, catat/lihat hutang pelanggan. Setelah login, diarahkan ke Dashboard Kasir (tampilan berbeda dari Owner/Admin). |

### Matriks Hak Akses

| Modul / Fitur | Owner | Admin | Kasir |
|---|---|---|---|
| Login & Logout | ✅ | ✅ | ✅ |
| Manajemen Pengguna | ✅ | ❌ | ❌ |
| Produk (Lihat & Cari) | ✅ | ✅ | ✅ |
| Produk (Tambah/Edit/Hapus) | ✅ | ✅ | ❌ |
| Transaksi (Buat) | ✅ | ✅ | ✅ |
| Transaksi (Edit/Hapus) | ✅ | ✅ | ❌ |
| Riwayat Transaksi | ✅ | ✅ | 👁️ Milik sendiri |
| Supplier & Restock | ✅ | ✅ | ❌ |
| Hutang (Lihat & Catat) | ✅ | ✅ | ✅ |
| Hutang (Edit/Hapus) | ✅ | ✅ | ❌ |
| Dashboard (Statistik Bisnis) | ✅ | ✅ | ❌ |
| Dashboard (Halaman Kasir) | ✅ | ✅ | ✅ (tampilan berbeda) |
| Laporan | ✅ | ✅ | ❌ |

---

## 6. Pembagian Modul & Anggota

> Nama anggota diisi sesuai kelompok. Setiap anggota punya CRUD penuh di modulnya.  
> **Laporan per modul** dikerjakan masing-masing anggota di dalam modulnya sendiri.  
> **Dashboard utama** dikerjakan bersama — setiap anggota menyumbang widget dari modulnya.  
> **Infrastruktur global** (`config.php`, `header.php`, `navbar.php`) dikerjakan bersama di awal sprint.

| Anggota | Modul | Entitas Database | CRUD Utama |
|---|---|---|---|
| **Anggota 1** | Autentikasi + Manajemen Pengguna | `users`, `log_login` | CRUD Users + Log Login |
| **Anggota 2** | Manajemen Produk & Kategori | `produk`, `kategori` | CRUD Produk + CRUD Kategori |
| **Anggota 3** | Transaksi Penjualan | `transaksi`, `detail_transaksi` | CRUD Transaksi |
| **Anggota 4** | Supplier & Pembelian (Restock) | `supplier`, `pembelian`, `detail_pembelian` | CRUD Supplier + CRUD Pembelian |
| **Anggota 5** | Manajemen Hutang Pelanggan | `pelanggan`, `hutang`, `detail_hutang` | CRUD Pelanggan + CRUD Hutang |

---

### Detail Tanggung Jawab per Anggota

**Anggota 1 — Autentikasi & Manajemen Pengguna**
- Setup `config.php` (koneksi database) — dikerjakan paling awal
- Setup template global: `includes/header.php`, `includes/navbar.php`, `includes/footer.php`
- Halaman login (form, validasi server, session)
- Logika pencatatan otomatis ke tabel `log_login` setiap kali login (berhasil/gagal)
- Halaman logout
- Middleware proteksi halaman (cek session + role)
- CRUD akun pengguna (daftar, tambah, edit, hapus)
- Halaman riwayat log login (UC-44)
- Kontribusi widget Dashboard: — (Dashboard dikoordinasikan bersama)

**Anggota 2 — Manajemen Produk & Kategori**
- CRUD produk (daftar dengan pencarian, tambah, edit, hapus)
- CRUD kategori (daftar, tambah, edit, hapus)
- Logika peringatan stok minimum (highlight produk kritis)
- Halaman laporan stok produk
- Kontribusi widget Dashboard: kartu "Total Produk" dan "Produk Stok Kritis"

**Anggota 3 — Transaksi Penjualan**
- Halaman buat transaksi baru (pilih produk, jumlah, hitung total & kembalian)
- Halaman riwayat transaksi (dengan filter tanggal)
- Halaman detail transaksi
- Fungsi batal transaksi + rollback stok
- Logika stok berkurang otomatis saat transaksi
- Halaman laporan penjualan per periode
- Kontribusi widget Dashboard: kartu "Penjualan Hari Ini" dan "Jumlah Transaksi"

**Anggota 4 — Supplier & Pembelian (Restock)**
- CRUD supplier (daftar, tambah, edit, hapus)
- CRUD catatan restock/pembelian (daftar, tambah, detail, edit, hapus)
- Logika stok bertambah otomatis saat restock dicatat
- Logika rollback stok saat catatan restock dihapus
- Halaman laporan riwayat restock
- Kontribusi widget Dashboard: — (opsional: kartu "Restock Terakhir")

**Anggota 5 — Manajemen Hutang Pelanggan**
- CRUD pelanggan (daftar, tambah, edit, hapus)
- CRUD hutang header (catat hutang baru dengan memilih produk, daftar hutang aktif, detail, hapus)
- CRUD detail hutang (tambah produk ke hutang, edit jumlah, hapus baris produk)
- Logika stok berkurang otomatis saat hutang dicatat (sama seperti transaksi)
- Logika rollback stok saat catatan hutang dihapus
- Logika status hutang (aktif / lunas) + catat pembayaran
- Snapshot `harga_satuan` saat hutang dicatat agar nominal tidak berubah jika harga produk berubah
- Halaman laporan rekap hutang per pelanggan (rincian per produk)
- Kontribusi widget Dashboard: kartu "Total Hutang Aktif" dan "Pelanggan Berutang"

---

## 7. Rancangan Database

### Daftar Tabel

| No | Nama Tabel | Deskripsi | Dikerjakan Anggota |
|---|---|---|---|
| 1 | `users` | Data akun pengguna sistem | Anggota 1 |
| 2 | `log_login` | Riwayat aktivitas login (audit log) | Anggota 1 |
| 3 | `kategori` | Kategori produk | Anggota 2 |
| 4 | `produk` | Data produk / barang dagangan | Anggota 2 |
| 5 | `transaksi` | Header transaksi penjualan | Anggota 3 |
| 6 | `detail_transaksi` | Rincian produk per transaksi | Anggota 3 |
| 7 | `supplier` | Data agen / supplier | Anggota 4 |
| 8 | `pembelian` | Header catatan restock | Anggota 4 |
| 9 | `detail_pembelian` | Rincian produk per restock | Anggota 4 |
| 10 | `pelanggan` | Data pelanggan yang berutang | Anggota 5 |
| 11 | `hutang` | Header catatan hutang & pembayaran | Anggota 5 |
| 12 | `detail_hutang` | Rincian produk per catatan hutang | Anggota 5 |

> Total: **12 tabel** (11 tabel entitas bisnis + 1 tabel audit) — jauh melampaui syarat minimal 6 tabel ✅
>
> **Catatan:** Tabel `log_login` adalah tabel audit/teknis yang mencatat setiap aktivitas login. Tabel ini **tidak dimasukkan ke ERD Logis** karena bukan entitas bisnis, namun tetap ada di file `.sql` dan digunakan oleh fitur UC-44 (Lihat Riwayat Log Login).

---

### Struktur Tabel

#### Tabel `users`
```sql
CREATE TABLE users (
    id_user       INT PRIMARY KEY AUTO_INCREMENT,
    nama_lengkap  VARCHAR(100) NOT NULL,
    username      VARCHAR(50)  UNIQUE NOT NULL,
    password      VARCHAR(255) NOT NULL,   -- hasil password_hash()
    role          ENUM('owner','admin','kasir') NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Tabel `log_login`
```sql
CREATE TABLE log_login (
    id_log         INT PRIMARY KEY AUTO_INCREMENT,
    id_user        INT          DEFAULT NULL,  -- NULL jika login gagal
    username_input VARCHAR(50)  NOT NULL,      -- username yang diketik saat percobaan login
    waktu_login    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    status         ENUM('berhasil', 'gagal') NOT NULL,
    keterangan     VARCHAR(100) DEFAULT NULL,  -- misal: "Password salah"
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);
```

> **Keterangan:** Tabel ini diisi otomatis oleh sistem setiap kali UC-01 (Login) dijalankan — baik yang berhasil maupun yang gagal. Digunakan oleh UC-44 (Lihat Riwayat Log Login) untuk keperluan audit akses sistem. Tidak dimasukkan ke ERD Logis karena bersifat tabel audit, bukan entitas bisnis.

#### Tabel `kategori`
```sql
CREATE TABLE kategori (
    id_kategori   INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi     TEXT
);
```

#### Tabel `produk`
```sql
CREATE TABLE produk (
    id_produk     INT PRIMARY KEY AUTO_INCREMENT,
    id_kategori   INT NOT NULL,
    kode_produk   VARCHAR(50) UNIQUE,
    nama_produk   VARCHAR(150) NOT NULL,
    harga_beli    DECIMAL(10,2) NOT NULL,
    harga_jual    DECIMAL(10,2) NOT NULL,
    stok          INT NOT NULL DEFAULT 0,
    stok_minimum  INT DEFAULT 5,
    satuan        VARCHAR(30),              -- pcs, kg, liter, bungkus, dll.
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);
```

#### Tabel `transaksi`
```sql
CREATE TABLE transaksi (
    id_transaksi    INT PRIMARY KEY AUTO_INCREMENT,
    id_user         INT NOT NULL,            -- kasir yang melayani
    kode_transaksi  VARCHAR(50) UNIQUE,      -- misal: TRX-20240101-001
    total_harga     DECIMAL(10,2) NOT NULL,
    uang_bayar      DECIMAL(10,2),
    kembalian       DECIMAL(10,2),
    status          ENUM('selesai','batal') DEFAULT 'selesai',
    keterangan      TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);
```

#### Tabel `detail_transaksi`
```sql
CREATE TABLE detail_transaksi (
    id_detail       INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi    INT NOT NULL,
    id_produk       INT NOT NULL,
    jumlah          INT NOT NULL,
    harga_satuan    DECIMAL(10,2) NOT NULL,  -- snapshot harga saat transaksi
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
);
```

#### Tabel `supplier`
```sql
CREATE TABLE supplier (
    id_supplier   INT PRIMARY KEY AUTO_INCREMENT,
    nama_supplier VARCHAR(150) NOT NULL,
    nama_kontak   VARCHAR(100),
    no_telepon    VARCHAR(20),
    alamat        TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Tabel `pembelian`
```sql
CREATE TABLE pembelian (
    id_pembelian    INT PRIMARY KEY AUTO_INCREMENT,
    id_supplier     INT NOT NULL,
    id_user         INT NOT NULL,            -- admin yang mencatat
    kode_pembelian  VARCHAR(50) UNIQUE,
    total_harga     DECIMAL(10,2),
    keterangan      TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier),
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);
```

#### Tabel `detail_pembelian`
```sql
CREATE TABLE detail_pembelian (
    id_detail_beli  INT PRIMARY KEY AUTO_INCREMENT,
    id_pembelian    INT NOT NULL,
    id_produk       INT NOT NULL,
    jumlah          INT NOT NULL,
    harga_beli      DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pembelian) REFERENCES pembelian(id_pembelian),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
);
```

#### Tabel `pelanggan`
```sql
CREATE TABLE pelanggan (
    id_pelanggan  INT PRIMARY KEY AUTO_INCREMENT,
    nama_pelanggan VARCHAR(150) NOT NULL,
    no_telepon    VARCHAR(20),
    alamat        TEXT,                      -- misal: "Tetangga sebelah kanan"
    keterangan    TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Tabel `hutang`
```sql
CREATE TABLE hutang (
    id_hutang       INT PRIMARY KEY AUTO_INCREMENT,
    id_pelanggan    INT NOT NULL,
    id_user         INT NOT NULL,            -- user yang mencatat
    kode_hutang     VARCHAR(50) UNIQUE,      -- misal: HTG-20240601-001
    total_hutang    DECIMAL(10,2) NOT NULL,  -- total dari semua detail_hutang
    jumlah_terbayar DECIMAL(10,2) DEFAULT 0,
    sisa_hutang     DECIMAL(10,2),           -- total_hutang - jumlah_terbayar
    status          ENUM('aktif','lunas') DEFAULT 'aktif',
    tanggal_hutang  DATE NOT NULL,
    tanggal_lunas   DATE,
    keterangan      TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);
```

#### Tabel `detail_hutang`
```sql
CREATE TABLE detail_hutang (
    id_detail_hutang  INT PRIMARY KEY AUTO_INCREMENT,
    id_hutang         INT NOT NULL,
    id_produk         INT NOT NULL,
    jumlah            INT NOT NULL,
    harga_satuan      DECIMAL(10,2) NOT NULL,  -- snapshot harga jual saat hutang dicatat
    subtotal          DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_hutang) REFERENCES hutang(id_hutang),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
);
```

---

### Relasi Antar Tabel (ERD Ringkas Teks)

```
users ──────────────────────────────┬──── transaksi ──── detail_transaksi ──┐
  │                                 │                                        │
  │                                 └──── pembelian ──── detail_pembelian ───┤
  │                                           │                              │
  │                                        supplier                       produk ──── kategori
  │                                                                          │
  └──── hutang ──── detail_hutang ───────────────────────────────────────────┘
         │
      pelanggan
```

---

## 8. Rancangan Halaman (Sitemap)

### Halaman Publik (Tanpa Login)
```
/login.php
```

### Halaman Semua Role (Kasir, Admin, Owner)
```
/dashboard.php        ← satu file, konten berbeda per role:
                         Owner/Admin → statistik bisnis (penjualan, stok kritis, hutang)
                         Kasir       → sapaan, tombol transaksi, cari produk, ringkasan harian
/produk/index.php     ← lihat & cari produk (read-only untuk kasir)
/transaksi/baru.php   ← buat transaksi
/transaksi/riwayat.php ← riwayat transaksi (kasir: hanya milik sendiri)
/transaksi/detail.php  ← detail transaksi (kasir: hanya milik sendiri)
/hutang/index.php     ← lihat daftar hutang aktif
/hutang/baru.php      ← catat hutang baru (pilih pelanggan + pilih produk)
/hutang/detail.php    ← detail hutang + rincian produk yang diambil
/hutang/bayar.php     ← catat pembayaran hutang
```

### Halaman Admin (+ semua halaman Kasir)
```
/produk/tambah.php
/produk/edit.php
/kategori/index.php
/kategori/tambah.php
/kategori/edit.php
/transaksi/edit.php
/supplier/index.php
/supplier/tambah.php
/supplier/edit.php
/pembelian/index.php
/pembelian/baru.php
/pembelian/detail.php
/hutang/edit.php
/laporan/stok.php
/laporan/penjualan.php
/laporan/restock.php
/laporan/hutang.php
```

### Halaman Owner (+ semua halaman Admin)
```
/users/index.php
/users/tambah.php
/users/edit.php
/users/log_login.php   ← riwayat aktivitas login (UC-44)
```

---

## 9. Fitur Bonus yang Direncanakan

| Fitur Bonus | Poin | Dikerjakan Anggota | Prioritas |
|---|---|---|---|
| Multi-Role Pengguna | +10 | Anggota 1 | 🔴 Tinggi (sudah dirancang) |
| Pencarian Produk | +5 | Anggota 2 | 🔴 Tinggi (kebutuhan utama objek) |
| Paginasi Data | +5 | Semua (tiap halaman daftar) | 🟡 Sedang |
| Upload Gambar Produk | +10 | Anggota 2 | 🟡 Sedang |
| Export PDF / Excel | +10 | Anggota 3 & 5 (laporan) | 🟢 Opsional |

**Potensi nilai bonus total: +40 poin**

---

## 10. Rencana Teknologi & Struktur Folder

### Teknologi
- **Backend:** PHP Native (tanpa framework)
- **Database:** MySQL
- **Frontend:** HTML5 + CSS3 + Bootstrap 5 (pilihan: versi CDN)
- **Server Lokal:** XAMPP / Laragon
- **Aksesibilitas:** Mobile-friendly (diutamakan, karena objek hanya punya HP)

### Struktur Folder
```
calontong/
├── config.php                    ← koneksi database
├── index.php                     ← redirect ke login atau dashboard
├── login.php
├── logout.php
├── dashboard.php
│
├── pages/
│   ├── users/
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   ├── produk/
│   │   ├── index.php             ← termasuk fitur pencarian
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   ├── kategori/
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   ├── transaksi/
│   │   ├── index.php
│   │   ├── baru.php
│   │   ├── detail.php
│   │   ├── edit.php
│   │   └── batal.php
│   ├── supplier/
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   ├── pembelian/
│   │   ├── index.php
│   │   ├── baru.php
│   │   ├── detail.php
│   │   └── hapus.php
│   ├── hutang/
│   │   ├── index.php
│   │   ├── catat.php
│   │   ├── detail.php
│   │   ├── bayar.php             ← catat pembayaran hutang
│   │   └── hapus.php
│   └── laporan/
│       ├── stok.php
│       ├── penjualan.php
│       ├── restock.php
│       └── hutang.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   └── auth_check.php            ← middleware cek session & role
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── img/
│       └── produk/               ← folder upload foto produk (bonus)
│
└── calontong.sql                  ← file database lengkap + data dummy
```

---

## 11. Checklist Kesesuaian Spesifikasi UAS

### A. Syarat Umum
- [x] Kelompok 3–5 orang → **5 orang**
- [x] Setiap anggota memiliki modul yang dapat diidentifikasi
- [x] Domain kasus nyata: Sistem Informasi Toko / Warung Kelontong
- [x] Ada objek nyata: **Warung Ananta** (warung keluarga anggota kelompok)

### B. Syarat Teknis

**1. Struktur & Arsitektur**
- [x] PHP native (tanpa framework)
- [x] MySQL minimal 6 tabel dengan relasi & foreign key → **12 tabel** (11 entitas bisnis + 1 tabel audit)
- [x] File koneksi dipisah: `config.php`
- [x] Struktur folder terorganisir: `/pages`, `/assets`, `/includes`

**2. Fitur CRUD Lengkap**
- [x] Create — ada di semua 5 modul
- [x] Read — ada di semua 5 modul
- [x] Update — ada di semua 5 modul
- [x] Delete dengan konfirmasi — ada di semua 5 modul

**3. Autentikasi Pengguna**
- [x] Halaman login dengan validasi server
- [x] Password di-hash (`password_hash()`)
- [x] Session PHP untuk status login
- [x] Fungsi logout yang hapus session
- [x] Proteksi halaman dari akses tanpa login

**4. Validasi & Keamanan**
- [x] Validasi sisi server (PHP)
- [x] Pesan error yang informatif
- [x] Sanitasi input / Prepared Statement
- [x] Cegah akses langsung ke file PHP sensitif

**5. Antarmuka Pengguna**
- [x] Bootstrap 5 untuk styling
- [x] Navigasi konsisten via `includes/navbar.php`
- [x] Tabel untuk daftar data
- [x] Responsif — **mobile-friendly** (diutamakan karena objek hanya punya HP)

**6. Halaman Rekap / Laporan**
- [x] Dashboard: penjualan hari ini, stok kritis, total hutang aktif
- [x] Laporan stok (Modul 2)
- [x] Laporan penjualan (Modul 3)
- [x] Laporan restock (Modul 4)
- [x] Laporan hutang (Modul 5)

### C. Fitur Bonus
- [x] Multi-Role Pengguna (+10) — sudah dirancang
- [ ] Pencarian Produk (+5) — prioritas tinggi, dikerjakan Anggota 2
- [ ] Paginasi Data (+5) — direncanakan di semua halaman daftar
- [ ] Upload Gambar Produk (+10) — direncanakan Anggota 2
- [ ] Export PDF/Excel (+10) — opsional, halaman laporan

### D. Berkas yang Dikumpulkan
- [ ] Source code (.zip atau link GitHub/GitLab)
- [ ] File `calontong.sql` + data dummy minimal 10 baris per tabel
- [ ] Laporan tertulis PDF (5–10 halaman): deskripsi kasus, ERD, pembagian tugas, screenshot
- [ ] Presentasi & demo langsung (setiap anggota menjelaskan bagian masing-masing)

---

*Dokumen ini merupakan draft v5, diperbarui setelah wawancara dengan pemilik Warung Ananta.*  
*Akan diperbarui kembali sesuai perkembangan pengerjaan proyek.*

**Ca'lontong** | Sistem Informasi Warung Ananta | Pemrograman Web Dasar — PHP & MySQL
