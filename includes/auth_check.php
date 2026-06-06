<?php
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['id_user'])) {
    set_flash('danger', 'Silakan login terlebih dahulu.');
    redirect('login.php');
}

function require_role($roles)
{
    $roles = (array) $roles;

    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        set_flash('danger', 'Akses ditolak. Halaman ini tidak sesuai dengan role Anda.');
        redirect('dashboard.php');
    }
}
?>
