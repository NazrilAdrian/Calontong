# Spesifikasi Proyek: Aplikasi Warung Kelontong

## 1. Teknologi & Batasan Teknis

- **Bahasa**: PHP Native (Tanpa Framework seperti Laravel/CI)
- **Database**: MySQL dengan 6 tabel: USERS, CATEGORIES, PRODUCTS, CUSTOMERS, TRANSACTIONS, TRANSACTION_DETAILS.
- **Frontend**: HTML5, CSS Berwarna/Custom, dan Bootstrap (via CDN) agar responsif.
- **Keamanan**: Password di-hash dengan `password_hash()`, input disanitasi, proteksi session di setiap halaman internal.
- **Arsitektur**: Modular menggunakan instruksi `include` atau `require` secara konsisten untuk memisahkan komponen header, footer, sidebar, dan koneksi agar proyek tetap rapi dan kompatibel.

## 2. Struktur Folder Proyek Lengkap (Wajib Dipatuhi)

warung_kelontong/
├── .gitignore ← Mengabaikan file sampah lokal/OS/IDE
├── README.md ← Deskripsi proyek & petunjuk instalasi
├── warung_kelontong.sql ← Berkas SQL Database (Wajib ada)
├── index.php ← Redirect ke login atau dashboard
├── login.php
├── logout.php
├── includes/
│ ├── koneksi.php ← Koneksi database
│ ├── header.php ← Navbar + Sidebar + Bootstrap CDN
│ ├── footer.php ← Closing tags + Bootstrap JS CDN
│ └── auth_check.php ← Proteksi halaman (session check)
├── pages/
│ ├── dashboard.php
│ ├── users/
│ │ ├── index.php ← Daftar user
│ │ ├── tambah.php
│ │ ├── edit.php
│ │ └── hapus.php
│ ├── categories/
│ │ ├── index.php
│ │ ├── tambah.php
│ │ ├── edit.php
│ │ └── hapus.php
│ ├── products/
│ │ ├── index.php
│ │ ├── tambah.php
│ │ ├── edit.php
│ │ └── hapus.php ← CRUD + Upload foto produk
│ ├── customers/
│ │ ├── index.php
│ │ ├── tambah.php
│ │ ├── edit.php
│ │ └── hapus.php
│ ├── transactions/
│ │ ├── index.php
│ │ ├── kasir.php ← Form transaksi baru (POS Kasir)
│ │ └── detail.php
│ └── reports/
│ ├── harian.php
│ └── export.php ← Fitur cetak/export laporan
└── assets/
├── css/
│ └── custom.css
├── js/
│ └── custom.js
└── uploads/
└── products/ ← Tempat penyimpanan foto produk
