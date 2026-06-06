<?php
require_once __DIR__ . "/../../includes/auth_check.php";

require_role(['owner', 'admin']);

$id_hutang = $_GET["id"] ?? null;

if (!$id_hutang || !is_numeric($id_hutang)) {
    header("Location: manajemen-hutang.php");
    exit;
}

$id_hutang = (int) $id_hutang;

try {
    $conn->begin_transaction();

    $cekHutang = $conn->prepare("
        SELECT id_hutang 
        FROM hutang 
        WHERE id_hutang = ?
    ");

    $cekHutang->bind_param("i", $id_hutang);
    $cekHutang->execute();
    $hutang = $cekHutang->get_result()->fetch_assoc();

    if (!$hutang) {
        $conn->rollback();
        header("Location: manajemen-hutang.php?status=hapus-hutang-gagal");
        exit;
    }

    $queryDetail = $conn->prepare("
        SELECT 
            id_produk,
            jumlah
        FROM detail_hutang
        WHERE id_hutang = ?
    ");

    $queryDetail->bind_param("i", $id_hutang);
    $queryDetail->execute();
    $detailHutang = $queryDetail->get_result()->fetch_all(MYSQLI_ASSOC);

    $updateStok = $conn->prepare("
        UPDATE produk
        SET stok = stok + ?
        WHERE id_produk = ?
    ");

    foreach ($detailHutang as $detail) {
        $jumlah = (int) $detail["jumlah"];
        $id_produk = (int) $detail["id_produk"];
        $updateStok->bind_param("ii", $jumlah, $id_produk);
        $updateStok->execute();
    }

    $hapusHutang = $conn->prepare("
        DELETE FROM hutang
        WHERE id_hutang = ?
    ");

    $hapusHutang->bind_param("i", $id_hutang);
    $hapusHutang->execute();

    $conn->commit();

    header("Location: manajemen-hutang.php?status=hapus-hutang-berhasil");
    exit;

} catch (Exception $e) {
    if ($conn->errno === 0) {
        $conn->rollback();
    }

    header("Location: manajemen-hutang.php?status=hapus-hutang-gagal");
    exit;
}
