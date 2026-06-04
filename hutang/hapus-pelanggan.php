<?php
require_once __DIR__ . "/../config/koneksi.php";

$id_pelanggan = $_GET["id"] ?? null;

if (!$id_pelanggan) {
    header("Location: manajemen-hutang.php");
    exit;
}

// Cek apakah pelanggan masih punya data hutang
$cekHutang = $pdo->prepare("
    SELECT COUNT(*) 
    FROM hutang 
    WHERE id_pelanggan = :id_pelanggan
");

$cekHutang->execute([
    ":id_pelanggan" => $id_pelanggan
]);

$totalHutang = $cekHutang->fetchColumn();

if ($totalHutang > 0) {
    header("Location: manajemen-hutang.php?status=hapus-pelanggan-gagal");
    exit;
}

// Hapus pelanggan jika tidak punya data hutang
$hapus = $pdo->prepare("
    DELETE FROM pelanggan 
    WHERE id_pelanggan = :id_pelanggan
");

$hapus->execute([
    ":id_pelanggan" => $id_pelanggan
]);

header("Location: manajemen-hutang.php?status=hapus-pelanggan-berhasil");
exit;