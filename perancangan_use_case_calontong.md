# 📐 Perancangan Use Case
# **Ca'lontong** — Sistem Informasi Manajemen Warung Ananta

> **Dokumen:** Perancangan Use Case  
> **Versi:** 2.0 — Restrukturisasi dengan UC Utama per Modul  
> **Mata Kuliah:** Pemrograman Web Dasar (PHP & MySQL) & RPL  
> **Sistem:** Ca'lontong — Sistem Informasi Warung Kelontong

---

## DAFTAR ISI

1. [Definisi Aktor](#1-definisi-aktor)
2. [Daftar Use Case](#2-daftar-use-case)
3. [Relasi Antar Use Case](#3-relasi-antar-use-case)
4. [Spesifikasi Use Case Detail](#4-spesifikasi-use-case-detail)

---

## 1. Definisi Aktor

Sistem Ca'lontong memiliki **3 aktor** yang berinteraksi dengan sistem, dengan hierarki hak akses berjenjang.

| ID Aktor | Nama Aktor | Deskripsi | Pengguna Nyata (Warung Ananta) |
|---|---|---|---|
| A-01 | **Owner** | Pengguna dengan hak akses penuh. Dapat mengakses seluruh fitur sistem termasuk manajemen pengguna dan semua laporan. | Ayah & Ibu (pemilik warung) |
| A-02 | **Admin** | Pengguna dengan hak akses operasional. Dapat mengelola produk, transaksi, supplier, restock, dan hutang, namun tidak bisa mengelola akun pengguna lain. | Anak pertama |
| A-03 | **Kasir** | Pengguna dengan hak akses terbatas. Hanya dapat membuat transaksi penjualan, melihat produk, dan mencatat/melihat hutang pelanggan. | Anak kedua (adik) |

**Hierarki Aktor:**
```
Owner  ⊃  Admin  ⊃  Kasir
```
Owner mewarisi semua hak akses Admin, dan Admin mewarisi semua hak akses Kasir.

---

## 2. Daftar Use Case

### Penjelasan Struktur

Setiap modul memiliki **1 UC Utama** sebagai payung. Seluruh fitur di dalam modul tersebut menjadi `<<extend>>` dari UC Utama-nya. Aktor menghubungkan garis ke UC Utama jika memiliki akses penuh terhadap modul tersebut, atau langsung ke UC fitur tertentu jika aksesnya terbatas.

---

### UC-U01 — Autentikasi & Dashboard *(dikerjakan bersama)*

| ID UC | Jenis | Nama Use Case | Aktor |
|---|---|---|---|
| **UC-U01** | **UC Utama** | **Autentikasi & Dashboard** | **Owner, Admin, Kasir** |
| UC-01 | extend | Login | Owner, Admin, Kasir |
| UC-02 | extend | Logout | Owner, Admin, Kasir |
| UC-03 | extend | Lihat Dashboard | Owner, Admin, Kasir |

---

### UC-U02 — Manajemen Pengguna *(Modul 1 — Anggota 1)*

| ID UC | Jenis | Nama Use Case | Aktor |
|---|---|---|---|
| **UC-U02** | **UC Utama** | **Manajemen Pengguna** | **Owner** |
| UC-04 | extend | Lihat Daftar Pengguna | Owner |
| UC-05 | extend | Tambah Pengguna | Owner |
| UC-06 | extend | Edit Pengguna | Owner |
| UC-07 | extend | Hapus Pengguna | Owner |
| UC-44 | extend | Lihat Riwayat Log Login | Owner |

---

### UC-U03 — Manajemen Produk & Kategori *(Modul 2 — Anggota 2)*

| ID UC | Jenis | Nama Use Case | Aktor |
|---|---|---|---|
| **UC-U03** | **UC Utama** | **Manajemen Produk & Kategori** | **Owner, Admin** |
| UC-08 | extend | Lihat Daftar Produk | Owner, Admin, Kasir |
| UC-09 | extend | Cari Produk | Owner, Admin, Kasir |
| UC-10 | extend | Tambah Produk | Owner, Admin |
| UC-11 | extend | Edit Produk | Owner, Admin |
| UC-12 | extend | Hapus Produk | Owner, Admin |
| UC-13 | extend | Lihat Daftar Kategori | Owner, Admin |
| UC-14 | extend | Tambah Kategori | Owner, Admin |
| UC-15 | extend | Edit Kategori | Owner, Admin |
| UC-16 | extend | Hapus Kategori | Owner, Admin |
| UC-43 | extend | Lihat Laporan Stok Produk | Owner, Admin |

> **Catatan Aktor:** Kasir hanya menghubungkan garis langsung ke UC-08 (Lihat Daftar Produk) dan UC-09 (Cari Produk) karena aksesnya terbatas hanya pada fitur tersebut di modul ini.

---

### UC-U04 — Manajemen Transaksi Penjualan *(Modul 3 — Anggota 3)*

| ID UC | Jenis | Nama Use Case | Aktor |
|---|---|---|---|
| **UC-U04** | **UC Utama** | **Manajemen Transaksi Penjualan** | **Owner, Admin** |
| UC-17 | extend | Buat Transaksi Penjualan | Owner, Admin, Kasir |
| UC-18 | extend | Lihat Riwayat Transaksi | Owner, Admin, Kasir |
| UC-19 | extend | Lihat Detail Transaksi | Owner, Admin, Kasir |
| UC-20 | extend | Edit Transaksi | Owner, Admin |
| UC-21 | extend | Batalkan Transaksi | Owner, Admin |
| UC-22 | extend | Lihat Laporan Penjualan | Owner, Admin |

> **Catatan Aktor:** Kasir hanya menghubungkan garis langsung ke UC-17 (Buat Transaksi), UC-18 (Lihat Riwayat Transaksi — milik sendiri), dan UC-19 (Lihat Detail Transaksi — milik sendiri).

---

### UC-U05 — Manajemen Supplier & Pembelian *(Modul 4 — Anggota 4)*

| ID UC | Jenis | Nama Use Case | Aktor |
|---|---|---|---|
| **UC-U05** | **UC Utama** | **Manajemen Supplier & Pembelian** | **Owner, Admin** |
| UC-23 | extend | Lihat Daftar Supplier | Owner, Admin |
| UC-24 | extend | Tambah Supplier | Owner, Admin |
| UC-25 | extend | Edit Supplier | Owner, Admin |
| UC-26 | extend | Hapus Supplier | Owner, Admin |
| UC-27 | extend | Catat Restock (Pembelian) | Owner, Admin |
| UC-28 | extend | Lihat Riwayat Restock | Owner, Admin |
| UC-29 | extend | Lihat Detail Restock | Owner, Admin |
| UC-30 | extend | Edit Restock | Owner, Admin |
| UC-31 | extend | Hapus Restock | Owner, Admin |
| UC-32 | extend | Lihat Laporan Restock | Owner, Admin |

---

### UC-U06 — Manajemen Hutang Pelanggan *(Modul 5 — Anggota 5)*

| ID UC | Jenis | Nama Use Case | Aktor |
|---|---|---|---|
| **UC-U06** | **UC Utama** | **Manajemen Hutang Pelanggan** | **Owner, Admin** |
| UC-33 | extend | Lihat Daftar Pelanggan | Owner, Admin, Kasir |
| UC-34 | extend | Tambah Pelanggan | Owner, Admin, Kasir |
| UC-35 | extend | Edit Pelanggan | Owner, Admin |
| UC-36 | extend | Hapus Pelanggan | Owner, Admin |
| UC-37 | extend | Catat Hutang Baru | Owner, Admin, Kasir |
| UC-38 | extend | Lihat Daftar Hutang | Owner, Admin, Kasir |
| UC-39 | extend | Lihat Detail Hutang Pelanggan | Owner, Admin, Kasir |
| UC-40 | extend | Catat Pembayaran Hutang | Owner, Admin, Kasir |
| UC-41 | extend | Hapus Hutang | Owner, Admin |
| UC-42 | extend | Lihat Laporan Hutang | Owner, Admin |
| UC-45 | extend | Hapus Baris Produk dari Hutang | Owner, Admin |

> **Catatan Aktor:** Kasir hanya menghubungkan garis langsung ke UC-33, UC-34, UC-37, UC-38, UC-39, dan UC-40 karena aksesnya terbatas pada fitur-fitur tersebut di modul ini.

---

### Ringkasan Jumlah Use Case

| Kategori | Jumlah |
|---|---|
| UC Utama (UC-U01 s.d. UC-U06) | 6 |
| UC Fitur (UC-01 s.d. UC-45) | 45 |
| **Total** | **51** |

---

## 3. Relasi Antar Use Case

### Jenis Relasi yang Digunakan

| Relasi | Penjelasan |
|---|---|
| `<<extend>>` | UC fitur memperluas UC Utama. Digunakan dari semua UC fitur ke UC Utama modulnya. |
| `<<include>>` | UC A selalu membutuhkan UC B. Digunakan untuk dependensi fungsional antar UC fitur. |

---

### Relasi `<<extend>>` — UC Fitur ke UC Utama

| UC Fitur | Extend ke UC Utama | Modul |
|---|---|---|
| UC-01, UC-02, UC-03 | UC-U01 | Autentikasi & Dashboard |
| UC-04, UC-05, UC-06, UC-07, UC-44 | UC-U02 | Manajemen Pengguna |
| UC-08 s.d. UC-16, UC-43 | UC-U03 | Manajemen Produk & Kategori |
| UC-17 s.d. UC-22 | UC-U04 | Manajemen Transaksi Penjualan |
| UC-23 s.d. UC-32 | UC-U05 | Manajemen Supplier & Pembelian |
| UC-33 s.d. UC-42, UC-45 | UC-U06 | Manajemen Hutang Pelanggan |

---

### Relasi `<<extend>>` — Antar UC Fitur

| UC Utama Fitur | Extended oleh | Kondisi |
|---|---|---|
| UC-08 (Lihat Daftar Produk) | UC-09 (Cari Produk) | Hanya terjadi jika pengguna mengisi kolom pencarian |
| UC-17 (Buat Transaksi) | UC-21 (Batalkan Transaksi) | Hanya jika transaksi ingin dibatalkan setelah tercatat |
| UC-38 (Lihat Daftar Hutang) | UC-39 (Lihat Detail Hutang) | Hanya jika pengguna memilih satu hutang untuk melihat detailnya |
| UC-10 (Tambah Produk) | Fitur Upload Foto Produk | Hanya jika pengguna memilih mengunggah foto (fitur bonus) |
| UC-22 (Laporan Penjualan) | Fitur Export PDF/Excel | Hanya jika pengguna memilih mengekspor laporan (fitur bonus) |
| UC-42 (Laporan Hutang) | Fitur Export PDF/Excel | Hanya jika pengguna memilih mengekspor laporan (fitur bonus) |

---

### Relasi `<<include>>` — Antar UC Fitur

| Use Case | Include ke | Keterangan |
|---|---|---|
| UC-17 (Buat Transaksi) | UC-08 (Lihat Daftar Produk) | Saat buat transaksi, sistem menampilkan produk untuk dipilih |
| UC-27 (Catat Restock) | UC-23 (Lihat Daftar Supplier) | Saat catat restock, sistem menampilkan supplier untuk dipilih |
| UC-27 (Catat Restock) | UC-08 (Lihat Daftar Produk) | Saat catat restock, sistem menampilkan produk untuk dipilih |
| UC-37 (Catat Hutang Baru) | UC-33 (Lihat Daftar Pelanggan) | Saat catat hutang, sistem menampilkan pelanggan untuk dipilih |
| UC-37 (Catat Hutang Baru) | UC-08 (Lihat Daftar Produk) | Saat catat hutang, sistem menampilkan produk untuk dipilih |
| UC-40 (Catat Pembayaran) | UC-38 (Lihat Daftar Hutang) | Pembayaran hutang dilakukan dari halaman daftar hutang |

---

### Relasi Aktor ke Use Case (Ringkasan untuk Diagram)

> Aktor hanya menarik garis ke **UC Utama** jika memiliki akses penuh ke modul tersebut, atau langsung ke **UC fitur tertentu** jika aksesnya terbatas.

| Aktor | Terhubung ke UC Utama | Terhubung langsung ke UC Fitur (akses terbatas) |
|---|---|---|
| **Owner** | UC-U01, UC-U02, UC-U03, UC-U04, UC-U05, UC-U06 | — (akses penuh semua modul) |
| **Admin** | UC-U03, UC-U04, UC-U05, UC-U06 | — (akses penuh 4 modul) |
| **Kasir** | UC-U01 | UC-08, UC-09 (Modul 3); UC-17, UC-18, UC-19 (Modul 4); UC-33, UC-34, UC-37, UC-38, UC-39, UC-40 (Modul 6) |

> **Mengapa Admin tidak terhubung ke UC-U01 dan UC-U02?**
> - UC-U01 (Autentikasi & Dashboard) → semua role termasuk Admin sudah terhubung karena Admin termasuk aktor UC-U01
> - UC-U02 (Manajemen Pengguna) → Admin memang tidak punya akses ke modul ini sama sekali, jadi tidak ada garis sama sekali

---

## 4. Spesifikasi Use Case Detail

> **Keterangan kolom:**
> - **Prekondisi** — Kondisi yang harus terpenuhi sebelum use case dapat dijalankan
> - **Alur Normal** — Urutan langkah saat semua berjalan tanpa masalah (happy path)
> - **Alur Alternatif** — Variasi alur yang masih menghasilkan keberhasilan
> - **Alur Pengecualian** — Kondisi error atau kegagalan beserta respons sistem
> - **Pascakondisi** — Kondisi sistem setelah use case selesai dijalankan

---

### UC-U01: Autentikasi & Dashboard

| Atribut | Keterangan |
|---|---|
| **ID** | UC-U01 |
| **Nama** | Autentikasi & Dashboard |
| **Aktor** | Owner, Admin, Kasir |
| **Deskripsi** | Modul yang menangani akses masuk dan keluar sistem, serta halaman utama (dashboard) yang ditampilkan setelah pengguna berhasil login. Tampilan dashboard berbeda berdasarkan role pengguna. |
| **UC yang di-extend** | UC-01 (Login), UC-02 (Logout), UC-03 (Lihat Dashboard) |

---

### UC-U02: Manajemen Pengguna

| Atribut | Keterangan |
|---|---|
| **ID** | UC-U02 |
| **Nama** | Manajemen Pengguna |
| **Aktor** | Owner |
| **Deskripsi** | Modul yang mengelola akun pengguna sistem. Owner dapat melihat, menambah, mengedit, menghapus akun pengguna, serta memantau riwayat aktivitas login. |
| **UC yang di-extend** | UC-04, UC-05, UC-06, UC-07, UC-44 |

---

### UC-U03: Manajemen Produk & Kategori

| Atribut | Keterangan |
|---|---|
| **ID** | UC-U03 |
| **Nama** | Manajemen Produk & Kategori |
| **Aktor** | Owner, Admin *(akses penuh)*; Kasir *(terbatas: lihat & cari produk saja)* |
| **Deskripsi** | Modul yang mengelola data produk dan kategori barang dagangan warung. Termasuk fitur pencarian produk yang menjadi kebutuhan utama kasir untuk mengecek harga barang dengan cepat. |
| **UC yang di-extend** | UC-08, UC-09, UC-10, UC-11, UC-12, UC-13, UC-14, UC-15, UC-16, UC-43 |

---

### UC-U04: Manajemen Transaksi Penjualan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-U04 |
| **Nama** | Manajemen Transaksi Penjualan |
| **Aktor** | Owner, Admin *(akses penuh)*; Kasir *(terbatas: buat transaksi & lihat riwayat/detail milik sendiri)* |
| **Deskripsi** | Modul yang mengelola seluruh transaksi penjualan warung. Mencakup pencatatan transaksi baru, pengelolaan riwayat, dan pelaporan penjualan. |
| **UC yang di-extend** | UC-17, UC-18, UC-19, UC-20, UC-21, UC-22 |

---

### UC-U05: Manajemen Supplier & Pembelian

| Atribut | Keterangan |
|---|---|
| **ID** | UC-U05 |
| **Nama** | Manajemen Supplier & Pembelian |
| **Aktor** | Owner, Admin |
| **Deskripsi** | Modul yang mengelola data supplier/agen dan catatan restock barang. Menggantikan struk pembelian fisik yang rawan hilang atau rusak. |
| **UC yang di-extend** | UC-23, UC-24, UC-25, UC-26, UC-27, UC-28, UC-29, UC-30, UC-31, UC-32 |

---

### UC-U06: Manajemen Hutang Pelanggan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-U06 |
| **Nama** | Manajemen Hutang Pelanggan |
| **Aktor** | Owner, Admin *(akses penuh)*; Kasir *(terbatas: lihat pelanggan, tambah pelanggan, catat hutang, lihat & bayar hutang)* |
| **Deskripsi** | Modul yang mengelola data pelanggan dan catatan hutang. Mencatat produk yang diambil beserta rinciannya sehingga nominal hutang transparan dan stok berkurang otomatis. |
| **UC yang di-extend** | UC-33, UC-34, UC-35, UC-36, UC-37, UC-38, UC-39, UC-40, UC-41, UC-42, UC-45 |

---

### UC-01: Login

| Atribut | Keterangan |
|---|---|
| **ID** | UC-01 |
| **Nama** | Login |
| **Aktor** | Owner, Admin, Kasir |
| **Deskripsi** | Pengguna memasukkan kredensial untuk mengakses sistem |
| **Relasi** | `<<extend>>` UC-U01 |
| **Prekondisi** | Pengguna belum login. Halaman login terbuka di browser. Akun pengguna sudah terdaftar di database. |
| **Pascakondisi** | Session login tersimpan. Pengguna diarahkan ke halaman dashboard sesuai role-nya. |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna membuka halaman login (`login.php`) | Sistem menampilkan form login dengan field username dan password |
| 2 | Pengguna mengisi username dan password lalu menekan tombol "Login" | — |
| 3 | — | Sistem menerima input dan memvalidasi: field tidak boleh kosong |
| 4 | — | Sistem mencari username di database |
| 5 | — | Sistem memverifikasi password dengan `password_verify()` |
| 6 | — | Sistem menyimpan data sesi (`$_SESSION['id_user']`, `$_SESSION['role']`, `$_SESSION['nama']`) |
| 7 | — | Sistem mencatat aktivitas login ke tabel `log_login` dengan status "berhasil" |
| 8 | — | Sistem mengarahkan (redirect) pengguna ke `dashboard.php` |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Field username atau password kosong | Sistem menampilkan pesan: "Username dan password wajib diisi." |
| Username tidak ditemukan di database | Sistem mencatat ke `log_login` status "gagal", menampilkan pesan: "Username atau password salah." |
| Password tidak cocok | Sistem mencatat ke `log_login` status "gagal", menampilkan pesan: "Username atau password salah." |
| Pengguna sudah login dan mencoba membuka halaman login | Sistem redirect langsung ke `dashboard.php` |

---

### UC-02: Logout

| Atribut | Keterangan |
|---|---|
| **ID** | UC-02 |
| **Nama** | Logout |
| **Aktor** | Owner, Admin, Kasir |
| **Deskripsi** | Pengguna mengakhiri sesi dan keluar dari sistem |
| **Relasi** | `<<extend>>` UC-U01 |
| **Prekondisi** | Pengguna sudah login dan berada di halaman mana pun dalam sistem |
| **Pascakondisi** | Semua data sesi dihapus. Pengguna diarahkan ke halaman login. |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan tombol/menu "Logout" | — |
| 2 | — | Sistem menjalankan `session_unset()` dan `session_destroy()` |
| 3 | — | Sistem mengarahkan pengguna ke `login.php` |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Pengguna mencoba mengakses halaman lain setelah logout | Sistem mendeteksi tidak ada session aktif dan redirect ke `login.php` |

---

### UC-03: Lihat Dashboard

| Atribut | Keterangan |
|---|---|
| **ID** | UC-03 |
| **Nama** | Lihat Dashboard |
| **Aktor** | Owner, Admin, Kasir |
| **Deskripsi** | Semua pengguna diarahkan ke `dashboard.php` setelah login. Konten berbeda berdasarkan role: Owner/Admin melihat statistik bisnis warung, Kasir melihat halaman kerja harian. |
| **Relasi** | `<<extend>>` UC-U01 |
| **Prekondisi** | Pengguna sudah login dengan role apapun |
| **Pascakondisi** | Halaman dashboard ditampilkan dengan konten yang sesuai role pengguna |

**Alur Normal — Bercabang per Role:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna berhasil login atau mengklik menu "Dashboard" | — |
| 2 | — | Sistem memeriksa `$_SESSION['role']` untuk menentukan konten yang akan dirender |
| 3 | — | **Jika role Owner atau Admin:** sistem mengambil data statistik bisnis: total produk aktif, jumlah produk stok kritis, total penjualan hari ini (Rp), jumlah transaksi hari ini, total hutang aktif (Rp), jumlah pelanggan berutang |
| 4 | — | **Jika role Owner atau Admin:** sistem menampilkan data tersebut dalam kartu-kartu ringkasan (widget statistik) |
| 5 | — | **Jika role Kasir:** sistem mengambil jumlah transaksi yang dibuat kasir ini hari ini |
| 6 | — | **Jika role Kasir:** sistem menampilkan sapaan personal, tombol besar "Buat Transaksi Baru", kolom pencarian produk cepat, ringkasan transaksi harian, dan shortcut menu |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Data belum ada / database kosong (Owner/Admin) | Sistem menampilkan nilai 0 atau "Belum ada data" pada setiap widget |
| Kasir belum membuat transaksi hari ini | Widget menampilkan "0 transaksi hari ini" |

---

### UC-04: Lihat Daftar Pengguna

| Atribut | Keterangan |
|---|---|
| **ID** | UC-04 |
| **Nama** | Lihat Daftar Pengguna |
| **Aktor** | Owner |
| **Relasi** | `<<extend>>` UC-U02 |
| **Prekondisi** | Pengguna login dengan role Owner |
| **Pascakondisi** | Daftar pengguna ditampilkan dalam bentuk tabel |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Owner mengklik menu "Manajemen Pengguna" | — |
| 2 | — | Sistem mengambil semua data dari tabel `users` |
| 3 | — | Sistem menampilkan tabel: nomor, nama lengkap, username, role, dan kolom aksi (edit, hapus) |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Selain Owner mencoba mengakses halaman ini | Sistem menampilkan pesan "Akses ditolak" dan redirect ke dashboard |

---

### UC-05: Tambah Pengguna

| Atribut | Keterangan |
|---|---|
| **ID** | UC-05 |
| **Nama** | Tambah Pengguna |
| **Aktor** | Owner |
| **Relasi** | `<<extend>>` UC-U02 |
| **Prekondisi** | Pengguna login dengan role Owner |
| **Pascakondisi** | Akun pengguna baru tersimpan di database dengan password yang sudah di-hash |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Owner menekan tombol "Tambah Pengguna" | Sistem menampilkan form: nama lengkap, username, password, konfirmasi password, role |
| 2 | Owner mengisi seluruh field dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi semua field wajib terisi |
| 4 | — | Sistem memvalidasi username belum digunakan |
| 5 | — | Sistem memvalidasi password dan konfirmasi password cocok |
| 6 | — | Sistem meng-hash password menggunakan `password_hash()` |
| 7 | — | Sistem menyimpan data ke tabel `users` |
| 8 | — | Sistem menampilkan pesan sukses dan redirect ke daftar pengguna |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Ada field wajib yang kosong | Sistem menampilkan pesan error di dekat field kosong |
| Username sudah digunakan | Sistem menampilkan pesan: "Username sudah digunakan, pilih username lain." |
| Password dan konfirmasi tidak cocok | Sistem menampilkan pesan: "Konfirmasi password tidak cocok." |

---

### UC-06: Edit Pengguna

| Atribut | Keterangan |
|---|---|
| **ID** | UC-06 |
| **Nama** | Edit Pengguna |
| **Aktor** | Owner |
| **Relasi** | `<<extend>>` UC-U02 |
| **Prekondisi** | Pengguna login dengan role Owner. Data pengguna yang ingin diedit sudah ada. |
| **Pascakondisi** | Data pengguna berhasil diperbarui di database |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Owner menekan tombol "Edit" pada salah satu baris pengguna | Sistem menampilkan form edit yang sudah terisi data pengguna |
| 2 | Owner mengubah data yang diinginkan lalu menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi semua field wajib terisi |
| 4 | — | Sistem memvalidasi username tidak bentrok dengan pengguna lain |
| 5 | — | Jika field password diisi, sistem validasi cocok dengan konfirmasi lalu hash ulang; jika dikosongkan, password lama dipertahankan |
| 6 | — | Sistem menyimpan perubahan ke tabel `users` |
| 7 | — | Sistem menampilkan pesan sukses dan redirect ke daftar pengguna |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Username baru sudah dipakai pengguna lain | Sistem menampilkan pesan: "Username sudah digunakan pengguna lain." |
| Password dan konfirmasi tidak cocok | Sistem menampilkan pesan: "Konfirmasi password tidak cocok." |

---

### UC-07: Hapus Pengguna

| Atribut | Keterangan |
|---|---|
| **ID** | UC-07 |
| **Nama** | Hapus Pengguna |
| **Aktor** | Owner |
| **Relasi** | `<<extend>>` UC-U02 |
| **Prekondisi** | Pengguna login dengan role Owner. Data pengguna yang ingin dihapus sudah ada. |
| **Pascakondisi** | Data pengguna dihapus dari tabel `users` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Owner menekan tombol "Hapus" pada salah satu baris pengguna | Sistem menampilkan konfirmasi: "Apakah Anda yakin ingin menghapus pengguna ini?" |
| 2 | Owner mengklik "Ya, Hapus" | — |
| 3 | — | Sistem menghapus data pengguna dari tabel `users` |
| 4 | — | Sistem menampilkan pesan sukses: "Pengguna berhasil dihapus." |

**Alur Alternatif:**

| Kondisi | Alur |
|---|---|
| Owner mengklik "Batal" | Sistem menutup konfirmasi, tidak ada perubahan |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Owner mencoba menghapus akun diri sendiri | Sistem menampilkan pesan: "Anda tidak dapat menghapus akun Anda sendiri." |

---

### UC-08: Lihat Daftar Produk

| Atribut | Keterangan |
|---|---|
| **ID** | UC-08 |
| **Nama** | Lihat Daftar Produk |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna sudah login dengan role apapun |
| **Pascakondisi** | Daftar produk ditampilkan dalam bentuk tabel |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Produk" | — |
| 2 | — | Sistem mengambil semua data produk beserta nama kategorinya (JOIN `produk` dan `kategori`) |
| 3 | — | Sistem menampilkan tabel: nomor, nama produk, kategori, harga jual, stok, satuan, dan status stok (normal / kritis) |
| 4 | — | Sistem memberikan tanda visual pada produk yang stoknya di bawah stok minimum |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Belum ada produk di database | Sistem menampilkan tabel kosong dengan keterangan "Belum ada produk terdaftar" |

---

### UC-09: Cari Produk

| Atribut | Keterangan |
|---|---|
| **ID** | UC-09 |
| **Nama** | Cari Produk |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U03; `<<extend>>` UC-08 |
| **Deskripsi** | Pengguna mencari produk berdasarkan nama untuk menemukan harga dengan cepat. Ini adalah fitur utama untuk kasir yang tidak hafal harga. |
| **Prekondisi** | Pengguna sudah login. Halaman daftar produk sudah terbuka. |
| **Pascakondisi** | Tabel produk menampilkan hanya produk yang sesuai kata kunci |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengetikkan kata kunci pada kolom pencarian | — |
| 2 | — | Sistem memproses query dengan `LIKE '%kata_kunci%'` pada kolom `nama_produk` |
| 3 | — | Sistem memperbarui tabel dan hanya menampilkan produk yang cocok |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Tidak ada produk yang cocok | Sistem menampilkan: "Produk tidak ditemukan." |

---

### UC-10: Tambah Produk

| Atribut | Keterangan |
|---|---|
| **ID** | UC-10 |
| **Nama** | Tambah Produk |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna login dengan role Owner atau Admin. Minimal satu kategori sudah ada. |
| **Pascakondisi** | Data produk baru tersimpan di tabel `produk` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan tombol "Tambah Produk" | Sistem menampilkan form: nama produk, kategori, harga beli, harga jual, stok awal, stok minimum, satuan, foto (opsional) |
| 2 | Pengguna mengisi field dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi field wajib tidak kosong dan nilai tidak negatif |
| 4 | — | Sistem menyimpan data ke tabel `produk` |
| 5 | — | Sistem menampilkan pesan sukses dan redirect ke daftar produk |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Nama produk kosong | Sistem menampilkan pesan: "Nama produk wajib diisi." |
| Harga atau stok negatif | Sistem menampilkan pesan: "Nilai tidak boleh negatif." |

---

### UC-11: Edit Produk

| Atribut | Keterangan |
|---|---|
| **ID** | UC-11 |
| **Nama** | Edit Produk |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna login Owner/Admin. Data produk sudah ada. |
| **Pascakondisi** | Data produk berhasil diperbarui |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Edit" pada baris produk | Sistem menampilkan form edit yang terisi data produk |
| 2 | Pengguna mengubah data dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi input |
| 4 | — | Sistem menyimpan perubahan ke tabel `produk` |
| 5 | — | Sistem menampilkan pesan sukses |

---

### UC-12: Hapus Produk

| Atribut | Keterangan |
|---|---|
| **ID** | UC-12 |
| **Nama** | Hapus Produk |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna login Owner/Admin. Data produk sudah ada. |
| **Pascakondisi** | Data produk dihapus dari tabel `produk` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Hapus" pada baris produk | Sistem menampilkan konfirmasi penghapusan |
| 2 | Pengguna mengklik "Ya, Hapus" | — |
| 3 | — | Sistem menghapus data dari tabel `produk` |
| 4 | — | Sistem menampilkan pesan sukses |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Produk masih terkait dengan riwayat transaksi | Sistem menampilkan pesan: "Produk tidak dapat dihapus karena masih memiliki riwayat transaksi." |

---

### UC-13: Lihat Daftar Kategori

| Atribut | Keterangan |
|---|---|
| **ID** | UC-13 |
| **Nama** | Lihat Daftar Kategori |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Daftar kategori ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik submenu "Kategori" | — |
| 2 | — | Sistem mengambil semua data dari tabel `kategori` |
| 3 | — | Sistem menampilkan tabel: nomor, nama kategori, deskripsi, dan kolom aksi |

---

### UC-14: Tambah Kategori

| Atribut | Keterangan |
|---|---|
| **ID** | UC-14 |
| **Nama** | Tambah Kategori |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Kategori baru tersimpan di tabel `kategori` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Tambah Kategori" | Sistem menampilkan form: nama kategori (wajib), deskripsi (opsional) |
| 2 | Pengguna mengisi form dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi nama kategori tidak kosong dan belum ada |
| 4 | — | Sistem menyimpan ke tabel `kategori` dan menampilkan pesan sukses |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Nama kategori kosong | Sistem menampilkan pesan: "Nama kategori wajib diisi." |
| Nama kategori sudah ada | Sistem menampilkan pesan: "Kategori sudah terdaftar." |

---

### UC-15: Edit Kategori

| Atribut | Keterangan |
|---|---|
| **ID** | UC-15 |
| **Nama** | Edit Kategori |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna login Owner/Admin. Kategori sudah ada. |
| **Pascakondisi** | Data kategori berhasil diperbarui |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Edit" pada baris kategori | Sistem menampilkan form edit yang terisi data kategori |
| 2 | Pengguna mengubah data dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi dan menyimpan perubahan ke tabel `kategori` |

---

### UC-16: Hapus Kategori

| Atribut | Keterangan |
|---|---|
| **ID** | UC-16 |
| **Nama** | Hapus Kategori |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna login Owner/Admin. Kategori sudah ada. |
| **Pascakondisi** | Kategori dihapus dari tabel `kategori` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Hapus" pada baris kategori | Sistem menampilkan konfirmasi penghapusan |
| 2 | Pengguna mengklik "Ya, Hapus" | — |
| 3 | — | Sistem menghapus data dari tabel `kategori` dan menampilkan pesan sukses |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Kategori masih digunakan produk | Sistem menampilkan pesan: "Kategori tidak dapat dihapus karena masih digunakan oleh produk." |

---

### UC-17: Buat Transaksi Penjualan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-17 |
| **Nama** | Buat Transaksi Penjualan |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U04; `<<include>>` UC-08 |
| **Prekondisi** | Pengguna sudah login. Minimal satu produk terdaftar dengan stok > 0. |
| **Pascakondisi** | Transaksi tersimpan di `transaksi` dan `detail_transaksi`. Stok produk berkurang otomatis. |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Transaksi Baru" | Sistem menampilkan halaman transaksi dengan daftar produk |
| 2 | Pengguna memilih produk dan memasukkan jumlah | Sistem menghitung subtotal otomatis |
| 3 | Pengguna menambahkan beberapa produk sesuai kebutuhan | Sistem menghitung total keseluruhan otomatis |
| 4 | Pengguna memasukkan uang bayar | Sistem menghitung kembalian otomatis |
| 5 | Pengguna menekan "Selesaikan Transaksi" | — |
| 6 | — | Sistem memvalidasi: minimal satu produk dipilih, jumlah > 0, uang bayar ≥ total |
| 7 | — | Sistem men-generate kode transaksi unik |
| 8 | — | Sistem menyimpan ke tabel `transaksi` dan `detail_transaksi` (dengan snapshot harga) |
| 9 | — | Sistem mengurangi stok setiap produk yang terjual |
| 10 | — | Sistem menampilkan struk/ringkasan transaksi dan pesan sukses |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Tidak ada produk dipilih | Sistem menampilkan pesan: "Pilih minimal satu produk." |
| Jumlah melebihi stok | Sistem menampilkan pesan: "Stok [nama produk] tidak mencukupi. Tersedia: [n]." |
| Uang bayar kurang dari total | Sistem menampilkan pesan: "Uang bayar kurang dari total harga." |

---

### UC-18: Lihat Riwayat Transaksi

| Atribut | Keterangan |
|---|---|
| **ID** | UC-18 |
| **Nama** | Lihat Riwayat Transaksi |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U04 |
| **Deskripsi** | Owner/Admin melihat seluruh transaksi. Kasir hanya melihat transaksi milik sendiri. |
| **Prekondisi** | Pengguna sudah login |
| **Pascakondisi** | Daftar transaksi ditampilkan sesuai hak akses role |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Riwayat Transaksi" | — |
| 2 | — | Sistem memeriksa `$_SESSION['role']` dan `$_SESSION['id_user']` |
| 3 | — | **Jika Owner/Admin:** sistem mengambil seluruh transaksi dari semua user |
| 4 | — | **Jika Kasir:** sistem mengambil hanya transaksi dengan `WHERE id_user = $_SESSION['id_user']` |
| 5 | — | Sistem menampilkan tabel: kode transaksi, tanggal, kasir, total, status, dan kolom aksi |

**Alur Alternatif:**

| Kondisi | Alur |
|---|---|
| Pengguna memfilter berdasarkan tanggal | Sistem menambahkan filter tanggal pada query, tetap menerapkan pembatasan role |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Kasir mencoba akses transaksi orang lain via URL | Sistem menampilkan "Akses ditolak" dan redirect ke riwayat milik sendiri |

---

### UC-19: Lihat Detail Transaksi

| Atribut | Keterangan |
|---|---|
| **ID** | UC-19 |
| **Nama** | Lihat Detail Transaksi |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U04 |
| **Deskripsi** | Kasir hanya dapat melihat detail transaksi milik sendiri. Owner/Admin dapat melihat semua. |
| **Prekondisi** | Pengguna sudah login. Transaksi yang dimaksud sudah ada. |
| **Pascakondisi** | Detail transaksi ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Detail" pada baris transaksi | — |
| 2 | — | Sistem mengambil data dari tabel `transaksi` berdasarkan ID |
| 3 | — | **Jika Kasir:** sistem memvalidasi `id_user` pada transaksi = `$_SESSION['id_user']` |
| 4 | — | Sistem mengambil data detail dari `detail_transaksi` JOIN `produk` |
| 5 | — | Sistem menampilkan: kode, tanggal, kasir, daftar produk, total, uang bayar, kembalian, status |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Kasir mengakses detail transaksi milik orang lain | Sistem menampilkan "Akses ditolak. Anda hanya dapat melihat transaksi milik Anda." |
| ID transaksi tidak ditemukan | Sistem menampilkan "Transaksi tidak ditemukan." |

---

### UC-20: Edit Transaksi

| Atribut | Keterangan |
|---|---|
| **ID** | UC-20 |
| **Nama** | Edit Transaksi |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U04 |
| **Prekondisi** | Pengguna login Owner/Admin. Transaksi berstatus "selesai". |
| **Pascakondisi** | Data transaksi diperbarui, stok disesuaikan kembali |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Edit" pada baris transaksi | Sistem menampilkan form edit dengan data transaksi |
| 2 | Pengguna mengubah data dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi perubahan (stok mencukupi, nilai valid) |
| 4 | — | Sistem mengembalikan stok lama (rollback) |
| 5 | — | Sistem menyimpan data baru dan mengurangi stok sesuai data diperbarui |
| 6 | — | Sistem menampilkan pesan sukses |

---

### UC-21: Batalkan Transaksi

| Atribut | Keterangan |
|---|---|
| **ID** | UC-21 |
| **Nama** | Batalkan Transaksi |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U04; `<<extend>>` UC-17 |
| **Prekondisi** | Pengguna login Owner/Admin. Transaksi berstatus "selesai". |
| **Pascakondisi** | Status transaksi berubah menjadi "batal". Stok dikembalikan. |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Batal" pada baris transaksi | Sistem menampilkan konfirmasi: "Transaksi akan dibatalkan dan stok dikembalikan. Lanjutkan?" |
| 2 | Pengguna mengklik "Ya, Batalkan" | — |
| 3 | — | Sistem mengubah status transaksi menjadi "batal" |
| 4 | — | Sistem mengembalikan stok setiap produk di `detail_transaksi` transaksi tersebut |
| 5 | — | Sistem menampilkan pesan sukses |

---

### UC-22: Lihat Laporan Penjualan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-22 |
| **Nama** | Lihat Laporan Penjualan |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U04 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Laporan penjualan ditampilkan sesuai filter periode |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Laporan Penjualan" | Sistem menampilkan form filter: tanggal mulai dan akhir |
| 2 | Pengguna memilih rentang tanggal dan menekan "Tampilkan" | — |
| 3 | — | Sistem mengambil transaksi berstatus "selesai" pada rentang tanggal tersebut |
| 4 | — | Sistem menampilkan: daftar transaksi, total pendapatan, jumlah transaksi, dan produk terlaris |

---

### UC-23: Lihat Daftar Supplier

| Atribut | Keterangan |
|---|---|
| **ID** | UC-23 |
| **Nama** | Lihat Daftar Supplier |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Daftar supplier ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Supplier" | — |
| 2 | — | Sistem mengambil semua data dari tabel `supplier` |
| 3 | — | Sistem menampilkan tabel: nama supplier, kontak, telepon, alamat, kolom aksi |

---

### UC-24: Tambah Supplier

| Atribut | Keterangan |
|---|---|
| **ID** | UC-24 |
| **Nama** | Tambah Supplier |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Data supplier baru tersimpan di tabel `supplier` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Tambah Supplier" | Sistem menampilkan form: nama supplier (wajib), nama kontak, telepon, alamat |
| 2 | Pengguna mengisi form dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi nama supplier tidak kosong |
| 4 | — | Sistem menyimpan ke tabel `supplier` dan menampilkan pesan sukses |

---

### UC-25: Edit Supplier

| Atribut | Keterangan |
|---|---|
| **ID** | UC-25 |
| **Nama** | Edit Supplier |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin. Data supplier sudah ada. |
| **Pascakondisi** | Data supplier berhasil diperbarui |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Edit" pada baris supplier | Sistem menampilkan form edit yang terisi data supplier |
| 2 | Pengguna mengubah data dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi dan menyimpan perubahan ke tabel `supplier` |

---

### UC-26: Hapus Supplier

| Atribut | Keterangan |
|---|---|
| **ID** | UC-26 |
| **Nama** | Hapus Supplier |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin. Data supplier sudah ada. |
| **Pascakondisi** | Data supplier dihapus dari tabel `supplier` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Hapus" pada baris supplier | Sistem menampilkan konfirmasi penghapusan |
| 2 | Pengguna mengklik "Ya, Hapus" | — |
| 3 | — | Sistem menghapus data dari tabel `supplier` dan menampilkan pesan sukses |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Supplier masih memiliki riwayat pembelian | Sistem menampilkan pesan: "Supplier tidak dapat dihapus karena masih memiliki riwayat restock." |

---

### UC-27: Catat Restock (Pembelian)

| Atribut | Keterangan |
|---|---|
| **ID** | UC-27 |
| **Nama** | Catat Restock (Pembelian) |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05; `<<include>>` UC-23; `<<include>>` UC-08 |
| **Prekondisi** | Pengguna login Owner/Admin. Minimal satu supplier dan produk sudah terdaftar. |
| **Pascakondisi** | Data pembelian tersimpan di `pembelian` dan `detail_pembelian`. Stok produk bertambah otomatis. |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik "Catat Restock" | Sistem menampilkan form: pilih supplier, pilih produk, jumlah, harga beli |
| 2 | Pengguna memilih supplier dan menambahkan produk-produk yang dibeli | Sistem menghitung subtotal dan total otomatis |
| 3 | Pengguna menambahkan keterangan (opsional) dan menekan "Simpan" | — |
| 4 | — | Sistem memvalidasi: supplier dipilih, minimal satu produk, jumlah > 0 |
| 5 | — | Sistem men-generate kode pembelian unik |
| 6 | — | Sistem menyimpan ke tabel `pembelian` dan `detail_pembelian` |
| 7 | — | Sistem menambahkan stok setiap produk yang dibeli |
| 8 | — | Sistem menampilkan pesan sukses |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Supplier tidak dipilih | Sistem menampilkan pesan: "Pilih supplier terlebih dahulu." |
| Tidak ada produk yang ditambahkan | Sistem menampilkan pesan: "Tambahkan minimal satu produk." |

---

### UC-28: Lihat Riwayat Restock

| Atribut | Keterangan |
|---|---|
| **ID** | UC-28 |
| **Nama** | Lihat Riwayat Restock |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Daftar riwayat restock ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Riwayat Restock" | — |
| 2 | — | Sistem mengambil data dari tabel `pembelian` JOIN `supplier` |
| 3 | — | Sistem menampilkan tabel: kode pembelian, tanggal, supplier, total, kolom aksi |

---

### UC-29: Lihat Detail Restock

| Atribut | Keterangan |
|---|---|
| **ID** | UC-29 |
| **Nama** | Lihat Detail Restock |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin. Data restock sudah ada. |
| **Pascakondisi** | Detail restock ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Detail" pada baris restock | — |
| 2 | — | Sistem mengambil data dari `pembelian` dan `detail_pembelian` |
| 3 | — | Sistem menampilkan: kode, tanggal, supplier, daftar produk (nama, jumlah, harga beli, subtotal), total, keterangan |

---

### UC-30: Edit Restock

| Atribut | Keterangan |
|---|---|
| **ID** | UC-30 |
| **Nama** | Edit Restock |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin. Data restock sudah ada. |
| **Pascakondisi** | Data restock diperbarui, stok disesuaikan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Edit" pada baris restock | Sistem menampilkan form edit dengan data restock |
| 2 | Pengguna mengubah data dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi input |
| 4 | — | Sistem mengembalikan stok lama (rollback penambahan sebelumnya) |
| 5 | — | Sistem menyimpan data baru dan menambah stok sesuai data diperbarui |
| 6 | — | Sistem menampilkan pesan sukses |

---

### UC-31: Hapus Restock

| Atribut | Keterangan |
|---|---|
| **ID** | UC-31 |
| **Nama** | Hapus Restock |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin. Data restock sudah ada. |
| **Pascakondisi** | Catatan restock dihapus. Stok produk terkait dikurangi kembali (rollback). |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Hapus" pada baris restock | Sistem menampilkan konfirmasi: "Menghapus restock ini akan mengurangi stok produk terkait. Lanjutkan?" |
| 2 | Pengguna mengklik "Ya, Hapus" | — |
| 3 | — | Sistem mengurangi stok produk terkait (rollback) |
| 4 | — | Sistem menghapus data dari `detail_pembelian` dan `pembelian` |
| 5 | — | Sistem menampilkan pesan sukses |

---

### UC-32: Lihat Laporan Restock

| Atribut | Keterangan |
|---|---|
| **ID** | UC-32 |
| **Nama** | Lihat Laporan Restock |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U05 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Laporan restock ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik "Laporan Restock" | Sistem menampilkan form filter: rentang tanggal dan/atau supplier |
| 2 | Pengguna memilih filter dan menekan "Tampilkan" | — |
| 3 | — | Sistem mengambil data restock sesuai filter |
| 4 | — | Sistem menampilkan: daftar restock, total pengeluaran per periode, rincian per supplier |

---

### UC-33: Lihat Daftar Pelanggan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-33 |
| **Nama** | Lihat Daftar Pelanggan |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U06 |
| **Prekondisi** | Pengguna sudah login |
| **Pascakondisi** | Daftar pelanggan ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Pelanggan" | — |
| 2 | — | Sistem mengambil semua data dari tabel `pelanggan` |
| 3 | — | Sistem menampilkan tabel: nama, alamat/keterangan, telepon, kolom aksi |

---

### UC-34: Tambah Pelanggan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-34 |
| **Nama** | Tambah Pelanggan |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U06 |
| **Prekondisi** | Pengguna sudah login |
| **Pascakondisi** | Data pelanggan baru tersimpan di tabel `pelanggan` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Tambah Pelanggan" | Sistem menampilkan form: nama (wajib), telepon (opsional), alamat/keterangan (opsional) |
| 2 | Pengguna mengisi form dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi nama pelanggan tidak kosong |
| 4 | — | Sistem menyimpan ke tabel `pelanggan` dan menampilkan pesan sukses |

---

### UC-35: Edit Pelanggan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-35 |
| **Nama** | Edit Pelanggan |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U06 |
| **Prekondisi** | Pengguna login Owner/Admin. Data pelanggan sudah ada. |
| **Pascakondisi** | Data pelanggan berhasil diperbarui |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Edit" pada baris pelanggan | Sistem menampilkan form edit yang terisi data pelanggan |
| 2 | Pengguna mengubah data dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi dan menyimpan perubahan ke tabel `pelanggan` |

---

### UC-36: Hapus Pelanggan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-36 |
| **Nama** | Hapus Pelanggan |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U06 |
| **Prekondisi** | Pengguna login Owner/Admin. Data pelanggan sudah ada. |
| **Pascakondisi** | Data pelanggan dihapus dari tabel `pelanggan` |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Hapus" pada baris pelanggan | Sistem menampilkan konfirmasi penghapusan |
| 2 | Pengguna mengklik "Ya, Hapus" | — |
| 3 | — | Sistem menghapus data dari tabel `pelanggan` dan menampilkan pesan sukses |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Pelanggan masih memiliki hutang aktif | Sistem menampilkan pesan: "Pelanggan tidak dapat dihapus karena masih memiliki hutang yang belum lunas." |

---

### UC-37: Catat Hutang Baru

| Atribut | Keterangan |
|---|---|
| **ID** | UC-37 |
| **Nama** | Catat Hutang Baru |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U06; `<<include>>` UC-33; `<<include>>` UC-08 |
| **Deskripsi** | Pengguna mencatat hutang baru dengan memilih produk yang diambil pelanggan. Prosesnya mirip transaksi — stok berkurang otomatis dan rincian per produk tersimpan di `detail_hutang`. |
| **Prekondisi** | Pengguna sudah login. Minimal satu pelanggan dan produk sudah terdaftar. |
| **Pascakondisi** | Header hutang tersimpan di `hutang` (status "aktif"). Rincian produk tersimpan di `detail_hutang`. Stok berkurang otomatis. |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik "Catat Hutang Baru" | Sistem menampilkan halaman: pilih pelanggan, pilih produk, jumlah, tanggal, keterangan opsional |
| 2 | Pengguna memilih pelanggan | — |
| 3 | Pengguna memilih produk dan memasukkan jumlah | Sistem menghitung subtotal dan total hutang otomatis |
| 4 | Pengguna menekan "Simpan" | — |
| 5 | — | Sistem memvalidasi: pelanggan dipilih, minimal satu produk, jumlah > 0, tanggal valid |
| 6 | — | Sistem men-generate kode hutang unik |
| 7 | — | Sistem menyimpan ke tabel `hutang` (status "aktif", `jumlah_terbayar = 0`) |
| 8 | — | Sistem menyimpan setiap baris produk ke `detail_hutang` (dengan snapshot `harga_satuan`) |
| 9 | — | Sistem mengurangi stok setiap produk yang diambil |
| 10 | — | Sistem menampilkan pesan sukses: "Hutang berhasil dicatat." |

**Alur Alternatif:**

| Kondisi | Alur |
|---|---|
| Pelanggan belum terdaftar | Pengguna mengklik "Tambah Pelanggan Baru" (UC-34) lalu kembali mencatat hutang |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Pelanggan tidak dipilih | Sistem menampilkan pesan: "Pilih pelanggan terlebih dahulu." |
| Tidak ada produk yang dipilih | Sistem menampilkan pesan: "Tambahkan minimal satu produk." |
| Jumlah melebihi stok | Sistem menampilkan pesan: "Stok [nama produk] tidak mencukupi. Tersedia: [n]." |

---

### UC-38: Lihat Daftar Hutang

| Atribut | Keterangan |
|---|---|
| **ID** | UC-38 |
| **Nama** | Lihat Daftar Hutang |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U06 |
| **Prekondisi** | Pengguna sudah login |
| **Pascakondisi** | Daftar hutang ditampilkan dalam bentuk tabel |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Daftar Hutang" | — |
| 2 | — | Sistem mengambil data dari tabel `hutang` JOIN `pelanggan` |
| 3 | — | Sistem menampilkan tabel: nama pelanggan, tanggal hutang, total hutang, sudah dibayar, sisa hutang, status, kolom aksi |
| 4 | — | Sistem menandai hutang aktif dan lunas dengan warna berbeda |

**Alur Alternatif:**

| Kondisi | Alur |
|---|---|
| Pengguna memfilter hanya hutang aktif | Sistem hanya menampilkan baris dengan `status = 'aktif'` |

---

### UC-39: Lihat Detail Hutang Pelanggan

| Atribut | Keterangan |
|---|---|
| **ID** | UC-39 |
| **Nama** | Lihat Detail Hutang Pelanggan |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U06; `<<extend>>` UC-38 |
| **Deskripsi** | Menampilkan rincian lengkap hutang termasuk daftar produk yang diambil — sebagai transparansi ke pelanggan |
| **Prekondisi** | Pengguna sudah login. Data hutang sudah ada. |
| **Pascakondisi** | Detail hutang pelanggan ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Detail" pada baris hutang | — |
| 2 | — | Sistem mengambil data header dari `hutang` JOIN `pelanggan` |
| 3 | — | Sistem mengambil data rincian dari `detail_hutang` JOIN `produk` |
| 4 | — | Sistem menampilkan: nama pelanggan, tanggal, daftar produk (nama, jumlah, harga satuan, subtotal), total hutang, terbayar, sisa, status, tanggal lunas |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| ID hutang tidak ditemukan | Sistem menampilkan "Data hutang tidak ditemukan." dan redirect ke daftar hutang |

---

### UC-40: Catat Pembayaran Hutang

| Atribut | Keterangan |
|---|---|
| **ID** | UC-40 |
| **Nama** | Catat Pembayaran Hutang |
| **Aktor** | Owner, Admin, Kasir |
| **Relasi** | `<<extend>>` UC-U06; `<<include>>` UC-38 |
| **Prekondisi** | Pengguna sudah login. Hutang pelanggan berstatus "aktif". |
| **Pascakondisi** | `jumlah_terbayar` bertambah, `sisa_hutang` berkurang. Jika lunas, status berubah menjadi "lunas". |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Bayar" pada baris hutang | Sistem menampilkan form: sisa hutang saat ini, field jumlah yang dibayar |
| 2 | Pengguna memasukkan jumlah pembayaran dan menekan "Simpan" | — |
| 3 | — | Sistem memvalidasi: jumlah > 0 dan tidak melebihi sisa hutang |
| 4 | — | Sistem memperbarui `jumlah_terbayar` dan menghitung ulang `sisa_hutang` |
| 5 | — | Jika `sisa_hutang = 0`: sistem mengubah status menjadi "lunas" dan mengisi `tanggal_lunas` |
| 6 | — | Sistem menampilkan pesan sukses: "Pembayaran berhasil dicatat." |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Jumlah pembayaran nol atau negatif | Sistem menampilkan pesan: "Jumlah pembayaran harus lebih dari nol." |
| Jumlah melebihi sisa hutang | Sistem menampilkan pesan: "Jumlah tidak boleh melebihi sisa hutang sebesar Rp [n]." |

---

### UC-41: Hapus Hutang

| Atribut | Keterangan |
|---|---|
| **ID** | UC-41 |
| **Nama** | Hapus Hutang |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U06 |
| **Prekondisi** | Pengguna login Owner/Admin. Data hutang sudah ada. |
| **Pascakondisi** | Catatan hutang dihapus. Stok produk terkait dikembalikan (rollback). |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Hapus" pada baris hutang | Sistem menampilkan konfirmasi: "Yakin ingin menghapus? Stok produk terkait akan dikembalikan." |
| 2 | Pengguna mengklik "Ya, Hapus" | — |
| 3 | — | Sistem mengembalikan stok setiap produk di `detail_hutang` (rollback) |
| 4 | — | Sistem menghapus semua baris di `detail_hutang` yang terkait |
| 5 | — | Sistem menghapus data header dari tabel `hutang` |
| 6 | — | Sistem menampilkan pesan sukses |

---

### UC-42: Lihat Laporan Hutang

| Atribut | Keterangan |
|---|---|
| **ID** | UC-42 |
| **Nama** | Lihat Laporan Hutang |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U06 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Laporan hutang ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Laporan Hutang" | — |
| 2 | — | Sistem mengambil hutang berstatus "aktif", dikelompokkan per pelanggan |
| 3 | — | Sistem menampilkan: daftar pelanggan yang masih berutang, total hutang masing-masing, dan grand total hutang aktif |

---

### UC-43: Lihat Laporan Stok Produk

| Atribut | Keterangan |
|---|---|
| **ID** | UC-43 |
| **Nama** | Lihat Laporan Stok Produk |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U03 |
| **Prekondisi** | Pengguna login Owner/Admin |
| **Pascakondisi** | Laporan stok ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna mengklik menu "Laporan Stok" | — |
| 2 | — | Sistem mengambil semua data produk beserta stok dan stok minimumnya |
| 3 | — | Sistem menampilkan tabel: nama produk, kategori, stok saat ini, stok minimum, satuan, status (Normal / Kritis / Habis) |
| 4 | — | Produk dengan stok ≤ stok_minimum ditampilkan dengan tanda peringatan visual |
| 5 | — | Produk dengan stok = 0 ditampilkan dengan label "Habis" |

---

### UC-44: Lihat Riwayat Log Login

| Atribut | Keterangan |
|---|---|
| **ID** | UC-44 |
| **Nama** | Lihat Riwayat Log Login |
| **Aktor** | Owner |
| **Relasi** | `<<extend>>` UC-U02 |
| **Deskripsi** | Owner melihat riwayat aktivitas login seluruh pengguna sebagai fitur audit dan pemantauan akses sistem |
| **Prekondisi** | Pengguna login dengan role Owner |
| **Pascakondisi** | Daftar riwayat log login ditampilkan |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Owner mengklik submenu "Riwayat Login" | — |
| 2 | — | Sistem mengambil data dari tabel `log_login` JOIN `users` |
| 3 | — | Sistem menampilkan tabel: nama pengguna, username, role, waktu login, status (berhasil/gagal) |
| 4 | — | Data diurutkan dari yang paling terbaru |

**Alur Alternatif:**

| Kondisi | Alur |
|---|---|
| Owner memfilter berdasarkan nama atau tanggal | Sistem menambahkan kondisi WHERE dan memperbarui tabel |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Admin atau Kasir mencoba mengakses halaman ini | Sistem menampilkan "Akses ditolak" dan redirect ke dashboard |
| Belum ada riwayat login | Sistem menampilkan "Belum ada riwayat aktivitas login" |

---

### UC-45: Hapus Baris Produk dari Hutang

| Atribut | Keterangan |
|---|---|
| **ID** | UC-45 |
| **Nama** | Hapus Baris Produk dari Hutang |
| **Aktor** | Owner, Admin |
| **Relasi** | `<<extend>>` UC-U06 |
| **Deskripsi** | Menghapus satu baris produk dari detail hutang tanpa menghapus keseluruhan catatan hutang |
| **Prekondisi** | Pengguna login Owner/Admin. Halaman detail hutang sudah terbuka. |
| **Pascakondisi** | Baris produk dihapus dari `detail_hutang`. Stok dikembalikan. `total_hutang` diperbarui. |

**Alur Normal:**

| No | Aktor | Sistem |
|---|---|---|
| 1 | Pengguna menekan "Hapus" pada salah satu baris produk di detail hutang | Sistem menampilkan konfirmasi: "Yakin menghapus produk ini dari hutang? Stok akan dikembalikan." |
| 2 | Pengguna mengklik "Ya, Hapus" | — |
| 3 | — | Sistem mengembalikan stok produk sejumlah `jumlah` di baris tersebut |
| 4 | — | Sistem menghapus baris dari tabel `detail_hutang` |
| 5 | — | Sistem menghitung ulang `total_hutang` di tabel `hutang` |
| 6 | — | Sistem memperbarui tampilan halaman detail hutang |

**Alur Pengecualian:**

| Kondisi Error | Respons Sistem |
|---|---|
| Baris yang dihapus adalah satu-satunya produk dalam hutang | Sistem menampilkan: "Ini produk terakhir dalam hutang ini. Menghapusnya akan menghapus seluruh catatan hutang. Lanjutkan?" — jika ya, alur dilanjutkan ke UC-41 |

---

## Ringkasan Statistik Use Case

| Metrik | Nilai |
|---|---|
| Total Aktor | 3 (Owner, Admin, Kasir) |
| Total UC Utama | 6 (UC-U01 s.d. UC-U06) |
| Total UC Fitur | 45 (UC-01 s.d. UC-45) |
| **Total Keseluruhan** | **51** |
| UC Utama yang dibuat Activity Diagram | 6 |
| Relasi `<<extend>>` UC fitur ke UC Utama | 45 |
| Relasi `<<extend>>` antar UC fitur | 6 |
| Relasi `<<include>>` antar UC fitur | 7 |

---

*Dokumen Use Case Ca'lontong v6 — Sistem Informasi Warung Ananta*  
*Pemrograman Web Dasar (PHP & MySQL) & RPL*
