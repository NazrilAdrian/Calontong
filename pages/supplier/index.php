<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../includes/module4_helpers.php';
require_role(['owner', 'admin']);

$suppliers = module4_fetch_suppliers($conn);

render_page_start('Daftar Supplier', 'supplier', ['assets/css/produk.css', 'assets/css/module4.css']);
?>
<section class="module-section module4-wrap">
    <div class="module4-toolbar">
        <h1 class="module-title mb-0">Daftar Supplier</h1>
        <a class="btn btn-outline-primary btn-sm" href="<?= e(base_url('pages/restock/index.php')); ?>">
            <i class="bi bi-clock-history"></i> Riwayat Restock
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

    <a class="btn btn-primary add-button" href="<?= e(base_url('pages/supplier/tambah.php')); ?>">
        <i class="bi bi-plus-lg"></i> Tambah Supplier
    </a>
</section>
<?php render_page_end(); ?>
