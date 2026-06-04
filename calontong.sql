-- ============================================================
-- DATABASE: Ca'lontong
-- Sistem Informasi Manajemen Warung Ananta
-- Mata Kuliah: Pemrograman Web Dasar (PHP & MySQL)
-- ============================================================
-- Versi      : 1.0
-- Keterangan : File ini berisi struktur database lengkap
--              (CREATE TABLE) tanpa data dummy.
--              Import file ini di phpMyAdmin / MySQL CLI
--              sebelum menjalankan aplikasi.
-- ============================================================

-- Hapus database lama jika ada, lalu buat ulang
DROP DATABASE IF EXISTS calontong;
CREATE DATABASE calontong
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE calontong;

-- ============================================================
-- MODUL 1 — MANAJEMEN PENGGUNA
-- Tabel  : users, log_login
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: users
-- Menyimpan akun pengguna sistem (Owner, Admin, Kasir)
-- ------------------------------------------------------------
CREATE TABLE users (
    id_user      INT          NOT NULL AUTO_INCREMENT,
    nama_lengkap VARCHAR(100) NOT NULL,
    username     VARCHAR(50)  NOT NULL,
    password     VARCHAR(255) NOT NULL,           -- hasil password_hash()
    role         ENUM('owner', 'admin', 'kasir') NOT NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_user),
    UNIQUE KEY uk_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: log_login
-- Mencatat setiap percobaan login (berhasil maupun gagal)
-- Digunakan oleh UC-44 (Lihat Riwayat Log Login)
-- ------------------------------------------------------------
CREATE TABLE log_login (
    id_log        INT          NOT NULL AUTO_INCREMENT,
    id_user       INT                   DEFAULT NULL, -- NULL jika login gagal
    username_input VARCHAR(50) NOT NULL,              -- username yang diketik
    waktu_login   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status        ENUM('berhasil', 'gagal') NOT NULL,
    keterangan    VARCHAR(100)          DEFAULT NULL, -- misal: "Password salah"
    PRIMARY KEY (id_log),
    CONSTRAINT fk_log_user
        FOREIGN KEY (id_user)
        REFERENCES users (id_user)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODUL 2 — MANAJEMEN PRODUK & KATEGORI
-- Tabel  : kategori, produk
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: kategori
-- Menyimpan kategori/jenis produk warung
-- Contoh data: Snack, Minuman, Sembako, Bumbu Dapur, dst.
-- ------------------------------------------------------------
CREATE TABLE kategori (
    id_kategori   INT          NOT NULL AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi     TEXT                  DEFAULT NULL,
    PRIMARY KEY (id_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: produk
-- Menyimpan data barang dagangan warung Ananta
-- Stok dikelola otomatis saat transaksi/restock/hutang terjadi
-- ------------------------------------------------------------
CREATE TABLE produk (
    id_produk    INT            NOT NULL AUTO_INCREMENT,
    id_kategori  INT            NOT NULL,
    kode_produk  VARCHAR(50)             DEFAULT NULL,
    nama_produk  VARCHAR(150)   NOT NULL,
    harga_beli   DECIMAL(10, 2) NOT NULL,
    harga_jual   DECIMAL(10, 2) NOT NULL,
    stok         INT            NOT NULL DEFAULT 0,
    stok_minimum INT                     DEFAULT 5,  -- batas peringatan stok kritis
    satuan       VARCHAR(30)             DEFAULT NULL, -- pcs, kg, liter, bungkus, dll.
    created_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_produk),
    UNIQUE KEY uk_kode_produk (kode_produk),
    CONSTRAINT fk_produk_kategori
        FOREIGN KEY (id_kategori)
        REFERENCES kategori (id_kategori)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODUL 3 — TRANSAKSI PENJUALAN
-- Tabel  : transaksi, detail_transaksi
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: transaksi
-- Header setiap transaksi penjualan di kasir
-- ------------------------------------------------------------
CREATE TABLE transaksi (
    id_transaksi   INT            NOT NULL AUTO_INCREMENT,
    id_user        INT            NOT NULL,           -- kasir yang melayani
    kode_transaksi VARCHAR(50)             DEFAULT NULL, -- contoh: TRX-20240601-001
    total_harga    DECIMAL(10, 2) NOT NULL,
    uang_bayar     DECIMAL(10, 2)          DEFAULT NULL,
    kembalian      DECIMAL(10, 2)          DEFAULT NULL,
    status         ENUM('selesai', 'batal') NOT NULL DEFAULT 'selesai',
    keterangan     TEXT                    DEFAULT NULL,
    created_at     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_transaksi),
    UNIQUE KEY uk_kode_transaksi (kode_transaksi),
    CONSTRAINT fk_transaksi_user
        FOREIGN KEY (id_user)
        REFERENCES users (id_user)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: detail_transaksi
-- Rincian produk dalam satu transaksi penjualan
-- harga_satuan = snapshot harga jual saat transaksi terjadi
-- (agar perubahan harga di masa depan tidak mengubah histori)
-- ------------------------------------------------------------
CREATE TABLE detail_transaksi (
    id_detail    INT            NOT NULL AUTO_INCREMENT,
    id_transaksi INT            NOT NULL,
    id_produk    INT            NOT NULL,
    jumlah       INT            NOT NULL,
    harga_satuan DECIMAL(10, 2) NOT NULL, -- snapshot harga jual
    subtotal     DECIMAL(10, 2) NOT NULL, -- jumlah × harga_satuan
    PRIMARY KEY (id_detail),
    CONSTRAINT fk_detail_transaksi
        FOREIGN KEY (id_transaksi)
        REFERENCES transaksi (id_transaksi)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_detail_transaksi_produk
        FOREIGN KEY (id_produk)
        REFERENCES produk (id_produk)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODUL 4 — SUPPLIER & PEMBELIAN (RESTOCK)
-- Tabel  : supplier, pembelian, detail_pembelian
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: supplier
-- Menyimpan data agen/pemasok barang warung Ananta
-- ------------------------------------------------------------
CREATE TABLE supplier (
    id_supplier   INT          NOT NULL AUTO_INCREMENT,
    nama_supplier VARCHAR(150) NOT NULL,
    nama_kontak   VARCHAR(100)          DEFAULT NULL,
    no_telepon    VARCHAR(20)           DEFAULT NULL,
    alamat        TEXT                  DEFAULT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_supplier)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: pembelian
-- Header setiap catatan restock dari supplier
-- Menggantikan struk fisik yang rawan hilang/rusak
-- ------------------------------------------------------------
CREATE TABLE pembelian (
    id_pembelian   INT            NOT NULL AUTO_INCREMENT,
    id_supplier    INT            NOT NULL,
    id_user        INT            NOT NULL,           -- admin yang mencatat
    kode_pembelian VARCHAR(50)             DEFAULT NULL, -- contoh: BLI-20240601-001
    total_harga    DECIMAL(10, 2)          DEFAULT NULL,
    keterangan     TEXT                    DEFAULT NULL,
    created_at     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_pembelian),
    UNIQUE KEY uk_kode_pembelian (kode_pembelian),
    CONSTRAINT fk_pembelian_supplier
        FOREIGN KEY (id_supplier)
        REFERENCES supplier (id_supplier)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_pembelian_user
        FOREIGN KEY (id_user)
        REFERENCES users (id_user)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: detail_pembelian
-- Rincian produk dalam satu catatan restock
-- harga_beli = harga beli per satuan saat restock terjadi
-- (bisa berbeda antar restock jika harga supplier berubah)
-- ------------------------------------------------------------
CREATE TABLE detail_pembelian (
    id_detail_beli INT            NOT NULL AUTO_INCREMENT,
    id_pembelian   INT            NOT NULL,
    id_produk      INT            NOT NULL,
    jumlah         INT            NOT NULL,
    harga_beli     DECIMAL(10, 2) NOT NULL, -- snapshot harga beli
    subtotal       DECIMAL(10, 2) NOT NULL, -- jumlah × harga_beli
    PRIMARY KEY (id_detail_beli),
    CONSTRAINT fk_detail_pembelian
        FOREIGN KEY (id_pembelian)
        REFERENCES pembelian (id_pembelian)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_detail_pembelian_produk
        FOREIGN KEY (id_produk)
        REFERENCES produk (id_produk)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODUL 5 — MANAJEMEN HUTANG PELANGGAN
-- Tabel  : pelanggan, hutang, detail_hutang
-- ============================================================

-- ------------------------------------------------------------
-- Tabel: pelanggan
-- Menyimpan data pelanggan warung (tetangga/warga sekitar)
-- yang diizinkan mengambil barang dulu (ngutang)
-- ------------------------------------------------------------
CREATE TABLE pelanggan (
    id_pelanggan   INT          NOT NULL AUTO_INCREMENT,
    nama_pelanggan VARCHAR(150) NOT NULL,
    no_telepon     VARCHAR(20)           DEFAULT NULL,
    alamat         TEXT                  DEFAULT NULL, -- contoh: "Tetangga sebelah kanan"
    keterangan     TEXT                  DEFAULT NULL,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_pelanggan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: hutang
-- Header setiap catatan hutang pelanggan
-- total_hutang dihitung dari SUM(subtotal) di detail_hutang
-- ------------------------------------------------------------
CREATE TABLE hutang (
    id_hutang       INT            NOT NULL AUTO_INCREMENT,
    id_pelanggan    INT            NOT NULL,
    id_user         INT            NOT NULL,           -- user yang mencatat
    kode_hutang     VARCHAR(50)             DEFAULT NULL, -- contoh: HTG-20240601-001
    total_hutang    DECIMAL(10, 2) NOT NULL,
    jumlah_terbayar DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    sisa_hutang     DECIMAL(10, 2)          DEFAULT NULL, -- total_hutang - jumlah_terbayar
    status          ENUM('aktif', 'lunas')  NOT NULL DEFAULT 'aktif',
    tanggal_hutang  DATE           NOT NULL,
    tanggal_lunas   DATE                    DEFAULT NULL, -- diisi otomatis saat lunas
    keterangan      TEXT                    DEFAULT NULL,
    created_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_hutang),
    UNIQUE KEY uk_kode_hutang (kode_hutang),
    CONSTRAINT fk_hutang_pelanggan
        FOREIGN KEY (id_pelanggan)
        REFERENCES pelanggan (id_pelanggan)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_hutang_user
        FOREIGN KEY (id_user)
        REFERENCES users (id_user)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: detail_hutang
-- Rincian produk dalam satu catatan hutang
-- harga_satuan = snapshot harga jual saat hutang dicatat
-- Stok produk berkurang saat baris ini disimpan
-- Stok dikembalikan saat baris/hutang dihapus (rollback)
-- ------------------------------------------------------------
CREATE TABLE detail_hutang (
    id_detail_hutang INT            NOT NULL AUTO_INCREMENT,
    id_hutang        INT            NOT NULL,
    id_produk        INT            NOT NULL,
    jumlah           INT            NOT NULL,
    harga_satuan     DECIMAL(10, 2) NOT NULL, -- snapshot harga jual
    subtotal         DECIMAL(10, 2) NOT NULL, -- jumlah × harga_satuan
    PRIMARY KEY (id_detail_hutang),
    CONSTRAINT fk_detail_hutang
        FOREIGN KEY (id_hutang)
        REFERENCES hutang (id_hutang)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_detail_hutang_produk
        FOREIGN KEY (id_produk)
        REFERENCES produk (id_produk)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- VERIFIKASI STRUKTUR
-- Jalankan query berikut setelah import untuk memastikan
-- semua tabel berhasil dibuat:
--
-- SHOW TABLES;
--
-- Hasil yang diharapkan (11 tabel):
-- +----------------------+
-- | Tables_in_calontong  |
-- +----------------------+
-- | detail_hutang        |
-- | detail_pembelian     |
-- | detail_transaksi     |
-- | hutang               |
-- | kategori             |
-- | log_login            |
-- | pelanggan            |
-- | pembelian            |
-- | produk               |
-- | supplier             |
-- | transaksi            |
-- | users                |
-- +----------------------+
-- ============================================================
