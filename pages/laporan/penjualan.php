<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../transaksi/_helpers.php';

require_role(['owner', 'admin']);

$conn = calontong_db();
$tanggalMulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
$tanggalSelesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');
$canAccess = true;
$summary = [
    'total_pendapatan' => 0,
    'jumlah_transaksi' => 0,
];
$totalLaba = 0; // Variabel baru untuk menampung laba bersih
$transactions = [];

if ($conn && $canAccess) {
    // 1. Ambil summary pendapatan & jumlah transaksi
    $summary = fetch_one(
        'SELECT COALESCE(SUM(total_harga), 0) AS total_pendapatan, COUNT(*) AS jumlah_transaksi
         FROM transaksi
         WHERE status = "selesai"
           AND DATE(created_at) BETWEEN ? AND ?',
        'ss',
        [$tanggalMulai, $tanggalSelesai]
    ) ?: $summary;

    // 2. KODE TAMBAHAN: Ambil summary Laba Bersih (Harga Jual - Harga Beli)
    $dataLaba = fetch_one(
        'SELECT SUM((dt.harga_satuan - p.harga_beli) * dt.jumlah) as laba_bersih 
         FROM transaksi t 
         JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi 
         JOIN produk p ON dt.id_produk = p.id_produk 
         WHERE t.status = "selesai"
           AND DATE(t.created_at) BETWEEN ? AND ?',
        'ss',
        [$tanggalMulai, $tanggalSelesai]
    );
    $totalLaba = (float) ($dataLaba['laba_bersih'] ?? 0);

    // 3. Ambil daftar riwayat transaksi
    $transactions = fetch_all(
        'SELECT t.id_transaksi, t.kode_transaksi, t.total_harga, t.uang_bayar, t.kembalian, t.created_at, u.nama_lengkap
         FROM transaksi t
         JOIN users u ON u.id_user = t.id_user
         WHERE t.status = "selesai"
           AND DATE(t.created_at) BETWEEN ? AND ?
         ORDER BY t.created_at DESC, t.id_transaksi DESC',
        'ss',
        [$tanggalMulai, $tanggalSelesai]
    );
}

$messages = take_flash();
?>
<?php render_page_start('Laporan Penjualan', 'transaksi', ['assets/css/transaksi.css']); ?>
<div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 col-xxl-9">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-4">
                    <h1 class="h5 fw-bold mb-0">Laporan Penjualan</h1>
                    <a href="../transaksi/index.php" class="btn btn-outline-secondary btn-rounded px-4">Kembali</a>
                </div>

                <?php if (!$conn): ?>
                    <div class="alert alert-warning">
                        Koneksi database belum tersedia. Buat <code>config.php</code> dengan variabel <code>$conn</code>.
                    </div>
                <?php endif; ?>

                <?php if (!$canAccess): ?>
                    <div class="alert alert-danger">
                        Laporan penjualan hanya bisa diakses oleh owner/admin.
                    </div>
                <?php endif; ?>

                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= h($message['type']); ?> alert-dismissible fade show" role="alert">
                        <?= h($message['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endforeach; ?>

                <div class="card border card-rounded mb-4">
                    <div class="card-body">
                        <div class="fw-semibold mb-3">Filter periode</div>
                        <form method="get" class="row g-3 align-items-end">
                            <div class="col-12 col-md-5">
                                <label for="tanggalMulai" class="form-label">Dari tanggal</label>
                                <input type="date" class="form-control" id="tanggalMulai" name="tanggal_mulai" value="<?= h($tanggalMulai); ?>">
                            </div>
                            <div class="col-12 col-md-5">
                                <label for="tanggalSelesai" class="form-label">Sampai tanggal</label>
                                <input type="date" class="form-control" id="tanggalSelesai" name="tanggal_selesai" value="<?= h($tanggalSelesai); ?>">
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-rounded" <?= !$conn ? 'disabled' : ''; ?>>Tampilkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    
                    <div class="col-12 col-md-4">
                        <div class="card border card-rounded h-100">
                            <div class="card-body">
                                <div class="text-muted small">Total Pendapatan (Omzet)</div>
                                <div class="h4 fw-bold mb-0 text-dark"><?= rupiah($summary['total_pendapatan'] ?? 0); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card border card-rounded sales-profit-card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="text-muted small">Total Bersih (Laba/Profit)</div>
                                <div class="h4 fw-bold mb-0 text-success"><?= rupiah($totalLaba); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card border card-rounded h-100">
                            <div class="card-body">
                                <div class="text-muted small">Jumlah Transaksi Selesai</div>
                                <div class="h4 fw-bold mb-0 text-dark"><?= (int) ($summary['jumlah_transaksi'] ?? 0); ?></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card border card-rounded">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Trx</th>
                                        <th>Tgl</th>
                                        <th>Kasir</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Uang bayar</th>
                                        <th class="text-end">Kembalian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($transactions)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada transaksi selesai pada periode ini.
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td>
                                                <a href="../transaksi/detail.php?id=<?= (int) $transaction['id_transaksi']; ?>" class="text-decoration-none">
                                                    <?= h($transaction['kode_transaksi']); ?>
                                                </a>
                                            </td>
                                            <td><?= h(date('d/m/Y H:i', strtotime($transaction['created_at']))); ?></td>
                                            <td><?= h($transaction['nama_lengkap']); ?></td>
                                            <td class="text-end"><?= rupiah($transaction['total_harga']); ?></td>
                                            <td class="text-end"><?= rupiah($transaction['uang_bayar']); ?></td>
                                            <td class="text-end"><?= rupiah($transaction['kembalian']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<?php render_page_end(); ?>
