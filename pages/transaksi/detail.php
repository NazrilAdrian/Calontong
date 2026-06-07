<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/_helpers.php';

$conn = calontong_db();
$idTransaksi = (int) ($_GET['id'] ?? 0);
$transaction = null;
$details = [];

if ($conn && $idTransaksi > 0) {
    $whereKasir = '';
    $types = 'i';
    $params = [$idTransaksi];

    if (current_role() === 'kasir' && current_user_id() > 0) {
        $whereKasir = ' AND t.id_user = ?';
        $types .= 'i';
        $params[] = current_user_id();
    }

    $transaction = fetch_one(
        'SELECT t.*, u.nama_lengkap
         FROM transaksi t
         JOIN users u ON u.id_user = t.id_user
         WHERE t.id_transaksi = ?' . $whereKasir,
        $types,
        $params
    );

    if ($transaction) {
        $details = fetch_all(
            'SELECT dt.*, p.nama_produk, p.kode_produk, p.satuan
             FROM detail_transaksi dt
             JOIN produk p ON p.id_produk = dt.id_produk
             WHERE dt.id_transaksi = ?
             ORDER BY dt.id_detail ASC',
            'i',
            [$idTransaksi]
        );
    }
}

$messages = take_flash();
?>
<?php render_page_start('Detail Transaksi', 'transaksi'); ?>
<div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 col-xxl-9">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-4">
                    <h1 class="h5 fw-bold mb-0">Detail Transaksi</h1>
                    <a href="index.php" class="btn btn-outline-secondary btn-rounded px-4">Kembali</a>
                </div>

                <?php if (!$conn): ?>
                    <div class="alert alert-warning">
                        Koneksi database belum tersedia. Buat <code>config.php</code> dengan variabel <code>$conn</code>.
                    </div>
                <?php endif; ?>

                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= h($message['type']); ?> alert-dismissible fade show" role="alert">
                        <?= h($message['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endforeach; ?>

                <?php if (!$transaction): ?>
                    <div class="card border card-rounded">
                        <div class="card-body text-center text-muted py-5">
                            Transaksi tidak ditemukan atau tidak bisa diakses.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border card-rounded mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">No. Transaksi</div>
                                    <div class="fw-semibold"><?= h($transaction['kode_transaksi']); ?></div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Tanggal</div>
                                    <div class="fw-semibold"><?= h(date('d/m/Y H:i', strtotime($transaction['created_at']))); ?></div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Kasir</div>
                                    <div class="fw-semibold"><?= h($transaction['nama_lengkap']); ?></div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Status</div>
                                    <?php if ($transaction['status'] === 'selesai'): ?>
                                        <span class="badge text-bg-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Batal</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Uang bayar</div>
                                    <div class="fw-semibold"><?= rupiah($transaction['uang_bayar']); ?></div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Kembalian</div>
                                    <div class="fw-semibold"><?= rupiah($transaction['kembalian']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border card-rounded mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($details as $detail): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold"><?= h($detail['nama_produk']); ?></div>
                                                    <div class="small text-muted"><?= h($detail['kode_produk'] ?: '-'); ?></div>
                                                </td>
                                                <td class="text-center"><?= (int) $detail['jumlah']; ?> <?= h($detail['satuan'] ?? ''); ?></td>
                                                <td class="text-end"><?= rupiah($detail['harga_satuan']); ?></td>
                                                <td class="text-end"><?= rupiah($detail['subtotal']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total</th>
                                            <th class="text-end"><?= rupiah($transaction['total_harga']); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php if (is_manager_role() && $transaction['status'] === 'selesai'): ?>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="edit.php?id=<?= (int) $transaction['id_transaksi']; ?>" class="btn btn-outline-primary rounded-pill px-4">Edit Transaksi</a>    
                        <form method="post" action="batal.php" onsubmit="return confirm('PENTING: Batalkan transaksi ini dan kembalikan stok gudang secara otomatis?');">
                            <input type="hidden" name="id" value="<?= (int) $transaction['id_transaksi']; ?>">
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-4">Batalkan Transaksi</button>
                        </form>
                    </div>
                <?php endif; ?>
                
                <?php endif; ?> 
                </div>
            </div>
        </div>
</div>

<?php render_page_end(); ?>
