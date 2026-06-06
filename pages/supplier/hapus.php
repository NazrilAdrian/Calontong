<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/module4_helpers.php';
require_role(['owner', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/restock/index.php#daftar-supplier');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Supplier tidak valid.');
    redirect('pages/restock/index.php#daftar-supplier');
}

$stmt = $conn->prepare('SELECT COUNT(*) FROM pembelian WHERE id_supplier = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$hasPurchase = (int) ($stmt->get_result()->fetch_row()[0] ?? 0);

if ($hasPurchase > 0) {
    set_flash('danger', 'Supplier tidak dapat dihapus karena masih memiliki riwayat restock.');
    redirect('pages/restock/index.php#daftar-supplier');
}

$stmt = $conn->prepare('DELETE FROM supplier WHERE id_supplier = ?');
$stmt->bind_param('i', $id);
$stmt->execute();

set_flash('success', 'Supplier berhasil dihapus.');
redirect('pages/restock/index.php#daftar-supplier');
