<?php
require_once __DIR__ . "/../../includes/auth_check.php";

require_role(['owner', 'admin']);

$id_pelanggan = $_GET["id"] ?? null;

if (!$id_pelanggan || !is_numeric($id_pelanggan)) {
    header("Location: manajemen-hutang.php");
    exit;
}

$id_pelanggan = (int) $id_pelanggan;

$cekHutang = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM hutang 
    WHERE id_pelanggan = ?
");

$cekHutang->bind_param("i", $id_pelanggan);
$cekHutang->execute();
$totalHutang = (int) $cekHutang->get_result()->fetch_assoc()["total"];

if ($totalHutang > 0) {
    header("Location: manajemen-hutang.php?status=hapus-pelanggan-gagal");
    exit;
}

$hapus = $conn->prepare("
    DELETE FROM pelanggan 
    WHERE id_pelanggan = ?
");

$hapus->bind_param("i", $id_pelanggan);
$hapus->execute();

header("Location: manajemen-hutang.php?status=hapus-pelanggan-berhasil");
exit;
