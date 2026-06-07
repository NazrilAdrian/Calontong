<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../includes/module4_helpers.php';
require_role(['owner', 'admin']);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Restock tidak valid.');
    redirect('pages/restock/index.php');
}

$purchase = module4_fetch_purchase_with_details($conn, $id);
if (!$purchase) {
    set_flash('danger', 'Data restock tidak ditemukan.');
    redirect('pages/restock/index.php');
}

$supplierOptions = module4_fetch_supplier_options($conn);
$productOptions = module4_fetch_product_options($conn);
$productMap = [];

foreach ($productOptions as $product) {
    $productMap[(int) $product['id_produk']] = $product;
}

$errors = [];
$data = [
    'id_supplier' => (string) $purchase['id_supplier'],
    'keterangan' => $purchase['keterangan'] ?? '',
    'id_produk' => [],
    'jumlah' => [],
    'harga_beli' => [],
];

$cartItems = module4_enrich_cart($purchase['details'], $productMap);
$cartTotal = module4_cart_total($cartItems);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['id_supplier'] = trim($_POST['id_supplier'] ?? '');
    $data['keterangan'] = trim($_POST['keterangan'] ?? '');
    $data['id_produk'] = $_POST['id_produk'] ?? [];
    $data['jumlah'] = $_POST['jumlah'] ?? [];
    $data['harga_beli'] = $_POST['harga_beli'] ?? [];

    $supplierId = (int) $data['id_supplier'];
    if ($supplierId <= 0) {
        $errors[] = 'Pilih supplier terlebih dahulu.';
    } else {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM supplier WHERE id_supplier = ?');
        $stmt->bind_param('i', $supplierId);
        $stmt->execute();
        if ((int) ($stmt->get_result()->fetch_row()[0] ?? 0) === 0) {
            $errors[] = 'Supplier yang dipilih tidak valid.';
        }
    }

    $cartExtract = module4_extract_cart($_POST);
    $errors = array_merge($errors, $cartExtract['errors']);
    $cartItems = module4_enrich_cart($cartExtract['items'], $productMap);

    if (count($cartItems) !== count($cartExtract['items'])) {
        $errors[] = 'Salah satu produk yang dipilih tidak ditemukan.';
    }

    if (!$cartItems) {
        $errors[] = 'Minimal pilih satu produk untuk restock.';
    }

    $cartTotal = module4_cart_total($cartItems);

    if (!$errors) {
        $oldItems = module4_enrich_cart($purchase['details'], $productMap);
        $total = $cartTotal;
        $userId = (int) $_SESSION['id_user'];

        $conn->begin_transaction();
        try {
            module4_sync_stok($conn, $oldItems, -1);

            $stmt = $conn->prepare('
                UPDATE pembelian
                SET id_supplier = ?, id_user = ?, total_harga = ?, keterangan = ?
                WHERE id_pembelian = ?
            ');
            $notes = $data['keterangan'];
            $stmt->bind_param('iidsi', $supplierId, $userId, $total, $notes, $id);
            $stmt->execute();

            $stmt = $conn->prepare('DELETE FROM detail_pembelian WHERE id_pembelian = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $stmtDetail = $conn->prepare('
                INSERT INTO detail_pembelian (id_pembelian, id_produk, jumlah, harga_beli, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ');

            foreach ($cartItems as $item) {
                $detailProductId = (int) $item['id_produk'];
                $detailQuantity = (int) $item['jumlah'];
                $detailPrice = (float) $item['harga_beli'];
                $subtotal = (float) $item['subtotal'];
                $stmtDetail->bind_param('iiidd', $id, $detailProductId, $detailQuantity, $detailPrice, $subtotal);
                $stmtDetail->execute();
            }

            module4_sync_stok($conn, $cartItems, 1);
            $conn->commit();

            set_flash('success', 'Restock berhasil diperbarui dan stok sudah disesuaikan.');
            redirect('pages/restock/index.php');
        } catch (Throwable $e) {
            $conn->rollback();
            $errors[] = 'Gagal memperbarui restock. Silakan coba lagi.';
            $cartItems = module4_enrich_cart($purchase['details'], $productMap);
            $cartTotal = module4_cart_total($cartItems);
        }
    }
}

render_page_start('Edit Restock', 'supplier', ['assets/css/supplier-restock.css']);
?>
<section class="form-section module4-wrap" data-restock-page>
    <h1 class="form-title">Edit Restock</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e($errors[0]); ?></div>
    <?php endif; ?>

    <form class="module-form" method="post" id="restockForm" novalidate>
        <input type="hidden" name="id" value="<?= e($id); ?>">

        <label class="form-label" for="id_supplier">Pilih Agen/ Supplier</label>
        <div class="select-icon select-chevron mb-3">
            <i class="bi bi-truck"></i>
            <select class="form-select" id="id_supplier" name="id_supplier">
                <option value="">Pilih Supplier</option>
                <?php foreach ($supplierOptions as $supplier): ?>
                    <option value="<?= e($supplier['id_supplier']); ?>" <?= (string) $data['id_supplier'] === (string) $supplier['id_supplier'] ? 'selected' : ''; ?>>
                        <?= e($supplier['nama_supplier']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <i class="bi bi-chevron-down select-chevron-icon"></i>
        </div>

        <div class="cart-shell mb-3">
            <h2 class="cart-title">Pilih Produk</h2>
            <div class="product-chooser">
                <div class="product-search-row">
                    <label class="form-label" for="productSearch">Cari Produk</label>
                    <div class="input-icon">
                        <i class="bi bi-search"></i>
                        <input class="form-control" type="search" id="productSearch" data-product-search placeholder="Cari nama produk">
                    </div>
                </div>
                <div class="product-results" data-product-results></div>
                <div class="product-results-empty" data-product-results-empty>Produk tidak ditemukan.</div>
            </div>

            <div id="cartItems" class="cart-grid" data-cart-items>
                <?php if ($cartItems): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item" data-product-id="<?= e($item['id_produk']); ?>">
                            <input type="hidden" name="id_produk[]" value="<?= e($item['id_produk']); ?>" data-id-input>
                            <input type="hidden" name="jumlah[]" value="<?= e($item['jumlah']); ?>" data-qty-input>
                            <input type="hidden" name="harga_beli[]" value="<?= e($item['harga_beli']); ?>" data-price-input>
                            <div class="cart-item-head">
                                <div>
                                    <strong><?= e($item['nama_produk']); ?></strong>
                                    <div class="cart-item-meta"><?= e($item['kode_produk'] ?: '-'); ?><?= $item['satuan'] ? ' · ' . e($item['satuan']) : ''; ?></div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" type="button" data-remove-item aria-label="Hapus item">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="cart-total-row">
                                <span class="cart-stepper">
                                    <button class="btn btn-outline-secondary" type="button" data-decrease>-</button>
                                    <strong data-qty-display><?= e($item['jumlah']); ?></strong>
                                    <button class="btn btn-outline-secondary" type="button" data-increase>+</button>
                                </span>
                                <strong data-subtotal-text><?= e(format_rupiah($item['subtotal'])); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted helper-text" id="emptyCartMessage" data-cart-empty>Belum ada item dipilih.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="cart-summary mb-3">
            <div class="cart-total-row">
                <span>Subtotal</span>
                <strong id="cartTotalText" data-cart-total><?= e(format_rupiah($cartTotal)); ?></strong>
            </div>
        </div>

        <div class="cart-note mb-4">
            <label class="form-label" for="keterangan">Catatan (opsional)</label>
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Catatan tambahan restock"><?= e($data['keterangan']); ?></textarea>
        </div>

        <div class="form-actions">
            <a class="btn btn-outline-secondary" href="<?= e(base_url('pages/restock/index.php')); ?>">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan & update stok</button>
        </div>
    </form>
</section>

<script>
    window.restockProducts = <?= json_encode($productMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>
<script src="<?= e(base_url('assets/js/module4.js')); ?>"></script>
<?php render_page_end(); ?>
