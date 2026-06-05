<?php
require_once __DIR__ . '/../transaksi/_helpers.php';

$conn = calontong_db();
$tanggalMulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
$tanggalSelesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');
$canAccess = is_manager_role();
$summary = [
    'total_pendapatan' => 0,
    'jumlah_transaksi' => 0,
];
$transactions = [];

if ($conn && $canAccess) {
    $summary = fetch_one(
        'SELECT COALESCE(SUM(total_harga), 0) AS total_pendapatan, COUNT(*) AS jumlah_transaksi
         FROM transaksi
         WHERE status = "selesai"
           AND DATE(created_at) BETWEEN ? AND ?',
        'ss',
        [$tanggalMulai, $tanggalSelesai]
    ) ?: $summary;

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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ca'lontong - Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-rounded {
            border-radius: 18px;
        }

        .btn-rounded {
            border-radius: 999px;
        }
    </style>
</head>
<body class="bg-white">
    <main class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 col-xxl-9">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-4">
                    <h1 class="h5 fw-bold mb-0">Laporan Penjualan</h1>
                    <a href="../transaksi/index.php" class="btn btn-outline-secondary btn-rounded px-4">Riwayat transaksi</a>
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
                                    <button type="submit" class="btn btn-success btn-rounded" <?= (!$conn || !$canAccess) ? 'disabled' : ''; ?>>Tampilkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="card border card-rounded h-100">
                            <div class="card-body">
                                <div class="text-muted small">Total pendapatan</div>
                                <div class="h4 fw-bold mb-0"><?= rupiah($summary['total_pendapatan'] ?? 0); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card border card-rounded h-100">
                            <div class="card-body">
                                <div class="text-muted small">Jumlah transaksi selesai</div>
                                <div class="h4 fw-bold mb-0"><?= (int) ($summary['jumlah_transaksi'] ?? 0); ?></div>
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
