# 📐 Perancangan ERD Logis
# **Ca'lontong** — Sistem Informasi Manajemen Warung Ananta

> **Dokumen:** Entity Relationship Diagram (ERD) — Level Logis  
> **Versi:** 1.0  
> **Mata Kuliah:** Pemrograman Web Dasar (PHP & MySQL)  
> **Sistem:** Ca'lontong — Sistem Informasi Warung Kelontong

---

## DAFTAR ISI

1. [Penjelasan ERD Logis](#1-penjelasan-erd-logis)
2. [Daftar Entitas](#2-daftar-entitas)
3. [Daftar Relasi & Kardinalitas](#3-daftar-relasi--kardinalitas)
4. [Atribut Setiap Entitas](#4-atribut-setiap-entitas)
5. [Penjelasan Detail Setiap Relasi](#5-penjelasan-detail-setiap-relasi)
6. [Panduan Menggambar di Draw.io / Lucidchart](#6-panduan-menggambar-di-drawio--lucidchart)

---

## 1. Penjelasan ERD Logis

ERD Logis menggambarkan struktur data sistem pada level **antara konseptual dan fisik**. Pada level ini sudah tercantum:

- ✅ Seluruh **entitas** (tabel) yang ada dalam sistem
- ✅ Seluruh **atribut** setiap entitas beserta **tipe datanya**
- ✅ **Primary Key (PK)** setiap entitas
- ✅ **Foreign Key (FK)** sebagai penghubung antar entitas
- ✅ **Kardinalitas relasi** (One-to-Many, Many-to-Many)
- ❌ Belum mencantumkan detail implementasi seperti `AUTO_INCREMENT`, `DEFAULT`, `ON DELETE`, dsb. (itu urusan ERD Fisik / Desain Database)

---

## 2. Daftar Entitas

Sistem Ca'lontong memiliki **11 entitas** yang terbagi dalam 5 modul:

| No | Nama Entitas | Deskripsi Singkat | Modul |
|---|---|---|---|
| 1 | **users** | Akun pengguna sistem (Owner, Admin, Kasir) | Modul 1 |
| 2 | **kategori** | Kategori/jenis produk | Modul 2 |
| 3 | **produk** | Data barang dagangan warung | Modul 2 |
| 4 | **transaksi** | Header catatan penjualan | Modul 3 |
| 5 | **detail_transaksi** | Rincian produk dalam satu penjualan | Modul 3 |
| 6 | **supplier** | Data agen/pemasok barang | Modul 4 |
| 7 | **pembelian** | Header catatan restock dari supplier | Modul 4 |
| 8 | **detail_pembelian** | Rincian produk dalam satu restock | Modul 4 |
| 9 | **pelanggan** | Data pelanggan yang pernah berutang | Modul 5 |
| 10 | **hutang** | Header catatan hutang dan pembayaran | Modul 5 |
| 11 | **detail_hutang** | Rincian produk dalam satu catatan hutang | Modul 5 |

> **Catatan:** Tabel `log_login` (UC-44) tidak masuk ke ERD Logis karena bersifat tabel audit/teknis, bukan entitas bisnis utama. Cukup dicantumkan di Desain Database (ERD Fisik).

---

## 3. Daftar Relasi & Kardinalitas

Berikut adalah seluruh relasi antar entitas beserta jenis kardinalitasnya:

| No | Entitas A | Kardinalitas | Entitas B | Nama Relasi | Keterangan |
|---|---|---|---|---|---|
| R-01 | **kategori** | 1 : N | **produk** | "memiliki" | Satu kategori dapat memiliki banyak produk. Satu produk hanya masuk ke satu kategori. |
| R-02 | **users** | 1 : N | **transaksi** | "membuat" | Satu user (kasir) dapat membuat banyak transaksi penjualan. Satu transaksi hanya dibuat oleh satu user. |
| R-03 | **transaksi** | 1 : N | **detail_transaksi** | "terdiri dari" | Satu transaksi terdiri dari banyak baris detail produk. Satu baris detail hanya milik satu transaksi. |
| R-04 | **produk** | 1 : N | **detail_transaksi** | "tercatat dalam" | Satu produk dapat tercatat di banyak baris detail transaksi. Satu baris detail hanya merujuk ke satu produk. |
| R-05 | **supplier** | 1 : N | **pembelian** | "memasok" | Satu supplier dapat memiliki banyak catatan pembelian/restock. Satu catatan pembelian hanya dari satu supplier. |
| R-06 | **users** | 1 : N | **pembelian** | "mencatat" | Satu user (admin) dapat mencatat banyak pembelian. Satu pembelian hanya dicatat oleh satu user. |
| R-07 | **pembelian** | 1 : N | **detail_pembelian** | "terdiri dari" | Satu pembelian terdiri dari banyak baris detail produk. Satu baris detail hanya milik satu pembelian. |
| R-08 | **produk** | 1 : N | **detail_pembelian** | "tercatat dalam" | Satu produk dapat tercatat di banyak baris detail pembelian. Satu baris detail hanya merujuk ke satu produk. |
| R-09 | **pelanggan** | 1 : N | **hutang** | "memiliki" | Satu pelanggan dapat memiliki banyak catatan hutang. Satu catatan hutang hanya milik satu pelanggan. |
| R-10 | **users** | 1 : N | **hutang** | "mencatat" | Satu user dapat mencatat banyak hutang pelanggan. Satu catatan hutang hanya dicatat oleh satu user. |
| R-11 | **hutang** | 1 : N | **detail_hutang** | "terdiri dari" | Satu hutang terdiri dari banyak baris detail produk yang diambil. Satu baris detail hanya milik satu hutang. |
| R-12 | **produk** | 1 : N | **detail_hutang** | "tercatat dalam" | Satu produk dapat tercatat di banyak baris detail hutang. Satu baris detail hanya merujuk ke satu produk. |

### Ringkasan Pola Relasi

Seluruh relasi dalam sistem Ca'lontong berpola **One-to-Many (1:N)**. Tidak ada relasi Many-to-Many langsung karena relasi yang secara logika bersifat M:N sudah diselesaikan menggunakan **tabel jembatan**:

```
produk  ←──── detail_transaksi ────→  transaksi
  (1)              (N jembatan)           (1)

produk  ←──── detail_pembelian ────→  pembelian
  (1)              (N jembatan)           (1)

produk  ←────  detail_hutang   ────→  hutang
  (1)              (N jembatan)           (1)
```

---

## 4. Atribut Setiap Entitas

Pada ERD Logis, setiap atribut dicantumkan beserta tipe datanya, status PK/FK, dan apakah wajib diisi (NOT NULL).

> **Notasi:**
> - 🔑 = Primary Key (PK)
> - 🔗 = Foreign Key (FK)
> - **bold** = atribut wajib (NOT NULL)
> - *italic* = atribut opsional (boleh NULL)

---

### Entitas: `users`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_user** | INT | Primary Key |
| **nama_lengkap** | VARCHAR(100) | Nama lengkap pengguna |
| **username** | VARCHAR(50) | Username unik untuk login |
| **password** | VARCHAR(255) | Password (disimpan dalam bentuk hash) |
| **role** | ENUM | Nilai: `owner`, `admin`, `kasir` |
| *created_at* | TIMESTAMP | Waktu akun dibuat |
| *updated_at* | TIMESTAMP | Waktu akun terakhir diperbarui |

---

### Entitas: `kategori`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_kategori** | INT | Primary Key |
| **nama_kategori** | VARCHAR(100) | Nama kategori produk |
| *deskripsi* | TEXT | Keterangan tambahan kategori |

---

### Entitas: `produk`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_produk** | INT | Primary Key |
| 🔗 **id_kategori** | INT | FK → `kategori(id_kategori)` |
| *kode_produk* | VARCHAR(50) | Kode unik produk (opsional) |
| **nama_produk** | VARCHAR(150) | Nama produk |
| **harga_beli** | DECIMAL(10,2) | Harga beli dari supplier |
| **harga_jual** | DECIMAL(10,2) | Harga jual ke pelanggan |
| **stok** | INT | Jumlah stok saat ini |
| *stok_minimum* | INT | Batas minimum stok (untuk peringatan) |
| *satuan* | VARCHAR(30) | Satuan produk: pcs, kg, liter, bungkus, dll. |
| *created_at* | TIMESTAMP | Waktu produk ditambahkan |
| *updated_at* | TIMESTAMP | Waktu data produk terakhir diperbarui |

---

### Entitas: `transaksi`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_transaksi** | INT | Primary Key |
| 🔗 **id_user** | INT | FK → `users(id_user)` — kasir yang melayani |
| *kode_transaksi* | VARCHAR(50) | Kode unik transaksi, contoh: `TRX-20240601-001` |
| **total_harga** | DECIMAL(10,2) | Total harga seluruh produk dalam transaksi |
| *uang_bayar* | DECIMAL(10,2) | Jumlah uang yang dibayarkan pelanggan |
| *kembalian* | DECIMAL(10,2) | Jumlah kembalian (uang_bayar − total_harga) |
| **status** | ENUM | Nilai: `selesai`, `batal` |
| *keterangan* | TEXT | Catatan tambahan transaksi |
| *created_at* | TIMESTAMP | Waktu transaksi dibuat |

---

### Entitas: `detail_transaksi`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_detail** | INT | Primary Key |
| 🔗 **id_transaksi** | INT | FK → `transaksi(id_transaksi)` |
| 🔗 **id_produk** | INT | FK → `produk(id_produk)` |
| **jumlah** | INT | Jumlah produk yang dibeli |
| **harga_satuan** | DECIMAL(10,2) | Snapshot harga jual produk saat transaksi terjadi |
| **subtotal** | DECIMAL(10,2) | jumlah × harga_satuan |

---

### Entitas: `supplier`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_supplier** | INT | Primary Key |
| **nama_supplier** | VARCHAR(150) | Nama agen/supplier |
| *nama_kontak* | VARCHAR(100) | Nama orang yang bisa dihubungi |
| *no_telepon* | VARCHAR(20) | Nomor telepon/WA supplier |
| *alamat* | TEXT | Alamat supplier |
| *created_at* | TIMESTAMP | Waktu data supplier ditambahkan |

---

### Entitas: `pembelian`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_pembelian** | INT | Primary Key |
| 🔗 **id_supplier** | INT | FK → `supplier(id_supplier)` |
| 🔗 **id_user** | INT | FK → `users(id_user)` — admin yang mencatat |
| *kode_pembelian* | VARCHAR(50) | Kode unik pembelian, contoh: `BLI-20240601-001` |
| *total_harga* | DECIMAL(10,2) | Total harga seluruh produk dalam restock |
| *keterangan* | TEXT | Catatan tambahan restock |
| *created_at* | TIMESTAMP | Waktu restock dicatat |

---

### Entitas: `detail_pembelian`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_detail_beli** | INT | Primary Key |
| 🔗 **id_pembelian** | INT | FK → `pembelian(id_pembelian)` |
| 🔗 **id_produk** | INT | FK → `produk(id_produk)` |
| **jumlah** | INT | Jumlah produk yang dibeli/direstock |
| **harga_beli** | DECIMAL(10,2) | Harga beli per satuan dari supplier |
| **subtotal** | DECIMAL(10,2) | jumlah × harga_beli |

---

### Entitas: `pelanggan`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_pelanggan** | INT | Primary Key |
| **nama_pelanggan** | VARCHAR(150) | Nama pelanggan yang berutang |
| *no_telepon* | VARCHAR(20) | Nomor telepon pelanggan |
| *alamat* | TEXT | Alamat/keterangan lokasi, contoh: "Tetangga sebelah kanan" |
| *keterangan* | TEXT | Catatan tambahan tentang pelanggan |
| *created_at* | TIMESTAMP | Waktu data pelanggan ditambahkan |

---

### Entitas: `hutang`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_hutang** | INT | Primary Key |
| 🔗 **id_pelanggan** | INT | FK → `pelanggan(id_pelanggan)` |
| 🔗 **id_user** | INT | FK → `users(id_user)` — user yang mencatat hutang |
| *kode_hutang* | VARCHAR(50) | Kode unik hutang, contoh: `HTG-20240601-001` |
| **total_hutang** | DECIMAL(10,2) | Total nilai hutang (dihitung dari jumlah semua subtotal di `detail_hutang`) |
| *jumlah_terbayar* | DECIMAL(10,2) | Akumulasi pembayaran yang sudah diterima |
| *sisa_hutang* | DECIMAL(10,2) | total_hutang − jumlah_terbayar |
| **status** | ENUM | Nilai: `aktif`, `lunas` |
| **tanggal_hutang** | DATE | Tanggal hutang terjadi |
| *tanggal_lunas* | DATE | Tanggal hutang dinyatakan lunas (diisi otomatis) |
| *keterangan* | TEXT | Catatan tambahan (opsional) |
| *created_at* | TIMESTAMP | Waktu catatan hutang dibuat |
| *updated_at* | TIMESTAMP | Waktu catatan hutang terakhir diperbarui |

---

### Entitas: `detail_hutang`

| Atribut | Tipe Data | Keterangan |
|---|---|---|
| 🔑 **id_detail_hutang** | INT | Primary Key |
| 🔗 **id_hutang** | INT | FK → `hutang(id_hutang)` |
| 🔗 **id_produk** | INT | FK → `produk(id_produk)` |
| **jumlah** | INT | Jumlah produk yang diambil/diutang |
| **harga_satuan** | DECIMAL(10,2) | Snapshot harga jual produk saat hutang dicatat |
| **subtotal** | DECIMAL(10,2) | jumlah × harga_satuan |

---

## 5. Penjelasan Detail Setiap Relasi

Bagian ini menjelaskan setiap relasi secara naratif — berguna untuk menuliskan deskripsi relasi di laporan tertulis UAS.

---

### R-01: kategori → produk (1:N)
**"Satu kategori memiliki banyak produk"**

Setiap produk di warung Ananta dikelompokkan ke dalam satu kategori (misalnya Snack, Minuman, Sembako). Satu kategori dapat menampung banyak produk, namun setiap produk hanya boleh masuk ke dalam satu kategori. Relasi ini diwujudkan dengan atribut `id_kategori` pada tabel `produk` yang merujuk ke `id_kategori` pada tabel `kategori`.

**Aturan integritas:** Produk tidak dapat ditambahkan jika kategori yang dipilih belum ada. Kategori tidak dapat dihapus selama masih ada produk yang menggunakannya.

---

### R-02: users → transaksi (1:N)
**"Satu user membuat banyak transaksi penjualan"**

Setiap transaksi penjualan yang tercatat di sistem selalu dikaitkan dengan user yang sedang login saat itu (kasir yang melayani). Satu user dapat membuat banyak transaksi sepanjang waktu, namun satu transaksi hanya dicatat oleh satu user. Relasi ini memungkinkan sistem untuk menyaring riwayat transaksi berdasarkan kasir (fitur "transaksi milik sendiri" untuk role Kasir).

**Aturan integritas:** Transaksi tidak dapat dibuat tanpa user yang login. Jika akun user dihapus, data transaksi tetap dipertahankan untuk keperluan riwayat.

---

### R-03: transaksi → detail_transaksi (1:N)
**"Satu transaksi terdiri dari banyak baris detail produk"**

Dalam satu kali transaksi penjualan, pelanggan bisa membeli lebih dari satu jenis produk. Setiap produk yang dibeli dicatat sebagai satu baris di tabel `detail_transaksi`. Tabel ini berfungsi sebagai **tabel jembatan** yang memecah relasi M:N antara `transaksi` dan `produk` menjadi dua relasi 1:N.

**Aturan integritas:** Baris detail tidak dapat ada tanpa transaksi induknya. Jika transaksi dihapus, seluruh baris detailnya ikut dihapus (CASCADE DELETE).

---

### R-04: produk → detail_transaksi (1:N)
**"Satu produk dapat tercatat di banyak baris detail transaksi"**

Satu produk (misalnya "Indomie Goreng") bisa terjual berkali-kali dalam transaksi yang berbeda. Setiap penjualan produk tersebut menghasilkan satu baris di `detail_transaksi`. Atribut `harga_satuan` di tabel ini menyimpan **snapshot harga** saat transaksi terjadi, sehingga perubahan harga produk di kemudian hari tidak mengubah data transaksi yang sudah tercatat.

**Aturan integritas:** Produk yang masih memiliki riwayat transaksi sebaiknya tidak dihapus, atau diberi penanganan khusus untuk menjaga integritas data historis.

---

### R-05: supplier → pembelian (1:N)
**"Satu supplier memasok banyak catatan pembelian"**

Pemilik warung Ananta menghubungi agen/supplier saat stok menipis. Setiap sesi restock dicatat sebagai satu entri `pembelian`. Satu supplier dapat menjadi sumber banyak restock berbeda di waktu yang berbeda, namun setiap catatan restock hanya berasal dari satu supplier.

**Aturan integritas:** Catatan pembelian tidak dapat dibuat tanpa memilih supplier. Supplier yang masih memiliki riwayat pembelian tidak dapat dihapus.

---

### R-06: users → pembelian (1:N)
**"Satu user mencatat banyak pembelian/restock"**

Mirip dengan relasi users → transaksi, setiap catatan restock dikaitkan dengan user (admin/owner) yang sedang login saat mencatatnya. Ini berguna untuk mengetahui siapa yang menginput catatan restock tertentu.

**Aturan integritas:** Catatan pembelian tidak dapat dibuat tanpa user yang login.

---

### R-07: pembelian → detail_pembelian (1:N)
**"Satu pembelian terdiri dari banyak baris detail produk"**

Dalam satu sesi restock, bisa dibeli lebih dari satu jenis produk sekaligus. Setiap produk yang dibeli dicatat sebagai satu baris di `detail_pembelian`. Fungsinya sama dengan `detail_transaksi` — menjadi **tabel jembatan** antara `pembelian` dan `produk`.

**Aturan integritas:** Baris detail tidak dapat ada tanpa pembelian induknya. Jika pembelian dihapus, seluruh baris detailnya ikut dihapus dan stok produk terkait dikembalikan (rollback).

---

### R-08: produk → detail_pembelian (1:N)
**"Satu produk dapat tercatat di banyak baris detail pembelian"**

Satu produk (misalnya "Aqua 600ml") bisa dibeli/direstock berkali-kali dari supplier yang sama atau berbeda. Setiap restock produk tersebut menghasilkan satu baris di `detail_pembelian`. Atribut `harga_beli` di sini mencatat harga beli pada saat restock tersebut — yang bisa berbeda dari restock sebelumnya jika harga dari supplier berubah.

**Aturan integritas:** Produk yang masih memiliki riwayat pembelian sebaiknya tidak dihapus.

---

### R-09: pelanggan → hutang (1:N)
**"Satu pelanggan dapat memiliki banyak catatan hutang"**

Seorang pelanggan (misalnya tetangga) bisa berutang lebih dari satu kali di waktu yang berbeda. Setiap kejadian utang dicatat sebagai satu entri terpisah di tabel `hutang`, sehingga riwayat hutang pelanggan tersebut dapat terlacak secara lengkap. Satu catatan hutang hanya milik satu pelanggan.

**Aturan integritas:** Catatan hutang tidak dapat dibuat tanpa memilih pelanggan. Pelanggan yang masih memiliki hutang berstatus `aktif` tidak dapat dihapus.

---

### R-10: users → hutang (1:N)
**"Satu user mencatat banyak hutang pelanggan"**

Setiap kali hutang pelanggan dicatat ke sistem, dicatat pula siapa user (kasir/admin/owner) yang menginputnya. Satu user dapat mencatat banyak hutang dari berbagai pelanggan yang berbeda. Ini berguna untuk akuntabilitas pencatatan.

**Aturan integritas:** Catatan hutang tidak dapat dibuat tanpa user yang login.

---

### R-11: hutang → detail_hutang (1:N)
**"Satu hutang terdiri dari banyak baris detail produk"**

Dalam satu kejadian hutang, pelanggan bisa mengambil lebih dari satu jenis produk sekaligus. Setiap produk yang diambil dicatat sebagai satu baris di tabel `detail_hutang`. Tabel ini berfungsi sebagai **tabel jembatan** yang memecah relasi M:N antara `hutang` dan `produk` menjadi dua relasi 1:N — polanya identik dengan `detail_transaksi` dan `detail_pembelian`. Dengan adanya tabel ini, sistem dapat menampilkan transparansi hutang kepada pelanggan: hutang senilai Rp X terdiri dari produk A (sekian), produk B (sekian), dan seterusnya.

**Aturan integritas:** Baris detail tidak dapat ada tanpa hutang induknya. Jika hutang dihapus, seluruh baris detailnya ikut dihapus dan stok produk terkait dikembalikan (rollback).

---

### R-12: produk → detail_hutang (1:N)
**"Satu produk dapat tercatat di banyak baris detail hutang"**

Satu produk (misalnya "Mie Instan") bisa muncul di banyak catatan hutang dari pelanggan yang berbeda atau dari hutang yang berbeda pada pelanggan yang sama. Atribut `harga_satuan` di tabel ini menyimpan **snapshot harga jual** saat hutang terjadi — prinsip yang sama seperti di `detail_transaksi` — agar perubahan harga di masa depan tidak mempengaruhi nilai hutang yang sudah tercatat.

**Aturan integritas:** Produk yang masih memiliki riwayat hutang aktif sebaiknya tidak dihapus untuk menjaga integritas data.

---

## 6. Panduan Menggambar di Draw.io / Lucidchart

Bagian ini adalah panduan praktis untuk menggambar ERD Logis Ca'lontong berdasarkan deskripsi di atas.

### Notasi yang Digunakan

Gunakan notasi **Crow's Foot (Notasi Kaki Gagak)** — ini notasi yang paling umum digunakan di dunia akademik dan industri untuk ERD Logis.

| Simbol Crow's Foot | Artinya |
|---|---|
| `──∣` | Tepat satu (exactly one) — sisi "satu" pada relasi 1:N |
| `──<` atau `──∣<` | Banyak (many) — sisi "banyak" pada relasi 1:N |
| `──○∣` | Nol atau satu (zero or one) |
| `──○<` | Nol atau banyak (zero or many) |

Untuk sistem Ca'lontong, semua relasi adalah **wajib di kedua sisi**, sehingga gunakan `──∣` di sisi "satu" dan `──∣<` di sisi "banyak".

---

### Tata Letak yang Disarankan

```
┌─────────────┐
│   kategori  │
└──────┬──────┘
       │ 1
       │ N
┌──────┴──────┐     ┌──────────────────┐     ┌─────────────┐
│    produk   ├──N──┤ detail_transaksi ├──N──┤  transaksi  │
│             │     └──────────────────┘     └──────┬──────┘
│             │                                     │ N
│             │     ┌──────────────────┐       ┌────┴─────┐
│             ├──N──┤ detail_pembelian ├──N────┤ pembelian│
│             │     └──────────────────┘       └────┬─────┘
│             │                                     │ N
│             │     ┌──────────────────┐       ┌────┴─────┐      ┌───────────┐
│             ├──N──┤  detail_hutang   ├──N────┤  hutang  ├──N───┤ pelanggan │
└─────────────┘     └──────────────────┘       └──────────┘      └───────────┘
                                                     │ N
                                               ┌─────┴────┐
                                               │  users   │
                                               │  (juga   │
                                               │  →transak│
                                               │  →pembeli│
                                               └──────────┘
```

> **Tips:** Karena `users` terhubung ke 3 entitas (`transaksi`, `pembelian`, `hutang`), posisikan di bagian bawah tengah diagram agar garis relasinya tidak terlalu panjang dan tidak saling bersilangan.

---

### Langkah-langkah di Draw.io

1. Buka [draw.io](https://draw.io) → pilih template **Blank**
2. Aktifkan shape ERD: klik **"+ More Shapes"** → centang **"Entity Relation"** → OK
3. **Buat setiap entitas** sebagai kotak dengan:
   - Baris pertama (header gelap): nama tabel (contoh: `produk`)
   - Baris-baris berikutnya: atribut dengan format `🔑 id_produk : INT` atau `nama_produk : VARCHAR(150)`
   - Tandai PK dengan ikon kunci 🔑 atau tulisan `PK`
   - Tandai FK dengan ikon atau tulisan `FK`
4. **Hubungkan entitas** menggunakan garis relasi Crow's Foot:
   - Di toolbar kanan, pilih koneksi dengan ujung `|` di sisi "satu" dan `|<` di sisi "banyak"
5. **Beri label relasi** di tengah garis: nama relasi seperti "memiliki", "membuat", "terdiri dari"
6. Atur posisi sesuai tata letak yang disarankan di atas

---

### Daftar Koneksi yang Harus Digambar

| No | Dari | Ke | Sisi "Satu" | Sisi "Banyak" | Label Garis |
|---|---|---|---|---|---|
| 1 | `kategori` | `produk` | `id_kategori (PK)` | `id_kategori (FK)` | memiliki |
| 2 | `users` | `transaksi` | `id_user (PK)` | `id_user (FK)` | membuat |
| 3 | `transaksi` | `detail_transaksi` | `id_transaksi (PK)` | `id_transaksi (FK)` | terdiri dari |
| 4 | `produk` | `detail_transaksi` | `id_produk (PK)` | `id_produk (FK)` | tercatat dalam |
| 5 | `supplier` | `pembelian` | `id_supplier (PK)` | `id_supplier (FK)` | memasok |
| 6 | `users` | `pembelian` | `id_user (PK)` | `id_user (FK)` | mencatat |
| 7 | `pembelian` | `detail_pembelian` | `id_pembelian (PK)` | `id_pembelian (FK)` | terdiri dari |
| 8 | `produk` | `detail_pembelian` | `id_produk (PK)` | `id_produk (FK)` | tercatat dalam |
| 9 | `pelanggan` | `hutang` | `id_pelanggan (PK)` | `id_pelanggan (FK)` | memiliki |
| 10 | `users` | `hutang` | `id_user (PK)` | `id_user (FK)` | mencatat |
| 11 | `hutang` | `detail_hutang` | `id_hutang (PK)` | `id_hutang (FK)` | terdiri dari |
| 12 | `produk` | `detail_hutang` | `id_produk (PK)` | `id_produk (FK)` | tercatat dalam |

> **Total garis relasi: 12 garis** — sesuai dengan jumlah relasi pada bagian 3.

---

### Perhatian Khusus: Entitas `produk` dan `users` Punya Banyak Relasi

**Entitas `produk`** adalah entitas yang paling banyak terhubung dari sisi "satu" — menjadi sumber relasi ke 3 tabel jembatan sekaligus: `detail_transaksi`, `detail_pembelian`, dan `detail_hutang`. Posisikan `produk` di sisi kiri diagram sebagai "pusat" agar ketiga garis relasinya bisa ditarik ke kanan secara rapi.

**Entitas `users`** terhubung ke 3 entitas berbeda: `transaksi`, `pembelian`, dan `hutang`. Posisikan `users` di bagian bawah tengah diagram agar garis relasinya tidak saling bersilangan terlalu banyak.

---

## Ringkasan Statistik ERD

| Metrik | Nilai |
|---|---|
| Total Entitas | 11 |
| Total Relasi | 12 |
| Jenis Relasi | Semua One-to-Many (1:N) |
| Tabel Jembatan (M:N resolver) | 3 (`detail_transaksi`, `detail_pembelian`, `detail_hutang`) |
| Entitas dengan relasi terbanyak (sisi "satu") | `produk` → 4 relasi ke: `kategori`, `detail_transaksi`, `detail_pembelian`, `detail_hutang` |
| Entitas dengan relasi terbanyak (sisi FK) | `users` → 3 relasi ke: `transaksi`, `pembelian`, `hutang` |
| Total garis yang digambar di diagram | 12 garis |

---

*Dokumen ERD Logis Ca'lontong v1.1 — Sistem Informasi Warung Ananta*  
*Pemrograman Web Dasar — PHP & MySQL*
