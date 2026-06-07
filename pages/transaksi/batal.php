<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('index.php');
}

$conn = calontong_db();
$idTransaksi = (int) ($_POST['id'] ?? 0);

if (!$conn) {
    flash('danger', 'Koneksi database belum tersedia.');
    redirect_to('index.php');
}

if (!is_manager_role()) {
    flash('danger', 'Hanya owner/admin yang bisa membatalkan transaksi.');
    redirect_to('detail.php?id=' . $idTransaksi);
}

if ($idTransaksi <= 0) {
    flash('danger', 'ID transaksi tidak valid.');
    redirect_to('index.php');
}

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare('SELECT id_transaksi, status FROM transaksi WHERE id_transaksi = ? FOR UPDATE');
    $stmt->bind_param('i', $idTransaksi);
    $stmt->execute();
    $transaction = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$transaction) {
        throw new Exception('Transaksi tidak ditemukan.');
    }

    if ($transaction['status'] === 'batal') {
        throw new Exception('Transaksi ini sudah dibatalkan.');
    }

    $details = fetch_all(
        'SELECT id_produk, jumlah FROM detail_transaksi WHERE id_transaksi = ?',
        'i',
        [$idTransaksi]
    );

    $stmtStok = $conn->prepare('UPDATE produk SET stok = stok + ? WHERE id_produk = ?');

    foreach ($details as $detail) {
        $jumlah = (int) $detail['jumlah'];
        $idProduk = (int) $detail['id_produk'];

        $stmtStok->bind_param('ii', $jumlah, $idProduk);
        $stmtStok->execute();
    }

    $stmtStok->close();

    $stmt = $conn->prepare('UPDATE transaksi SET status = "batal", keterangan = ? WHERE id_transaksi = ?');
    $keterangan = 'Dibatalkan oleh ' . (current_role() ?: 'user') . ' pada ' . date('Y-m-d H:i:s');

    $stmt->bind_param('si', $keterangan, $idTransaksi);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    flash('success', 'Transaksi berhasil dibatalkan dan stok sudah dikembalikan.');
} catch (Exception $e) {
    $conn->rollback();

    flash('danger', $e->getMessage());
}

redirect_to('detail.php?id=' . $idTransaksi);
