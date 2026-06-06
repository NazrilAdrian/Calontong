<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['owner', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/produk/index.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'Produk tidak valid.');
    redirect('pages/produk/index.php');
}

$stmt = $conn->prepare('SELECT nama_produk FROM produk WHERE id_produk = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$produk = $stmt->get_result()->fetch_assoc();

if (!$produk) {
    set_flash('danger', 'Produk tidak ditemukan.');
    redirect('pages/produk/index.php');
}

$tables = ['detail_transaksi', 'detail_pembelian', 'detail_hutang'];
foreach ($tables as $table) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE id_produk = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $count = (int) ($stmt->get_result()->fetch_row()[0] ?? 0);

    if ($count > 0) {
        set_flash('danger', 'Produk tidak dapat dihapus karena masih memiliki riwayat transaksi, restock, atau hutang.');
        redirect('pages/produk/index.php');
    }
}

$stmt = $conn->prepare('DELETE FROM produk WHERE id_produk = ?');
$stmt->bind_param('i', $id);
$stmt->execute();

set_flash('success', 'Produk berhasil dihapus.');
redirect('pages/produk/index.php');
?>
