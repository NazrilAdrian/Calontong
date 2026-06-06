<?php
require_once __DIR__ . '/_helpers.php';

$conn = calontong_db();
$tanggalMulai = $_GET['tanggal_mulai'] ?? '';
$tanggalSelesai = $_GET['tanggal_selesai'] ?? '';
$where = [];
$types = '';
$params = [];

if ($tanggalMulai !== '') {
    $where[] = 'DATE(t.created_at) >= ?';
    $types .= 's';
    $params[] = $tanggalMulai;
}

if ($tanggalSelesai !== '') {
    $where[] = 'DATE(t.created_at) <= ?';
    $types .= 's';
    $params[] = $tanggalSelesai;
}

if (current_role() === 'kasir' && current_user_id() > 0) {
    $where[] = 't.id_user = ?';
    $types .= 'i';
    $params[] = current_user_id();
}

$sql = 'SELECT t.id_transaksi, t.kode_transaksi, t.total_harga, t.status, t.created_at, u.nama_lengkap
        FROM transaksi t
        JOIN users u ON u.id_user = t.id_user';

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY t.created_at DESC, t.id_transaksi DESC';

$transactions = $conn ? fetch_all($sql, $types, $params) : [];
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ca'lontong - Riwayat Transaksi</title>
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
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4">
                    <h1 class="h5 fw-bold mb-0">Riwayat Transaksi</h1>
                    
                    <!-- Tombol ini hanya akan muncul jika yang login adalah Owner/Admin -->
                    <?php if (is_manager_role()): ?>
                        <a href="../laporan/penjualan.php" class="btn btn-outline-dark rounded-pill px-4">
                            Lihat Laporan Penjualan
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (!$conn): ?>
                    <div class="alert alert-warning">
                        Koneksi database belum tersedia. Buat <code>config.php</code> dengan variabel <code>$conn</code> agar riwayat transaksi bisa tampil.
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
                        <div class="fw-semibold mb-3">Filter tanggal</div>
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
                                    <button type="submit" class="btn btn-success btn-rounded">Tampilkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border card-rounded mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Trx</th>
                                        <th>Tgl</th>
                                        <th>Kasir</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($transactions)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <?= $conn ? 'Belum ada transaksi untuk filter ini.' : 'Data transaksi akan tampil setelah koneksi database tersedia.'; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?= h($transaction['kode_transaksi']); ?></td>
                                            <td><?= h(date('d/m/Y H:i', strtotime($transaction['created_at']))); ?></td>
                                            <td><?= h($transaction['nama_lengkap']); ?></td>
                                            <td class="text-end"><?= rupiah($transaction['total_harga']); ?></td>
                                            <td>
                                                <?php if ($transaction['status'] === 'selesai'): ?>
                                                    <span class="badge text-bg-success">Selesai</span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-secondary">Batal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex flex-wrap justify-content-center gap-2">
                                                    <a href="detail.php?id=<?= (int) $transaction['id_transaksi']; ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                                    <?php if (is_manager_role() && $transaction['status'] === 'selesai'): ?>
                                                        <a href="edit.php?id=<?= (int) $transaction['id_transaksi']; ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                                                        <form method="post" action="batal.php" onsubmit="return confirm('Batalkan transaksi ini dan kembalikan stok?');">
                                                            <input type="hidden" name="id" value="<?= (int) $transaction['id_transaksi']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Batal</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-5 mb-5">
                    <a href="baru.php" class="btn btn-success px-4 py-2" style="border-radius: 8px; font-weight: 500;">
                    Transaksi baru
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
