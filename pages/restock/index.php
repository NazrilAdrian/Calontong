<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../includes/module4_helpers.php';
require_role(['owner', 'admin']);

$suppliers = module4_fetch_suppliers($conn);

$fromDate = trim($_GET['dari_tanggal'] ?? '');
$toDate = trim($_GET['sampai_tanggal'] ?? '');

$purchaseSql = '
    SELECT p.*, s.nama_supplier, u.nama_lengkap
    FROM pembelian p
    JOIN supplier s ON s.id_supplier = p.id_supplier
    JOIN users u ON u.id_user = p.id_user
';
$purchaseParams = [];
$purchaseTypes = '';

if ($fromDate !== '' && $toDate !== '') {
    $purchaseSql .= ' WHERE DATE(p.created_at) BETWEEN ? AND ?';
    $purchaseParams = [$fromDate, $toDate];
    $purchaseTypes = 'ss';
} elseif ($fromDate !== '') {
    $purchaseSql .= ' WHERE DATE(p.created_at) >= ?';
    $purchaseParams = [$fromDate];
    $purchaseTypes = 's';
} elseif ($toDate !== '') {
    $purchaseSql .= ' WHERE DATE(p.created_at) <= ?';
    $purchaseParams = [$toDate];
    $purchaseTypes = 's';
}

$purchaseSql .= ' ORDER BY p.created_at DESC';
$purchaseStmt = $conn->prepare($purchaseSql);

if ($purchaseParams) {
    if (count($purchaseParams) === 2) {
        $purchaseStmt->bind_param('ss', $purchaseParams[0], $purchaseParams[1]);
    } else {
        $purchaseStmt->bind_param('s', $purchaseParams[0]);
    }
}

$purchaseStmt->execute();
$purchases = $purchaseStmt->get_result();

render_page_start('Supplier & Restock', 'supplier', ['assets/css/produk.css', 'assets/css/module4.css']);
?>
<section class="module-section module4-wrap" id="daftar-supplier">
    <div class="module4-toolbar">
        <h1 class="module-title mb-0">Daftar Supplier</h1>
        <a class="btn btn-primary btn-sm" href="<?= e(base_url('pages/supplier/tambah.php')); ?>">
            <i class="bi bi-plus-lg"></i> Tambah Supplier
        </a>
    </div>

    <div class="table-shell">
        <table class="table product-table supplier-table align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Agen</th>
                    <th>Kontak Person</th>
                    <th>Telp</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($suppliers->num_rows === 0): ?>
                    <tr>
                        <td colspan="6" class="empty-row">Belum ada supplier terdaftar.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= e($supplier['nama_supplier']); ?></strong></td>
                            <td><?= e($supplier['nama_kontak'] ?: '-'); ?></td>
                            <td><?= e($supplier['no_telepon'] ?: '-'); ?></td>
                            <td><?= e($supplier['alamat'] ?: '-'); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('pages/supplier/edit.php?id=' . $supplier['id_supplier'])); ?>" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#hapusSupplier<?= e($supplier['id_supplier']); ?>" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <div class="modal fade" id="hapusSupplier<?= e($supplier['id_supplier']); ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h2 class="modal-title fs-5">Hapus Supplier</h2>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                Yakin ingin menghapus supplier "<?= e($supplier['nama_supplier']); ?>"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form method="post" action="<?= e(base_url('pages/supplier/hapus.php')); ?>">
                                                    <input type="hidden" name="id" value="<?= e($supplier['id_supplier']); ?>">
                                                    <button class="btn btn-danger" type="submit">Ya, Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="module-section module4-wrap" id="riwayat-restock">
    <h1 class="module-title">Riwayat Restock</h1>

    <form class="restock-filter" method="get">
        <h2>Filter Tanggal</h2>
        <div class="filter-grid">
            <div>
                <label class="form-label mb-1" for="dari_tanggal">Dari tanggal</label>
                <input class="form-control" type="date" id="dari_tanggal" name="dari_tanggal" value="<?= e($fromDate); ?>">
            </div>
            <div>
                <label class="form-label mb-1" for="sampai_tanggal">Sampai tanggal</label>
                <input class="form-control" type="date" id="sampai_tanggal" name="sampai_tanggal" value="<?= e($toDate); ?>">
            </div>
            <button class="btn btn-primary" type="submit">Tampilkan</button>
        </div>
    </form>

    <div class="table-shell">
        <table class="table product-table restock-table align-middle">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tgl</th>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Pencatat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($purchases->num_rows === 0): ?>
                    <tr>
                        <td colspan="6" class="empty-row">Belum ada data restock.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php while ($purchase = $purchases->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <strong><?= e(date('d/m/Y', strtotime($purchase['created_at']))); ?></strong>
                                <small><?= e(date('H:i', strtotime($purchase['created_at']))); ?></small>
                            </td>
                            <td><?= e($purchase['nama_supplier']); ?></td>
                            <td><?= e(format_rupiah($purchase['total_harga'])); ?></td>
                            <td><?= e($purchase['nama_lengkap']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('pages/restock/detail.php?id=' . $purchase['id_pembelian'])); ?>" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(base_url('pages/restock/edit.php?id=' . $purchase['id_pembelian'])); ?>" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#hapusRestock<?= e($purchase['id_pembelian']); ?>" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <div class="modal fade" id="hapusRestock<?= e($purchase['id_pembelian']); ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h2 class="modal-title fs-5">Hapus Restock</h2>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                Menghapus restock ini akan mengurangi stok produk terkait. Lanjutkan?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form method="post" action="<?= e(base_url('pages/restock/hapus.php')); ?>">
                                                    <input type="hidden" name="id" value="<?= e($purchase['id_pembelian']); ?>">
                                                    <button class="btn btn-danger" type="submit">Ya, Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-3">
        <a class="btn btn-primary add-button" href="<?= e(base_url('pages/restock/tambah.php')); ?>">
            <i class="bi bi-plus-lg"></i> Catat Restock
        </a>
    </div>
</section>
<?php render_page_end(); ?>
