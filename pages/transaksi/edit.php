<?php
require_once __DIR__ . '/_helpers.php';

if (!is_manager_role()) {
    flash('danger', 'Akses ditolak. Hanya Owner/Admin yang dapat mengedit transaksi.');
    redirect_to('index.php');
}

$conn = calontong_db();
$idTransaksi = (int) ($_GET['id'] ?? 0);

if (!$conn) {
    flash('danger', 'Koneksi database belum tersedia.');
    redirect_to('index.php');
}

// Proses form saat tombol simpan ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost = (int) ($_POST['id_transaksi'] ?? 0);
    $uangBayar = (float) ($_POST['uang_bayar'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');

    // Cek total_harga transaksi saat ini
    $trxInfo = fetch_one('SELECT total_harga FROM transaksi WHERE id_transaksi = ? AND status = "selesai"', 'i', [$idPost]);
    
    if ($trxInfo) {
        $totalHarga = (float) $trxInfo['total_harga'];
        
        // Validasi uang bayar tidak boleh kurang dari total harga
        if ($uangBayar < $totalHarga) {
            flash('danger', 'Koreksi gagal: Uang bayar (Rp ' . number_format($uangBayar, 0, ',', '.') . ') tidak boleh kurang dari Total Belanja (Rp ' . number_format($totalHarga, 0, ',', '.') . ').');
        } else {
            $kembalian = $uangBayar - $totalHarga;
            
            // Update data transaksi
            $update = execute_query(
                'UPDATE transaksi SET uang_bayar = ?, kembalian = ?, keterangan = ? WHERE id_transaksi = ?',
                'ddsi',
                [$uangBayar, $kembalian, $keterangan, $idPost]
            );

            if ($update) {
                flash('success', 'Data transaksi berhasil dikoreksi.');
                redirect_to('detail.php?id=' . $idPost);
            } else {
                flash('danger', 'Gagal memperbarui data transaksi.');
            }
        }
    } else {
        flash('danger', 'Transaksi tidak valid atau sudah dibatalkan.');
    }
}

// Ambil data transaksi untuk ditampilkan di form
$transaction = fetch_one('SELECT * FROM transaksi WHERE id_transaksi = ?', 'i', [$idTransaksi]);

if (!$transaction) {
    flash('danger', 'Transaksi tidak ditemukan.');
    redirect_to('index.php');
}

$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - Ca'lontong</title>
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
                
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-4">
                    <h1 class="h5 fw-bold mb-0">Edit Transaksi</h1>
                    <a href="detail.php?id=<?= $idTransaksi; ?>" class="btn btn-outline-secondary btn-rounded px-4 mt-3 mt-sm-0">Kembali</a>
                </div>

                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="alert alert-<?= h($msg['type']); ?> alert-dismissible fade show" role="alert">
                            <?= h($msg['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="card border card-rounded mb-4 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <input type="hidden" name="id_transaksi" value="<?= $idTransaksi; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold">Kode Transaksi</label>
                                    <input type="text" class="form-control bg-light" value="<?= h($transaction['kode_transaksi']); ?>" readonly disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold">Total Belanja</label>
                                    <input type="text" class="form-control bg-light fw-bold text-danger" value="<?= rupiah($transaction['total_harga']); ?>" readonly disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold">Koreksi Uang Bayar</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">Rp</span>
                                        <input type="number" name="uang_bayar" class="form-control" value="<?= (int) $transaction['uang_bayar']; ?>" required min="<?= (int) $transaction['total_harga']; ?>">
                                    </div>
                                    <div class="form-text">Ubah nominal ini jika kasir salah mengetik uang yang diterima.</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Keterangan / Alasan Edit</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Koreksi nominal pembayaran yang kurang nol..."><?= h($transaction['keterangan']); ?></textarea>
                            </div>

                            <hr class="text-muted mt-4 mb-4">

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success btn-rounded px-5 py-2 fw-medium">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>