<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/sidebar.php';

$module = trim($_GET['module'] ?? 'Modul ini');

render_page_start('Coming Soon', '');
?>
<section class="coming-soon">
    <div class="soft-icon"><i class="bi bi-hourglass-split"></i></div>
    <h1><?= e($module); ?></h1>
    <p>Halaman ini sedang disiapkan. Untuk sesi ini, modul yang sudah aktif adalah Manajemen Produk & Kategori.</p>
    <a class="btn btn-primary" href="<?= e(base_url('pages/produk/index.php')); ?>">Buka Produk & Kategori</a>
</section>
<?php render_page_end(); ?>
