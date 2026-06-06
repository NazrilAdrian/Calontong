<?php
require_once __DIR__ . "/../../config/koneksi.php";

$id_hutang = $_GET["id"] ?? null;

if (!$id_hutang || !is_numeric($id_hutang)) {
    header("Location: manajemen-hutang.php");
    exit;
}

try {
    $pdo->beginTransaction();

    $cekHutang = $pdo->prepare("
        SELECT id_hutang 
        FROM hutang 
        WHERE id_hutang = :id_hutang
    ");

    $cekHutang->execute([
        ":id_hutang" => $id_hutang
    ]);

    $hutang = $cekHutang->fetch(PDO::FETCH_ASSOC);

    if (!$hutang) {
        $pdo->rollBack();
        header("Location: manajemen-hutang.php?status=hapus-hutang-gagal");
        exit;
    }

    $queryDetail = $pdo->prepare("
        SELECT 
            id_produk,
            jumlah
        FROM detail_hutang
        WHERE id_hutang = :id_hutang
    ");

    $queryDetail->execute([
        ":id_hutang" => $id_hutang
    ]);

    $detailHutang = $queryDetail->fetchAll(PDO::FETCH_ASSOC);

    $updateStok = $pdo->prepare("
        UPDATE produk
        SET stok = stok + :jumlah
        WHERE id_produk = :id_produk
    ");

    foreach ($detailHutang as $detail) {
        $updateStok->execute([
            ":jumlah" => $detail["jumlah"],
            ":id_produk" => $detail["id_produk"]
        ]);
    }

    $hapusHutang = $pdo->prepare("
        DELETE FROM hutang
        WHERE id_hutang = :id_hutang
    ");

    $hapusHutang->execute([
        ":id_hutang" => $id_hutang
    ]);

    $pdo->commit();

    header("Location: manajemen-hutang.php?status=hapus-hutang-berhasil");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: manajemen-hutang.php?status=hapus-hutang-gagal");
    exit;
}