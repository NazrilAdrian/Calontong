<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../includes/module4_helpers.php';
require_role(['owner', 'admin']);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Restock tidak valid.');
    redirect('pages/restock/index.php');
}

$purchase = module4_fetch_purchase_with_details($conn, $id);
if (!$purchase) {
    set_flash('danger', 'Data restock tidak ditemukan.');
    redirect('pages/restock/index.php');
}

render_page_start('Detail Restock', 'supplier', ['assets/css/produk.css', 'assets/css/module4.css']);
?>
<section class="form-section module4-wrap">
    <h1 class="form-title">Detail Restock</h1>

    <div class="module-form">
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Kode Pembelian</label>
                <div class="form-control"><?= e($purchase['kode_pembelian']); ?></div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Tanggal</label>
                <div class="form-control"><?= e(date('d/m/Y H:i', strtotime($purchase['created_at']))); ?></div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Supplier</label>
                <div class="form-control"><?= e($purchase['nama_supplier']); ?></div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Pencatat</label>
                <div class="form-control"><?= e($purchase['nama_lengkap']); ?></div>
            </div>
        </div>

        <div class="table-shell mb-3">
            <table class="table product-table restock-table align-middle">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Beli</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchase['details'] as $detail): ?>
                        <tr>
                            <td>
                                <strong><?= e($detail['nama_produk']); ?></strong>
                                <?php if (!empty($detail['kode_produk'])): ?>
                                    <small><?= e($detail['kode_produk']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= e($detail['jumlah']); ?> <?= e($detail['satuan'] ?: ''); ?></td>
                            <td><?= e(format_rupiah($detail['harga_beli'])); ?></td>
                            <td><?= e(format_rupiah($detail['subtotal'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="cart-total-row mb-3">
            <span>Total</span>
            <strong><?= e(format_rupiah($purchase['total_harga'])); ?></strong>
        </div>

        <?php if (!empty($purchase['keterangan'])): ?>
            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <div class="form-control" style="min-height: 90px; white-space: pre-wrap; height: auto;"><?= e($purchase['keterangan']); ?></div>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <a class="btn btn-outline-secondary" href="<?= e(base_url('pages/restock/index.php')); ?>">Kembali</a>
            <a class="btn btn-primary" href="<?= e(base_url('pages/restock/edit.php?id=' . $purchase['id_pembelian'])); ?>">Edit</a>
        </div>
    </div>
</section>
<?php render_page_end(); ?>
