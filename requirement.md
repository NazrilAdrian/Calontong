# 📐 REQUIREMENTS.md
## Sistem Informasi Toko / Warung Kelontong

> Dokumen ini mendefinisikan seluruh persyaratan teknis, fungsional, dan non-fungsional yang **wajib dipenuhi** sebelum proyek dinyatakan selesai. Dijadikan acuan oleh semua anggota tim selama pengerjaan dan digunakan sebagai checklist saat testing & presentasi.

---

## Daftar Isi

1. [Persyaratan Umum](#1-persyaratan-umum)
2. [Persyaratan Teknis Wajib](#2-persyaratan-teknis-wajib)
3. [Persyaratan Fungsional](#3-persyaratan-fungsional)
4. [Persyaratan Non-Fungsional](#4-persyaratan-non-fungsional)
5. [Persyaratan Keamanan](#5-persyaratan-keamanan)
6. [Persyaratan Database](#6-persyaratan-database)
7. [Persyaratan UI/UX](#7-persyaratan-uiux)
8. [Persyaratan Fitur Bonus](#8-persyaratan-fitur-bonus)
9. [Persyaratan Pengumpulan](#9-persyaratan-pengumpulan)
10. [Definition of Done](#10-definition-of-done)
11. [Checklist Final Sebelum Submit](#11-checklist-final-sebelum-submit)

---

## 1. Persyaratan Umum

### 1.1 Ketentuan Kelompok

| Ketentuan | Detail |
|-----------|--------|
| Jumlah anggota | 5 orang |
| Kontribusi | Setiap anggota wajib memiliki modul yang dapat diidentifikasi |
| Identitas | Nama dan NIM dicantumkan di laporan **dan** di halaman aplikasi |
| Domain kasus | Sistem Informasi Toko / Warung Kelontong |

### 1.2 Batasan Pengerjaan

- **DILARANG** menyalin project dari internet atau GitHub tanpa modifikasi signifikan
- Setiap anggota **wajib mampu** menjelaskan kode bagiannya saat presentasi
- Pelanggaran akademik berakibat nilai **0** untuk seluruh kelompok

---

## 2. Persyaratan Teknis Wajib

### 2.1 Bahasa Pemrograman & Framework

```
✅ PHP Native — DILARANG menggunakan framework Laravel, CodeIgniter, Symfony, dll.
✅ MySQL — sebagai database relasional
✅ HTML5 + CSS3 — untuk markup dan styling
✅ JavaScript — untuk konfirmasi hapus dan interaksi UI sederhana
```

### 2.2 Arsitektur Aplikasi

```
✅ Struktur folder terorganisir:
   /includes  — file koneksi, header, footer, auth check
   /pages     — semua halaman fungsional
   /assets    — CSS, JS, dan file upload

✅ File koneksi database DIPISAH dalam satu file tersendiri
   → includes/koneksi.php

✅ Layout menggunakan PHP include statements:
   include 'includes/header.php';
   include 'includes/footer.php';
   require_once 'includes/koneksi.php';
   require_once 'includes/auth_check.php';

✅ DILARANG menggunakan alternatif selain include/require untuk layouting
```

### 2.3 Lingkungan Pengembangan

```
✅ Berjalan di localhost menggunakan XAMPP / Laragon / MAMP
✅ PHP versi >= 7.4
✅ MySQL versi >= 5.7
✅ Diuji di browser Chrome / Firefox
✅ Resolusi minimum yang didukung: 1024px (desktop)
```

---

## 3. Persyaratan Fungsional

Setiap persyaratan fungsional diberi kode unik sebagai referensi di Trello dan saat testing.

---

### 3.1 Modul Autentikasi `[AUTH]`

#### REQ-AUTH-01 — Halaman Login
- Sistem menampilkan form login dengan field `username` dan `password`
- Form menggunakan method `POST`
- Setelah login berhasil, pengguna diarahkan ke `pages/dashboard.php`
- Setelah login gagal, menampilkan pesan error yang informatif (bukan pesan generik)

#### REQ-AUTH-02 — Validasi Login
- Validasi dilakukan di sisi **server (PHP)**, bukan hanya HTML `required`
- Field kosong harus ditolak dengan pesan error spesifik
- Username yang tidak terdaftar menampilkan pesan: *"Username atau password salah"*
- Password diverifikasi menggunakan `password_verify()` terhadap hash di database

#### REQ-AUTH-03 — Session Management
- Setelah login berhasil, sistem menyimpan ke session minimal:
  - `$_SESSION['user_id']`
  - `$_SESSION['username']`
  - `$_SESSION['nama']`
  - `$_SESSION['role']`
- Session dimulai dengan `session_start()` di setiap halaman yang membutuhkan

#### REQ-AUTH-04 — Logout
- Halaman `logout.php` menghapus session dengan `session_destroy()`
- Setelah logout, pengguna diarahkan kembali ke `login.php`
- Mengakses halaman setelah logout harus kembali ke login (tidak bisa back-button)

#### REQ-AUTH-05 — Proteksi Halaman
- Semua halaman di `/pages/` wajib menyertakan `auth_check.php`
- Pengguna yang mengakses halaman via URL langsung tanpa login otomatis diarahkan ke `login.php`
- File `auth_check.php` berisi minimal:
  ```php
  if (!isset($_SESSION['user_id'])) {
      header('Location: /login.php');
      exit;
  }
  ```

---

### 3.2 Modul User Management `[USER]`

#### REQ-USER-01 — Daftar User
- Menampilkan tabel semua user: nama, username, role, status aktif
- Hanya dapat diakses oleh role `owner`

#### REQ-USER-02 — Tambah User
- Form berisi: nama, username, password, konfirmasi password, role, status
- Password di-hash menggunakan `password_hash($password, PASSWORD_DEFAULT)`
- Username harus unik — jika duplikat tampilkan pesan error
- Konfirmasi password harus cocok dengan password

#### REQ-USER-03 — Edit User
- Dapat mengubah nama, role, dan status aktif
- Perubahan password bersifat opsional — jika field password dikosongkan, password lama tetap dipakai
- Jika password diisi, password baru di-hash ulang

#### REQ-USER-04 — Hapus User
- Menampilkan konfirmasi sebelum menghapus
- User yang sedang login **tidak dapat** menghapus akunnya sendiri

---

### 3.3 Modul Kategori `[CAT]`

#### REQ-CAT-01 — Daftar Kategori
- Menampilkan tabel semua kategori: nama, deskripsi, jumlah produk

#### REQ-CAT-02 — Tambah Kategori
- Form berisi: nama (wajib), deskripsi (opsional)
- Nama kategori harus unik

#### REQ-CAT-03 — Edit Kategori
- Dapat mengubah nama dan deskripsi

#### REQ-CAT-04 — Hapus Kategori
- Menampilkan konfirmasi sebelum menghapus
- **DILARANG** menghapus kategori yang masih memiliki produk aktif
- Tampilkan pesan error jika kategori masih digunakan

---

### 3.4 Modul Produk `[PROD]`

#### REQ-PROD-01 — Daftar Produk
- Menampilkan tabel: kode produk, nama, kategori, harga jual, stok, satuan
- Baris dengan stok ≤ `stok_minimum` ditandai dengan badge / warna merah sebagai peringatan

#### REQ-PROD-02 — Tambah Produk
- Form berisi: kategori (dropdown), kode produk, nama, deskripsi, harga beli, harga jual, stok awal, stok minimum, satuan, foto (opsional)
- Kode produk harus unik
- Harga jual harus lebih besar dari 0
- Stok tidak boleh negatif

#### REQ-PROD-03 — Edit Produk
- Semua field dapat diubah
- Jika foto baru di-upload, foto lama dihapus dari server

#### REQ-PROD-04 — Hapus Produk
- Menampilkan konfirmasi sebelum menghapus
- **DILARANG** menghapus produk yang sudah pernah ada di `transaction_details`
- Foto produk dihapus dari folder `assets/uploads/products/` bersamaan dengan hapus data

#### REQ-PROD-05 — Upload Foto Produk
- Tipe file yang diizinkan: `.jpg`, `.jpeg`, `.png`, `.webp`
- Ukuran maksimal file: 2 MB
- Validasi tipe dan ukuran dilakukan di sisi **PHP**, bukan hanya HTML `accept`
- File disimpan di `assets/uploads/products/` dengan nama yang di-rename (hindari nama file duplikat)

---

### 3.5 Modul Pelanggan `[CUST]`

#### REQ-CUST-01 — Daftar Pelanggan
- Menampilkan tabel: nama, telepon, alamat, email, tanggal daftar

#### REQ-CUST-02 — Tambah Pelanggan
- Form berisi: nama (wajib), telepon, alamat, email

#### REQ-CUST-03 — Edit Pelanggan
- Semua field dapat diubah

#### REQ-CUST-04 — Hapus Pelanggan
- Menampilkan konfirmasi sebelum menghapus
- Jika pelanggan memiliki riwayat transaksi, kolom `customer_id` di tabel `transactions` di-set `NULL` (bukan error / gagal hapus)

---

### 3.6 Modul Transaksi `[TRX]`

#### REQ-TRX-01 — Halaman Kasir (Input Transaksi Baru)
- Kasir dapat mencari produk berdasarkan nama atau kode produk
- Produk dapat ditambahkan ke keranjang dengan qty yang bisa diubah
- Subtotal per item dihitung otomatis: `qty × harga_jual`
- Total harga keseluruhan ditampilkan real-time
- Kasir mengisi jumlah uang yang dibayar
- Kembalian dihitung otomatis: `bayar − total`
- Transaksi tidak dapat disimpan jika bayar < total
- Kasir dapat memilih pelanggan (opsional) dari dropdown
- Kasir dapat memilih metode pembayaran: tunai / transfer / QRIS

#### REQ-TRX-02 — Penyimpanan Transaksi
- Data tersimpan ke dua tabel sekaligus: `transactions` dan `transaction_details`
- Stok produk **berkurang otomatis** sebesar qty yang dibeli
- Kode transaksi di-generate otomatis dengan format: `TRX-YYYYMMDD-XXXX`
- Sistem menggunakan `mysqli_insert_id()` untuk menghubungkan header dan detail
- Jika terjadi error saat menyimpan, tidak ada data yang tersimpan setengah (gunakan MySQL transaction: `BEGIN` / `COMMIT` / `ROLLBACK`)

#### REQ-TRX-03 — Riwayat Transaksi
- Menampilkan daftar semua transaksi: kode, tanggal, kasir, pelanggan, total, metode bayar, status
- Dapat difilter berdasarkan tanggal (dari–sampai)

#### REQ-TRX-04 — Detail Transaksi
- Menampilkan detail satu nota: semua item, qty, harga satuan, subtotal, total, kembalian, kasir

#### REQ-TRX-05 — Batalkan Transaksi
- Transaksi dapat dibatalkan dengan konfirmasi
- Stok produk **dikembalikan** saat transaksi dibatalkan
- Status transaksi berubah menjadi `batal`

---

### 3.7 Modul Dashboard `[DASH]`

#### REQ-DASH-01 — Kartu Statistik
Halaman dashboard menampilkan minimal 4 kartu ringkasan:
- Total produk aktif
- Total pelanggan terdaftar
- Jumlah transaksi hari ini
- Total pendapatan hari ini (dalam Rupiah)

#### REQ-DASH-02 — Tabel Produk Stok Hampir Habis
- Menampilkan daftar produk yang stoknya ≤ `stok_minimum`
- Maksimal 10 produk ditampilkan, diurutkan dari stok terkecil

#### REQ-DASH-03 — Grafik Transaksi
- Menampilkan grafik transaksi 7 hari terakhir (jumlah transaksi atau total pendapatan per hari)
- Menggunakan Chart.js yang diambil via CDN

---

## 4. Persyaratan Non-Fungsional

### 4.1 Performa

```
✅ Setiap halaman selesai loading dalam < 3 detik di localhost
✅ Query database tidak menggunakan SELECT * di halaman yang menampilkan banyak data
   → Selalu specify kolom yang dibutuhkan: SELECT id, nama, harga_jual FROM products
✅ Gambar produk di-resize / dikompres sebelum disimpan (opsional tapi dianjurkan)
```

### 4.2 Keandalan

```
✅ Tidak ada halaman yang menampilkan layar putih kosong saat error
✅ Setiap operasi database dibungkus pengecekan: if (!$result) { ... }
✅ Pesan error yang ditampilkan ke pengguna harus informatif, bukan pesan teknis PHP
✅ File SQL dapat diimport ulang dari awal tanpa error
```

### 4.3 Maintainability

```
✅ Setiap file PHP maksimal mengerjakan satu tanggung jawab
✅ Tidak ada kode duplikat untuk koneksi database — semua pakai koneksi.php
✅ Nama variabel dan fungsi menggunakan Bahasa Indonesia atau Inggris, konsisten
✅ Komentar minimal di bagian logika yang kompleks (terutama di modul transaksi)
```

---

## 5. Persyaratan Keamanan

### 5.1 Autentikasi & Otorisasi

| Requirement | Implementasi |
|-------------|--------------|
| Hash password | `password_hash($pass, PASSWORD_DEFAULT)` — DILARANG MD5 atau SHA1 |
| Verifikasi password | `password_verify($input, $hash)` |
| Proteksi halaman | `require_once 'includes/auth_check.php'` di setiap halaman |
| Akses berbasis role | Cek `$_SESSION['role']` sebelum tampilkan menu / fitur sensitif |

### 5.2 Pencegahan SQL Injection

**WAJIB** menggunakan salah satu metode berikut untuk semua query yang menerima input pengguna:

**Pilihan 1 — Prepared Statement (direkomendasikan):**
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
```

**Pilihan 2 — mysqli_real_escape_string:**
```php
$username = mysqli_real_escape_string($conn, $_POST['username']);
$query = "SELECT * FROM users WHERE username = '$username'";
```

### 5.3 Keamanan File Upload

```
✅ Validasi ekstensi file di PHP (bukan hanya atribut HTML accept)
✅ Validasi MIME type menggunakan finfo_file() atau getimagesize()
✅ Rename file saat disimpan — jangan gunakan nama file asli dari pengguna
✅ Simpan file di luar root publik ATAU pastikan folder tidak executable
✅ Ukuran maksimal file dicek: if ($_FILES['foto']['size'] > 2097152) { ... }
```

### 5.4 Keamanan Umum

```
✅ File includes/koneksi.php tidak dapat diakses langsung dari URL
   → Tambahkan di awal file: if (!defined('APP_ROOT')) { die('Akses ditolak'); }
✅ Tidak menampilkan pesan error PHP/MySQL ke pengguna di halaman produksi
✅ Semua output dari database yang ditampilkan ke HTML menggunakan htmlspecialchars()
   → Contoh: echo htmlspecialchars($row['nama']);
```

---

## 6. Persyaratan Database

### 6.1 Ketentuan Umum

```
✅ Minimal 6 tabel dengan relasi menggunakan FOREIGN KEY
✅ Engine: InnoDB (mendukung foreign key dan transaction)
✅ Charset: utf8mb4 (mendukung karakter Indonesia dan emoji)
✅ Setiap tabel memiliki PRIMARY KEY dengan AUTO_INCREMENT
✅ Nama tabel dan kolom menggunakan snake_case (huruf kecil + underscore)
✅ Kolom yang sering dicari (username, kode_produk) diberi INDEX atau UNIQUE
```

### 6.2 Daftar Tabel Wajib

| Tabel | Kolom Kunci | Relasi |
|-------|-------------|--------|
| `users` | id, username, password, role | — |
| `categories` | id, nama | — |
| `products` | id, category_id, kode_produk, stok | FK → categories |
| `customers` | id, nama, telepon | — |
| `transactions` | id, kode_transaksi, customer_id, user_id | FK → customers, users |
| `transaction_details` | id, transaction_id, product_id | FK → transactions, products |

### 6.3 Ketentuan Data Dummy

```
✅ Minimal 10 baris data per tabel
✅ File SQL menyertakan CREATE DATABASE dan USE statement
✅ File SQL menyertakan data dummy dengan INSERT INTO
✅ File SQL dapat diimport ulang (DROP TABLE IF EXISTS atau CREATE TABLE IF NOT EXISTS)
✅ Password di data dummy menggunakan hasil password_hash(), bukan plain text
```

---

## 7. Persyaratan UI/UX

### 7.1 Layout & Navigasi

```
✅ Menggunakan CSS — boleh Bootstrap 5 via CDN
✅ Navbar konsisten di setiap halaman (via header.php)
✅ Navbar menampilkan nama pengguna yang sedang login
✅ Navbar memiliki link logout
✅ Tampilan responsif minimal di resolusi desktop 1024px
✅ Tidak ada halaman tanpa navigasi (kecuali login.php)
```

### 7.2 Tabel Data

```
✅ Semua daftar data menggunakan elemen <table> dengan styling Bootstrap
✅ Tabel memiliki header yang jelas
✅ Setiap baris memiliki tombol Aksi: Edit dan Hapus
✅ Tabel tidak meluber keluar container di layar 1024px
✅ Jika data kosong, tampilkan pesan "Belum ada data" — bukan tabel kosong
```

### 7.3 Form

```
✅ Setiap form memiliki label yang jelas untuk setiap input
✅ Field wajib ditandai dengan tanda (*) atau label "Wajib diisi"
✅ Pesan error validasi ditampilkan di dekat field yang bermasalah
✅ Setelah berhasil simpan/edit, pengguna diarahkan kembali ke halaman daftar
✅ Tombol Submit diberi teks yang deskriptif: "Simpan Produk", "Update Data", dll.
```

### 7.4 Konfirmasi Hapus

```
✅ Setiap aksi hapus WAJIB menampilkan konfirmasi sebelum dieksekusi
✅ Minimal menggunakan JavaScript confirm():
   onclick="return confirm('Yakin ingin menghapus data ini?')"
✅ Atau menggunakan SweetAlert2 untuk tampilan yang lebih baik
```

### 7.5 Feedback ke Pengguna

```
✅ Setiap aksi berhasil menampilkan pesan sukses (flash message / alert Bootstrap)
✅ Setiap aksi gagal menampilkan pesan error yang informatif
✅ Pesan tidak ditampilkan terus-menerus (hilang setelah redirect atau dismiss)
```

---

## 8. Persyaratan Fitur Bonus

Fitur bonus dikerjakan **hanya setelah semua fitur MVP selesai dan diuji**. Masing-masing memberi poin tambahan di rubrik penilaian.

### REQ-BONUS-01 — Multi-Role Access (+10 poin)

```
✅ Tiga role berbeda: owner, admin, kasir
✅ Menu navigasi berbeda berdasarkan role yang login
✅ Owner: akses penuh ke semua menu termasuk manajemen user
✅ Admin: akses ke produk, kategori, pelanggan, transaksi, dashboard
✅ Kasir: akses ke halaman kasir, riwayat transaksi (read-only), daftar produk (read-only)
✅ Halaman yang tidak boleh diakses oleh role tertentu menampilkan pesan "Akses ditolak"
   bukan error PHP atau halaman kosong
```

### REQ-BONUS-02 — Search & Filter (+5 poin)

```
✅ Form search tersedia di halaman daftar produk, pelanggan, dan riwayat transaksi
✅ Search menggunakan query LIKE: WHERE nama LIKE '%kata_kunci%'
✅ Input search di-sanitasi sebelum masuk ke query
✅ Jika hasil pencarian kosong, tampilkan pesan "Data tidak ditemukan untuk: [kata kunci]"
✅ URL menyertakan parameter pencarian agar bisa dibookmark: ?search=kata
```

### REQ-BONUS-03 — Paginasi Data (+5 poin)

```
✅ Tabel menampilkan maksimal N baris per halaman (default: 10)
✅ Tersedia navigasi: Sebelumnya / Selanjutnya / nomor halaman
✅ Paginasi menggunakan LIMIT dan OFFSET di query SQL
✅ Paginasi kompatibel dengan fitur search (halaman 2 dari hasil pencarian tetap benar)
```

### REQ-BONUS-04 — Export PDF / Excel (+10 poin)

```
✅ Tersedia tombol Export di halaman laporan transaksi atau stok produk
✅ Export PDF menggunakan library FPDF atau TCPDF
✅ Export Excel menggunakan PhpSpreadsheet atau output file CSV
✅ File yang diekspor berisi data sesuai filter yang aktif (bukan selalu semua data)
✅ Nama file ekspor menyertakan tanggal: laporan_transaksi_2025-05-17.pdf
```

### REQ-BONUS-05 — Upload Foto Produk (+10 poin)

```
✅ Sudah tercakup di REQ-PROD-05
✅ Foto ditampilkan sebagai thumbnail di halaman daftar produk
✅ Foto ditampilkan lebih besar di halaman detail / edit produk
✅ Jika produk tidak memiliki foto, tampilkan gambar placeholder default
```

---

## 9. Persyaratan Pengumpulan

Berdasarkan spesifikasi dosen, berkas yang dikumpulkan:

### 9.1 Source Code
- Format: folder `.zip` **atau** link GitHub repository yang dapat diakses publik
- Harus berjalan tanpa modifikasi setelah setup sesuai README
- Tidak menyertakan folder `vendor/` yang besar — cantumkan instruksi install di README

### 9.2 File SQL Database
- Nama file: `warung_kelontong.sql`
- Berisi: `CREATE DATABASE`, `CREATE TABLE`, `INSERT INTO` (data dummy)
- Minimal 10 baris per tabel
- Dapat diimport ulang dari awal **tanpa error**
- Password di data dummy sudah dalam format hash

### 9.3 Laporan Tertulis (PDF, 5–10 halaman)
Berisi minimal:

| Bagian | Isi |
|--------|-----|
| Cover | Judul, mata kuliah, nama + NIM semua anggota |
| Deskripsi kasus | Masalah yang dipecahkan, tujuan sistem |
| ERD / Struktur database | Diagram relasi antar tabel + penjelasan |
| Pembagian tugas | Tabel anggota + modul + file yang dikerjakan |
| Penjelasan fitur | Daftar fitur yang berhasil diimplementasikan |
| Screenshot aplikasi | Minimal 8 screenshot halaman utama |

### 9.4 Presentasi & Demo
- Setiap anggota **wajib** mampu menjelaskan kode bagiannya
- Setiap anggota **wajib** mampu menjawab pertanyaan dosen terkait kodenya
- Demo dilakukan live: login → kasir input transaksi → lihat dashboard berubah

---

## 10. Definition of Done

Sebuah fitur dinyatakan **SELESAI** hanya jika **semua** kriteria berikut terpenuhi:

```
[ ] Semua operasi dalam fitur berjalan tanpa error di localhost
[ ] Validasi server-side (PHP) sudah ada dan bekerja
[ ] Input sudah di-sanitasi (prepared statement atau real_escape_string)
[ ] Pesan error ditampilkan dengan jelas ke pengguna
[ ] Halaman tidak bisa diakses langsung via URL tanpa login
[ ] Tampilan menggunakan Bootstrap, konsisten dengan halaman lain
[ ] Konfirmasi hapus sudah ada di semua aksi delete
[ ] Kode sudah di-push ke branch feat/ di GitHub
[ ] Pull Request sudah dibuat dan di-review minimal 1 anggota lain
[ ] Teman lain sudah mencoba fitur dan tidak menemukan bug fatal
```

---

## 11. Checklist Final Sebelum Submit

Gunakan checklist ini pada hari H-1 pengumpulan untuk memastikan tidak ada yang terlewat.

### ✅ Fungsionalitas

```
[ ] Login dan logout berfungsi
[ ] Semua halaman admin tidak bisa diakses tanpa login (test via URL langsung)
[ ] CRUD Kategori: tambah, daftar, edit, hapus berfungsi
[ ] CRUD Produk: tambah, daftar, edit, hapus berfungsi (+ upload foto)
[ ] CRUD Pelanggan: tambah, daftar, edit, hapus berfungsi
[ ] CRUD User: tambah, daftar, edit, hapus berfungsi
[ ] Halaman kasir: pilih produk, hitung total, simpan transaksi berfungsi
[ ] Stok produk berkurang otomatis setelah transaksi berhasil
[ ] Riwayat transaksi menampilkan data yang benar
[ ] Detail transaksi menampilkan item yang benar
[ ] Dashboard menampilkan statistik hari ini yang akurat
[ ] Grafik 7 hari terakhir tampil dan datanya benar
[ ] Produk hampir habis tampil di dashboard
```

### ✅ Validasi & Error Handling

```
[ ] Form tidak bisa disubmit dengan field wajib yang kosong (PHP validation)
[ ] Pesan error muncul di dekat field yang bermasalah
[ ] Username duplikat ditolak dengan pesan yang jelas
[ ] Kode produk duplikat ditolak dengan pesan yang jelas
[ ] Upload file selain gambar ditolak
[ ] Upload file > 2MB ditolak
[ ] Bayar kurang dari total ditolak di halaman kasir
[ ] Tidak ada halaman putih kosong atau error PHP yang terekspos
```

### ✅ Keamanan

```
[ ] Password semua user tersimpan sebagai hash (cek langsung di database)
[ ] Prepared statement atau real_escape_string dipakai di semua query dengan input user
[ ] htmlspecialchars() dipakai saat menampilkan data dari database ke HTML
[ ] File upload di-rename dan divalidasi tipenya
```

### ✅ Database & File

```
[ ] File warung_kelontong.sql dapat diimport ulang dari awal tanpa error
[ ] Semua foreign key bekerja (coba hapus data yang direferensi — harus ditolak atau cascade)
[ ] Data dummy minimal 10 baris per tabel tersedia
[ ] Folder assets/uploads/products/ ada dan writable
```

### ✅ Pengumpulan

```
[ ] Source code sudah di-ZIP atau GitHub repo sudah dipublikasikan
[ ] File .sql final sudah diexport dari phpMyAdmin
[ ] Laporan PDF sudah selesai (5–10 halaman, ada ERD dan screenshot)
[ ] Nama dan NIM semua anggota ada di laporan dan di halaman aplikasi
[ ] Semua anggota sudah latihan menjelaskan kode masing-masing
[ ] Skenario demo sudah disiapkan: login → transaksi → dashboard
```

---

> **Catatan:** Dokumen ini dibuat berdasarkan spesifikasi UAS Pemrograman Web Dasar dan disesuaikan dengan scope proyek kelompok. Setiap perubahan requirement yang disetujui dosen harus didokumentasikan di sini.
