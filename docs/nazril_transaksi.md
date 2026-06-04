# 📋 Dokumen Perancangan Sistem - Fokus Modul Nazril
# **Ca'lontong** — Sistem Informasi Manajemen Toko Kelontong

> **Pengembang Modul:** Nazril
> **Fokus Utama:** Transaksi Penjualan (Point of Sale)
> **Entitas Database:** `transaksi`, `detail_transaksi`
> **Kebutuhan UI/UX:** WAJIB menggunakan **Bootstrap 5**. Antarmuka harus responsif, rapi diakses via Desktop (Laptop) maupun Mobile (HP).
> **CRUD Utama:** CRUD Transaksi

---

## 1. Analisis Kebutuhan Fungsional (Modul Transaksi)

| Fitur | Deskripsi | CRUD |
|---|---|---|
| **Buat Transaksi** | Pilih produk, masukkan jumlah, hitung total & kembalian | Create |
| **Riwayat Transaksi** | Daftar semua transaksi dengan filter tanggal | Read |
| **Detail Transaksi** | Lihat rincian satu transaksi | Read |
| **Edit Transaksi** | Koreksi transaksi (hanya Admin/Owner) | Update |
| **Batal Transaksi** | Batalkan transaksi dan kembalikan stok | Delete |
| **Kalkulasi Kembalian** | Input uang bayar, hitung kembalian otomatis | — |
| **Stok Otomatis Berkurang**| Stok produk berkurang saat transaksi selesai | — |
| **Laporan Penjualan** | Rekap penjualan harian / per periode | Read |

---

## 2. Detail Tanggung Jawab (Task List Nazril)

* Membuat antarmuka kasir / Point of Sale (`baru.php`) menggunakan **Bootstrap 5**. Struktur grid harus responsif (misal: tabel keranjang bisa di-*scroll* menyamping di layar HP).
* Membuat halaman riwayat transaksi dengan filter tanggal (`index.php`).
* Membuat halaman detail transaksi (`detail.php`).
* Membuat fungsi batal transaksi beserta logika pengembalian stok / *rollback* (`batal.php`).
* Membuat logika pemotongan stok otomatis saat transaksi berhasil.
* Membuat halaman laporan rekap penjualan per periode (`laporan/penjualan.php`).
* Berkontribusi menyiapkan *query* untuk widget Dashboard: "Penjualan Hari Ini (Rp)" dan "Jumlah Transaksi".

---

## 3. Matriks Hak Akses (Khusus Modul Transaksi)

| Fitur | Owner | Admin | Kasir |
|---|---|---|---|
| **Buat Transaksi Baru** | ✅ | ✅ | ✅ |
| **Edit / Batal Transaksi** | ✅ | ✅ | ❌ |
| **Lihat Riwayat Transaksi** | ✅ | ✅ | 👁️ (Hanya milik sendiri) |
| **Lihat Laporan Penjualan** | ✅ | ✅ | ❌ |

---

## 4. Rancangan Database (Area Nazril)

### Tabel `transaksi`
```sql
CREATE TABLE transaksi (
    id_transaksi    INT PRIMARY KEY AUTO_INCREMENT,
    id_user         INT NOT NULL,            -- ID kasir yang melayani (dari session)
    kode_transaksi  VARCHAR(50) UNIQUE,      -- Format: TRX-YYYYMMDD-XXX
    total_harga     DECIMAL(10,2) NOT NULL,
    uang_bayar      DECIMAL(10,2),
    kembalian       DECIMAL(10,2),
    status          ENUM('selesai','batal') DEFAULT 'selesai',
    keterangan      TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);
Tabel detail_transaksi
SQL
CREATE TABLE detail_transaksi (
    id_detail       INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi    INT NOT NULL,
    id_produk       INT NOT NULL,
    jumlah          INT NOT NULL,
    harga_satuan    DECIMAL(10,2) NOT NULL,  -- Snapshot harga saat transaksi terjadi
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
);
(Catatan: Modul ini membutuhkan operasi JOIN/SELECT ke tabel users milik Rafli dan tabel produk milik Defry).

5. Peta Struktur Folder & File Utama
Area kerja khusus untuk modul transaksi berada di dalam direktori pages/transaksi/ dan pages/laporan/. Perhatikan hirarki file berikut agar penulisan path include (../../) selalu presisi:

Plaintext
calontong/
├── config.php                    ← Berisi variabel koneksi MySQL ($conn)
├── includes/
│   └── menu.php                  ← File navigasi sidebar/navbar
└── pages/
    ├── transaksi/
    │   ├── index.php             ← Menampilkan tabel riwayat transaksi (Gunakan DataTables/Bootstrap Table)
    │   ├── baru.php              ← Form mesin kasir (Pilih barang & bayar)
    │   ├── detail.php            ← Melihat rincian barang dari 1 struk
    │   ├── edit.php              ← Form edit data transaksi
    │   └── batal.php             ← Proses backend membatalkan transaksi
    └── laporan/
        └── penjualan.php         ← Halaman filter laporan pendapatan
6. Alur Logika Sistem (Wajib Dipatuhi AI Backend)
Saat menulis logika proses bisnis menggunakan PHP, pedoman Activity Flow dan Sequence Flow berikut bersifat mutlak:

A. Alur Memasukkan Barang ke Keranjang:

Pengguna (Kasir) memilih produk dan menginput Quantity (jumlah).

Sistem mengecek ketersediaan stok fisik secara aktual di tabel produk.

Jika stok TIDAK CUKUP: Gagalkan proses dan berikan notifikasi error.

Jika stok CUKUP: Masukkan data item (id_produk, nama, harga, qty, subtotal) ke dalam memori sementara ($_SESSION['cart']), lalu tampilkan di tabel antarmuka.

B. Alur Pembayaran & Checkout:

Sistem menghitung Grand Total dari semua item di keranjang.

Kasir memasukkan nominal uang bayar.

Sistem memvalidasi: Apakah uang bayar >= Grand Total?

Jika uang KURANG: Gagalkan proses transaksi dan munculkan alert error.

Jika uang CUKUP, jalankan rangkaian instruksi MySQL (Database Transaction) berikut secara berurutan:

INSERT INTO transaksi (Menyimpan data nota/header).

Looping keranjang: INSERT INTO detail_transaksi (Memindahkan rincian barang).

Looping keranjang: UPDATE produk (Mengurangi stok barang terkait).

Kosongkan keranjang belanja dengan perintah unset($_SESSION['cart']).

Tampilkan pesan sukses, struk tercetak, atau arahkan kembali ke form kasir yang bersih.