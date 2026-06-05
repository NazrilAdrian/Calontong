<?php
// Konfigurasi koneksi database Ca'lontong.
// Sesuaikan nilai berikut jika konfigurasi Laragon/XAMPP berbeda.
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'calontong';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    $GLOBALS['calontong_db_error'] = $conn->connect_error;
    $conn = null;
} else {
    $conn->set_charset('utf8mb4');
}

// Fallback demo sementara sampai modul autentikasi selesai.
// Session login asli tetap diprioritaskan oleh helper transaksi.
define('CALONTONG_DEV_USER_ID', 1);
define('CALONTONG_DEV_ROLE', 'owner');
