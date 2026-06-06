<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$canManage = is_owner_or_admin();
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $like = '%' . $keyword . '%';
    $stmt = $conn->prepare("
        SELECT p.*, k.nama_kategori
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.nama_produk LIKE ?
        ORDER BY p.nama_produk ASC
    ");
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $produkResult = $stmt->get_result();
} else {
    $produkResult = $conn->query("
        SELECT p.*, k.nama_kategori
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id_kategori
        ORDER BY p.nama_produk ASC
    ");
}

$kategoriResult = null;
if ($canManage) {
    $kategoriResult = $conn->query('SELECT * FROM kategori ORDER BY nama_kategori ASC');
}

render_page_start('Produk & Kategori', 'produk', ['assets/css/produk.css']);
?>
<section class="module-section">
    <h1 class="module-title">Daftar Produk</h1>

    <form class="search-row" method="get">
        <div class="input-icon flex-grow-1">
            <i class="bi bi-search"></i>
            <input class="form-control" type="search" name="q" value="<?= e($keyword); ?>" placeholder="Cari produk">
        </div>
        <button class="btn btn-primary search-button" type="submit">Cari</button>
    </form>

    <div class="table-shell">
        <table class="table product-table align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($produkResult->num_rows === 0): ?>
                    <tr>
                        <td colspan="6" class="empty-row"><?= $keyword !== '' ? 'Produk tidak ditemukan.' : 'Belum ada produk terdaftar.'; ?></td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php while ($produk = $produkResult->fetch_assoc()): ?>
                        <?php $isCritical = (int) $produk['stok'] <= (int) $produk['stok_minimum']; ?>
                        <tr class="<?= $isCritical ? 'critical-row' : ''; ?>">
                            <td><?= $no++; ?></td>
                            <td>
                                <strong><?= e($produk['nama_produk']); ?></strong>
                                <?php if ($produk['kode_produk']): ?>
                                    <small><?= e($produk['kode_produk']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= e($produk['nama_kategori']); ?></td>
                            <td><?= e(format_rupiah($produk['harga_jual'])); ?></td>
                            <td>
                                <?= e($produk['stok']); ?> <?= e($produk['satuan']); ?>
                                <?php if ($isCritical): ?>
                                    <span class="badge text-bg-danger">Kritis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($canManage): ?>
                                    <div class="action-buttons">
                                        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('pages/produk/edit.php?id=' . $produk['id_produk'])); ?>" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#hapusProduk<?= e($produk['id_produk']); ?>" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <div class="modal fade" id="hapusProduk<?= e($produk['id_produk']); ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h2 class="modal-title fs-5">Hapus Produk</h2>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Yakin ingin menghapus produk "<?= e($produk['nama_produk']); ?>"?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form method="post" action="<?= e(base_url('pages/produk/hapus.php')); ?>">
                                                        <input type="hidden" name="id" value="<?= e($produk['id_produk']); ?>">
                                                        <button class="btn btn-danger" type="submit">Ya, Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($canManage): ?>
        <a class="btn btn-primary add-button" href="<?= e(base_url('pages/produk/tambah.php')); ?>">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
    <?php endif; ?>
</section>

<?php if ($canManage): ?>
    <section class="module-section category-section" id="kategori">
        <h1 class="module-title">Daftar Kategori</h1>
        <div class="table-shell">
            <table class="table product-table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($kategoriResult->num_rows === 0): ?>
                        <tr><td colspan="4" class="empty-row">Belum ada kategori terdaftar.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php while ($kategori = $kategoriResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><strong><?= e($kategori['nama_kategori']); ?></strong></td>
                                <td><?= e($kategori['deskripsi']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('pages/kategori/edit.php?id=' . $kategori['id_kategori'])); ?>" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#hapusKategori<?= e($kategori['id_kategori']); ?>" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <div class="modal fade" id="hapusKategori<?= e($kategori['id_kategori']); ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h2 class="modal-title fs-5">Hapus Kategori</h2>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Yakin ingin menghapus kategori "<?= e($kategori['nama_kategori']); ?>"?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form method="post" action="<?= e(base_url('pages/kategori/hapus.php')); ?>">
                                                        <input type="hidden" name="id" value="<?= e($kategori['id_kategori']); ?>">
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

        <a class="btn btn-primary add-button" href="<?= e(base_url('pages/kategori/tambah.php')); ?>">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </a>
    </section>
<?php endif; ?>
<?php render_page_end(); ?>
