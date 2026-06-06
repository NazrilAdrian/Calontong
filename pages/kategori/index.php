<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_role(['owner', 'admin']);

redirect('pages/produk/index.php#kategori');
?>
