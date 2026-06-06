<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../includes/module4_helpers.php';
require_role(['owner', 'admin']);

$supplierOptions = module4_fetch_supplier_options($conn);
$productOptions = module4_fetch_product_options($conn);
$productMap = [];

foreach ($productOptions as $product) {
    $productMap[(int) $product['id_produk']] = $product;
}

$errors = [];
$data = [
    'id_supplier' => '',
    'keterangan' => '',
    'id_produk' => [],
    'jumlah' => [],
    'harga_beli' => [],
];
$cartItems = [];
$cartTotal = 0;

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
        $purchaseCode = module4_generate_code('RST');
        $total = $cartTotal;

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare('
                INSERT INTO pembelian (id_supplier, id_user, kode_pembelian, total_harga, keterangan)
                VALUES (?, ?, ?, ?, ?)
            ');
            $userId = (int) $_SESSION['id_user'];
            $stmt->bind_param('iisds', $supplierId, $userId, $purchaseCode, $total, $data['keterangan']);
            $stmt->execute();
            $idPembelian = $conn->insert_id;

            $stmtDetail = $conn->prepare('
                INSERT INTO detail_pembelian (id_pembelian, id_produk, jumlah, harga_beli, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ');

            foreach ($cartItems as $item) {
                $detailProductId = (int) $item['id_produk'];
                $detailQuantity = (int) $item['jumlah'];
                $detailPrice = (float) $item['harga_beli'];
                $subtotal = (float) $item['subtotal'];
                $stmtDetail->bind_param('iiidd', $idPembelian, $detailProductId, $detailQuantity, $detailPrice, $subtotal);
                $stmtDetail->execute();
            }

            module4_sync_stok($conn, $cartItems, 1);
            $conn->commit();

            set_flash('success', 'Restock berhasil dicatat dan stok sudah diperbarui.');
            redirect('pages/restock/index.php');
        } catch (Throwable $e) {
            $conn->rollback();
            $errors[] = 'Gagal menyimpan restock. Silakan coba lagi.';
        }
    }
}

render_page_start('Catat Restock', 'supplier', ['assets/css/produk.css', 'assets/css/module4.css']);
?>
<section class="form-section module4-wrap" data-restock-page>
    <h1 class="form-title">Catat Restock</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e($errors[0]); ?></div>
    <?php endif; ?>

    <form class="module-form" method="post" id="restockForm" novalidate>
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
