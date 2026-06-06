<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/module4_helpers.php';
require_role(['owner', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/restock/index.php');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Restock tidak valid.');
    redirect('pages/restock/index.php');
}

$purchase = module4_fetch_purchase_with_details($conn, $id);
if (!$purchase) {
    set_flash('danger', 'Data restock tidak ditemukan.');
    redirect('pages/restock/index.php');
}

$productMap = [];
$productOptions = module4_fetch_product_options($conn);
foreach ($productOptions as $product) {
    $productMap[(int) $product['id_produk']] = $product;
}

$oldItems = module4_enrich_cart($purchase['details'], $productMap);

$conn->begin_transaction();
try {
    module4_sync_stok($conn, $oldItems, -1);

    $stmt = $conn->prepare('DELETE FROM pembelian WHERE id_pembelian = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $conn->commit();
    set_flash('success', 'Restock berhasil dihapus dan stok telah disesuaikan.');
    redirect('pages/restock/index.php');
} catch (Throwable $e) {
    $conn->rollback();
    set_flash('danger', 'Gagal menghapus restock.');
    redirect('pages/restock/index.php');
}

