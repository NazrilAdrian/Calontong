-- ==========================================================
-- DATABASE: calontong
-- ==========================================================
CREATE DATABASE IF NOT EXISTS `calontong`;
USE `calontong`;

-- ==========================================================
-- 1. TABEL users
-- ==========================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('owner','admin','kasir') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 1B. TABEL log_login (Audit Login)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `log_login` (
  `id_log` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) DEFAULT NULL,
  `username_input` VARCHAR(50) NOT NULL,
  `waktu_login` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('berhasil','gagal') NOT NULL,
  `keterangan` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 2. TABEL kategori
-- ==========================================================
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 3. TABEL supplier
-- ==========================================================
CREATE TABLE IF NOT EXISTS `supplier` (
  `id_supplier` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_supplier` VARCHAR(150) NOT NULL,
  `nama_kontak` VARCHAR(100) DEFAULT NULL,
  `no_telepon` VARCHAR(20) DEFAULT NULL,
  `alamat` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 4. TABEL pelanggan
-- ==========================================================
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id_pelanggan` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` VARCHAR(150) NOT NULL,
  `no_telepon` VARCHAR(20) DEFAULT NULL,
  `alamat` TEXT,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 5. TABEL produk (Berelasi dengan kategori)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `produk` (
  `id_produk` INT(11) NOT NULL AUTO_INCREMENT,
  `id_kategori` INT(11) NOT NULL,
  `kode_produk` VARCHAR(50) DEFAULT NULL,
  `nama_produk` VARCHAR(150) NOT NULL,
  `harga_beli` DECIMAL(10,2) NOT NULL,
  `harga_jual` DECIMAL(10,2) NOT NULL,
  `stok` INT(11) NOT NULL DEFAULT 0,
  `stok_minimum` INT(11) DEFAULT 0,
  `satuan` VARCHAR(30) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produk`),
  UNIQUE KEY `kode_produk` (`kode_produk`),
  FOREIGN KEY (`id_kategori`) REFERENCES `kategori`(`id_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 6. TABEL transaksi (Berelasi dengan users)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id_transaksi` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) NOT NULL,
  `kode_transaksi` VARCHAR(50) DEFAULT NULL,
  `total_harga` DECIMAL(10,2) NOT NULL,
  `uang_bayar` DECIMAL(10,2) DEFAULT NULL,
  `kembalian` DECIMAL(10,2) DEFAULT NULL,
  `status` ENUM('selesai','batal') NOT NULL DEFAULT 'selesai',
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_transaksi`),
  UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 7. TABEL detail_transaksi (Berelasi dengan transaksi & produk)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `detail_transaksi` (
  `id_detail` INT(11) NOT NULL AUTO_INCREMENT,
  `id_transaksi` INT(11) NOT NULL,
  `id_produk` INT(11) NOT NULL,
  `jumlah` INT(11) NOT NULL,
  `harga_satuan` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi`(`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_produk`) REFERENCES `produk`(`id_produk`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 8. TABEL pembelian (Berelasi dengan supplier & users)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `pembelian` (
  `id_pembelian` INT(11) NOT NULL AUTO_INCREMENT,
  `id_supplier` INT(11) NOT NULL,
  `id_user` INT(11) NOT NULL,
  `kode_pembelian` VARCHAR(50) DEFAULT NULL,
  `total_harga` DECIMAL(10,2) DEFAULT 0.00,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pembelian`),
  UNIQUE KEY `kode_pembelian` (`kode_pembelian`),
  FOREIGN KEY (`id_supplier`) REFERENCES `supplier`(`id_supplier`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 9. TABEL detail_pembelian (Berelasi dengan pembelian & produk)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `detail_pembelian` (
  `id_detail_beli` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pembelian` INT(11) NOT NULL,
  `id_produk` INT(11) NOT NULL,
  `jumlah` INT(11) NOT NULL,
  `harga_beli` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id_detail_beli`),
  FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian`(`id_pembelian`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_produk`) REFERENCES `produk`(`id_produk`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 10. TABEL hutang (Berelasi dengan pelanggan & users)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `hutang` (
  `id_hutang` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pelanggan` INT(11) NOT NULL,
  `id_user` INT(11) NOT NULL,
  `kode_hutang` VARCHAR(50) DEFAULT NULL,
  `total_hutang` DECIMAL(10,2) NOT NULL,
  `jumlah_terbayar` DECIMAL(10,2) DEFAULT 0.00,
  `sisa_hutang` DECIMAL(10,2) DEFAULT 0.00,
  `status` ENUM('aktif','lunas') NOT NULL DEFAULT 'aktif',
  `tanggal_hutang` DATE NOT NULL,
  `tanggal_lunas` DATE DEFAULT NULL,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_hutang`),
  UNIQUE KEY `kode_hutang` (`kode_hutang`),
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan`(`id_pelanggan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 11. TABEL detail_hutang (Berelasi dengan hutang & produk)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `detail_hutang` (
  `id_detail_hutang` INT(11) NOT NULL AUTO_INCREMENT,
  `id_hutang` INT(11) NOT NULL,
  `id_produk` INT(11) NOT NULL,
  `jumlah` INT(11) NOT NULL,
  `harga_satuan` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id_detail_hutang`),
  FOREIGN KEY (`id_hutang`) REFERENCES `hutang`(`id_hutang`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_produk`) REFERENCES `produk`(`id_produk`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- DATA DUMMY AWAL (Kategori & Produk)
-- ==========================================================
INSERT INTO `kategori` (`nama_kategori`, `deskripsi`) VALUES
('Sembako', 'Kebutuhan pokok dapur'),
('Minuman', 'Minuman dingin dan sachet'),
('Snack', 'Makanan ringan dan camilan');

-- Perhatikan: id_kategori harus sesuai dengan insert di atas (1=Sembako, 2=Minuman, 3=Snack)
INSERT INTO `produk` (`id_kategori`, `kode_produk`, `nama_produk`, `harga_beli`, `harga_jual`, `stok`, `stok_minimum`, `satuan`) VALUES
(1, 'PRD-001', 'Beras Sania 5kg', 65000.00, 72000.00, 15, 5, 'karung'),
(1, 'PRD-002', 'Minyak Goreng Bimoli 1L', 14000.00, 16000.00, 24, 10, 'liter'),
(2, 'PRD-003', 'Teh Pucuk Harum 350ml', 3000.00, 4000.00, 48, 12, 'botol'),
(3, 'PRD-004', 'Indomie Goreng', 2600.00, 3500.00, 80, 20, 'bungkus');

-- Data Supplier & Pelanggan Dummy
INSERT INTO `supplier` (`nama_supplier`, `nama_kontak`, `no_telepon`) VALUES
('Agen Sembako Makmur', 'Pak Budi', '08123456789'),
('Distributor Wings Food', 'Mas Andi', '08198765432');

INSERT INTO `pelanggan` (`nama_pelanggan`, `alamat`, `keterangan`) VALUES
('Bu Tejo', 'Rumah Pagar Hijau Blok A2', 'Sering ambil beras bulanan'),
('Pak RT (Agus)', 'Depan Pos Kamling', '');
