<?php
require_once __DIR__ . '/../../includes/auth_check.php';

require_role('owner');

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/users/index.php');
}

$id = (int) ($_POST['id_user'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'ID pengguna tidak valid.');
    redirect('pages/users/index.php');
}

// Tidak boleh hapus akun sendiri
if ($id === (int) $_SESSION['id_user']) {
    set_flash('danger', 'Anda tidak dapat menghapus akun Anda sendiri.');
    redirect('pages/users/index.php');
}

// Ambil data user untuk konfirmasi
$stmt = $conn->prepare('SELECT id_user, nama_lengkap, username FROM users WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    set_flash('danger', 'Pengguna tidak ditemukan.');
    redirect('pages/users/index.php');
}

// Cek apakah user memiliki transaksi (RESTRICT pada FK)
$chkTrx = $conn->prepare('SELECT COUNT(*) FROM transaksi WHERE id_user = ? LIMIT 1');
$chkTrx->bind_param('i', $id);
$chkTrx->execute();
$hasTrx = (int) $chkTrx->get_result()->fetch_row()[0];

if ($hasTrx > 0) {
    set_flash('danger', 'Pengguna <strong>' . htmlspecialchars($user['nama_lengkap']) . '</strong> tidak dapat dihapus karena memiliki data transaksi terkait.');
    redirect('pages/users/index.php');
}

// Cek pembelian
$chkBeli = $conn->prepare('SELECT COUNT(*) FROM pembelian WHERE id_user = ? LIMIT 1');
$chkBeli->bind_param('i', $id);
$chkBeli->execute();
$hasBeli = (int) $chkBeli->get_result()->fetch_row()[0];

if ($hasBeli > 0) {
    set_flash('danger', 'Pengguna <strong>' . htmlspecialchars($user['nama_lengkap']) . '</strong> tidak dapat dihapus karena memiliki data pembelian terkait.');
    redirect('pages/users/index.php');
}

// Aman untuk dihapus
try {
    $del = $conn->prepare('DELETE FROM users WHERE id_user = ?');
    $del->bind_param('i', $id);
    $del->execute();

    set_flash('success', 'Pengguna <strong>' . htmlspecialchars($user['nama_lengkap']) . '</strong> berhasil dihapus.');
} catch (mysqli_sql_exception $e) {
    set_flash('danger', 'Gagal menghapus pengguna. Pengguna mungkin masih memiliki data terkait di sistem.');
}

redirect('pages/users/index.php');
?>
