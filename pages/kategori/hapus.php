<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['owner', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/produk/index.php#kategori');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'Kategori tidak valid.');
    redirect('pages/produk/index.php#kategori');
}

$stmt = $conn->prepare('SELECT nama_kategori FROM kategori WHERE id_kategori = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$kategori = $stmt->get_result()->fetch_assoc();

if (!$kategori) {
    set_flash('danger', 'Kategori tidak ditemukan.');
    redirect('pages/produk/index.php#kategori');
}

$stmt = $conn->prepare('SELECT COUNT(*) FROM produk WHERE id_kategori = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$used = (int) ($stmt->get_result()->fetch_row()[0] ?? 0);

if ($used > 0) {
    set_flash('danger', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
    redirect('pages/produk/index.php#kategori');
}

$stmt = $conn->prepare('DELETE FROM kategori WHERE id_kategori = ?');
$stmt->bind_param('i', $id);
$stmt->execute();

set_flash('success', 'Kategori berhasil dihapus.');
redirect('pages/produk/index.php#kategori');
?>
