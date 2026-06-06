<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/sidebar.php';

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];

function dashboard_scalar($conn, $sql)
{
    $result = $conn->query($sql);

    if (!$result) {
        return 0;
    }

    $row = $result->fetch_row();
    return $row[0] ?? 0;
}

if ($role === 'kasir') {
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM transaksi 
        WHERE id_user = ? 
        AND DATE(created_at) = CURDATE() 
        AND status = 'selesai'
    ");

    $stmt->bind_param('i', $_SESSION['id_user']);
    $stmt->execute();

    $transaksiHariIni = (int) ($stmt->get_result()->fetch_row()[0] ?? 0);
} else {
    $stats = [
        'produk_aktif' => (int) dashboard_scalar($conn, 'SELECT COUNT(*) FROM produk'),

        'stok_kritis' => (int) dashboard_scalar(
            $conn, 
            'SELECT COUNT(*) FROM produk WHERE stok <= stok_minimum'
        ),

        'penjualan_hari_ini' => (float) dashboard_scalar(
            $conn, 
            "SELECT COALESCE(SUM(total_harga), 0) 
             FROM transaksi 
             WHERE DATE(created_at) = CURDATE() 
             AND status = 'selesai'"
        ),

        'jumlah_transaksi' => (int) dashboard_scalar(
            $conn, 
            "SELECT COUNT(*) 
             FROM transaksi 
             WHERE DATE(created_at) = CURDATE() 
             AND status = 'selesai'"
        ),

        'hutang_aktif' => (float) dashboard_scalar(
            $conn, 
            "SELECT COALESCE(SUM(sisa_hutang), 0) 
             FROM hutang 
             WHERE status = 'aktif'"
        ),

        'penghutang' => (int) dashboard_scalar(
            $conn, 
            "SELECT COUNT(DISTINCT id_pelanggan) 
             FROM hutang 
             WHERE status = 'aktif'"
        ),
    ];
}

render_page_start('Dashboard', 'dashboard');
?>

<?php if ($role === 'kasir'): ?>

    <section class="cashier-dashboard">
        <h1>Halo<br><span><?= e(strtok($nama, ' ') ?: $nama); ?>!</span></h1>

        <a 
            class="btn btn-primary btn-lg cashier-primary" 
            href="<?= e(base_url('coming_soon.php?module=Transaksi%20Baru')); ?>"
        >
            <i class="bi bi-plus-square-fill"></i> Buat Transaksi Baru
        </a>

        <div class="panel-card">
            <h2>Cari Produk</h2>

            <form action="<?= e(base_url('pages/produk/index.php')); ?>" method="get">
                <div class="input-icon mb-3">
                    <i class="bi bi-search"></i>
                    <input 
                        class="form-control" 
                        type="search" 
                        name="q" 
                        placeholder="Masukan Nama Produk"
                    >
                </div>

                <button class="btn btn-primary w-100" type="submit">
                    Cari
                </button>
            </form>
        </div>

        <div class="panel-card">
            <h2>Ringkasan Transaksi Harian</h2>
            <div class="daily-summary">
                <i class="bi bi-table"></i>
                <span><?= e($transaksiHariIni); ?> transaksi hari ini</span>
            </div>
        </div>
    </section>

<?php else: ?>

    <section class="stats-grid">
        <?php
        $cards = [
            [
                'title' => 'Total Produk Aktif',
                'value' => $stats['produk_aktif'],
                'icon' => 'bi-box-seam',
                'color' => 'green',
                'url' => base_url('pages/produk/index.php')
            ],
            [
                'title' => 'Jumlah Produk Stok Kritis',
                'value' => $stats['stok_kritis'],
                'icon' => 'bi-exclamation-triangle',
                'color' => 'red',
                'url' => base_url('pages/produk/index.php?filter=stok-kritis')
            ],
            [
                'title' => 'Total Penjualan Hari Ini',
                'value' => format_rupiah($stats['penjualan_hari_ini']),
                'icon' => 'bi-cart-check',
                'color' => 'green',
                'url' => base_url('coming_soon.php?module=Laporan%20Penjualan')
            ],
            [
                'title' => 'Jumlah Transaksi Hari Ini',
                'value' => $stats['jumlah_transaksi'],
                'icon' => 'bi-receipt',
                'color' => 'green',
                'url' => base_url('coming_soon.php?module=Riwayat%20Transaksi')
            ],
            [
                'title' => 'Total Hutang Aktif',
                'value' => format_rupiah($stats['hutang_aktif']),
                'icon' => 'bi-wallet2',
                'color' => 'green',
                'url' => base_url('pages/Hutang/manajemen-hutang.php')
            ],
            [
                'title' => 'Jumlah Penghutang',
                'value' => $stats['penghutang'],
                'icon' => 'bi-people',
                'color' => 'green',
                'url' => base_url('pages/Hutang/manajemen-hutang.php')
            ],
        ];
        ?>

        <?php foreach ($cards as $card): ?>
            <a 
                href="<?= e($card['url']); ?>" 
                class="stat-card <?= e($card['color']); ?> text-decoration-none"
            >
                <span class="stat-icon">
                    <i class="bi <?= e($card['icon']); ?>"></i>
                </span>

                <div>
                    <h2><?= e($card['title']); ?></h2>
                    <strong><?= e($card['value']); ?></strong>
                </div>

                <i class="bi bi-chevron-right stat-arrow"></i>
            </a>
        <?php endforeach; ?>
    </section>

<?php endif; ?>

<?php render_page_end(); ?>
