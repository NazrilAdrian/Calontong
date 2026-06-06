<?php
require_once __DIR__ . '/../config.php';

function role_name($role)
{
    $labels = [
        'owner' => 'OWNER',
        'admin' => 'ADMIN',
        'kasir' => 'KASIR',
    ];

    return $labels[$role] ?? strtoupper((string) $role);
}

function role_subtitle($role)
{
    $labels = [
        'owner' => 'Pemilik Warung',
        'admin' => 'Pengelola Sistem',
        'kasir' => 'Pelayan Transaksi',
    ];

    return $labels[$role] ?? 'Pengguna';
}

function menu_items_for_role($role)
{
    $items = [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'icon' => 'bi-house-door-fill',
            'url' => base_url('dashboard.php'),
        ],
    ];

    if ($role === 'owner') {
        $items[] = [
            'key' => 'users',
            'label' => 'Manajemen Pengguna',
            'icon' => 'bi-people-fill',
            'url' => base_url('coming_soon.php?module=Manajemen%20Pengguna'),
        ];
    }

    $items[] = [
        'key' => 'produk',
        'label' => 'Produk & Kategori',
        'icon' => 'bi-box-seam-fill',
        'url' => base_url('pages/produk/index.php'),
    ];

    $items[] = [
        'key' => 'transaksi',
        'label' => 'Transaksi & Penjualan',
        'icon' => 'bi-cart-fill',
        'url' => base_url('coming_soon.php?module=Transaksi%20%26%20Penjualan'),
    ];

    if (in_array($role, ['owner', 'admin'], true)) {
        $items[] = [
            'key' => 'supplier',
            'label' => 'Supplier & Restock',
            'icon' => 'bi-truck',
            'url' => base_url('pages/restock/index.php'),
        ];
    }

    $items[] = [
        'key' => 'hutang',
        'label' => 'Hutang Pelanggan',
        'icon' => 'bi-wallet-fill',
        'url' => base_url('coming_soon.php?module=Hutang%20Pelanggan'),
    ];

    return $items;
}

function render_sidebar_menu($active = '')
{
    $role = $_SESSION['role'] ?? '';
    $items = menu_items_for_role($role);
    ?>
    <div class="sidebar-user">
        <div class="sidebar-user-icon"><i class="bi bi-person-fill"></i></div>
        <div>
            <div class="sidebar-role"><?= e(role_name($role)); ?></div>
            <div class="sidebar-subtitle"><?= e(role_subtitle($role)); ?></div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($items as $item): ?>
            <a class="sidebar-link <?= $active === $item['key'] ? 'active' : ''; ?>" href="<?= e($item['url']); ?>">
                <span class="sidebar-link-icon"><i class="bi <?= e($item['icon']); ?>"></i></span>
                <span><?= e($item['label']); ?></span>
                <i class="bi bi-chevron-right sidebar-chevron"></i>
            </a>
        <?php endforeach; ?>
    </nav>

    <a class="sidebar-link logout-link" href="<?= e(base_url('logout.php')); ?>">
        <span class="sidebar-link-icon"><i class="bi bi-box-arrow-right"></i></span>
        <span>Logout</span>
        <i class="bi bi-chevron-right sidebar-chevron"></i>
    </a>
    <?php
}

function render_page_start($title, $active = '', $extraCss = [])
{
    $flash = get_flash();
    ?>
    <!doctype html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title); ?> - Ca'lontong</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="<?= e(base_url('assets/css/style.css')); ?>" rel="stylesheet">
        <?php foreach ($extraCss as $css): ?>
            <link href="<?= e(base_url($css)); ?>" rel="stylesheet">
        <?php endforeach; ?>
    </head>
    <body>
        <header class="app-topbar">
            <button class="topbar-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="Buka menu">
                <i class="bi bi-list"></i>
            </button>
            <a class="brand" href="<?= e(base_url('dashboard.php')); ?>"><span>Ca'</span>lontong</a>
            <span class="topbar-spacer"></span>
        </header>

        <div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileSidebar">
            <div class="offcanvas-body">
                <?php render_sidebar_menu($active); ?>
            </div>
        </div>

        <aside class="desktop-sidebar">
            <?php render_sidebar_menu($active); ?>
        </aside>

        <main class="app-main">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']); ?> alert-dismissible fade show" role="alert">
                    <?= e($flash['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>
    <?php
}

function render_page_end()
{
    ?>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
?>
