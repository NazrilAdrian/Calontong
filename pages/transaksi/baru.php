<?php
require_once __DIR__ . '/_helpers.php';

$conn = calontong_db();
$search = trim($_GET['q'] ?? '');
$uangBayarInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (!$conn) {
        flash('danger', 'Koneksi database belum tersedia. Pastikan config.php sudah dibuat dan menghasilkan variabel $conn.');
        redirect_to('baru.php');
    }

    if ($action === 'add') {
        $idProduk = (int) ($_POST['id_produk'] ?? 0);
        $jumlah = max(1, (int) ($_POST['jumlah'] ?? 1));
        $produk = fetch_one(
            'SELECT id_produk, nama_produk, harga_jual, stok, satuan FROM produk WHERE id_produk = ?',
            'i',
            [$idProduk]
        );

        if (!$produk) {
            flash('danger', 'Produk tidak ditemukan.');
        } elseif ((int) $produk['stok'] < $jumlah) {
            flash('danger', 'Stok ' . $produk['nama_produk'] . ' tidak cukup.');
        } else {
            $cart = cart_items();
            $existingQty = isset($cart[$idProduk]) ? (int) $cart[$idProduk]['jumlah'] : 0;
            $newQty = $existingQty + $jumlah;

            if ($newQty > (int) $produk['stok']) {
                flash('danger', 'Jumlah di keranjang melebihi stok ' . $produk['nama_produk'] . '.');
            } else {
                $cart[$idProduk] = [
                    'id_produk' => (int) $produk['id_produk'],
                    'nama_produk' => $produk['nama_produk'],
                    'harga_satuan' => (float) $produk['harga_jual'],
                    'stok' => (int) $produk['stok'],
                    'satuan' => $produk['satuan'] ?? '',
                    'jumlah' => $newQty,
                    'subtotal' => $newQty * (float) $produk['harga_jual'],
                ];
                set_cart_items($cart);
                flash('success', $produk['nama_produk'] . ' ditambahkan ke keranjang.');
            }
        }

        redirect_to('baru.php' . ($search !== '' ? '?q=' . urlencode($search) : ''));
    }

    if ($action === 'update_cart') {
        $cart = cart_items();
        $jumlahItems = $_POST['jumlah'] ?? [];

        foreach ($jumlahItems as $idProduk => $jumlah) {
            $idProduk = (int) $idProduk;
            $jumlah = (int) $jumlah;

            if (!isset($cart[$idProduk])) {
                continue;
            }

            if ($jumlah <= 0) {
                unset($cart[$idProduk]);
                continue;
            }

            $produk = fetch_one(
                'SELECT id_produk, nama_produk, harga_jual, stok, satuan FROM produk WHERE id_produk = ?',
                'i',
                [$idProduk]
            );

            if (!$produk) {
                unset($cart[$idProduk]);
                flash('warning', 'Ada produk yang sudah tidak ditemukan dan dihapus dari keranjang.');
                continue;
            }

            if ($jumlah > (int) $produk['stok']) {
                $jumlah = (int) $produk['stok'];
                flash('warning', 'Jumlah ' . $produk['nama_produk'] . ' disesuaikan dengan stok tersedia.');
            }

            $cart[$idProduk] = [
                'id_produk' => (int) $produk['id_produk'],
                'nama_produk' => $produk['nama_produk'],
                'harga_satuan' => (float) $produk['harga_jual'],
                'stok' => (int) $produk['stok'],
                'satuan' => $produk['satuan'] ?? '',
                'jumlah' => $jumlah,
                'subtotal' => $jumlah * (float) $produk['harga_jual'],
            ];
        }

        set_cart_items($cart);
        flash('success', 'Keranjang diperbarui.');
        redirect_to('baru.php');
    }

    if ($action === 'remove') {
        $idProduk = (int) ($_POST['id_produk'] ?? 0);
        $cart = cart_items();
        unset($cart[$idProduk]);
        set_cart_items($cart);
        flash('success', 'Produk dihapus dari keranjang.');
        redirect_to('baru.php');
    }

    if ($action === 'clear') {
        unset($_SESSION['cart']);
        flash('success', 'Keranjang dikosongkan.');
        redirect_to('baru.php');
    }

    if ($action === 'checkout') {
        $cart = cart_items();
        $uangBayar = (float) ($_POST['uang_bayar'] ?? 0);
        $uangBayarInput = (string) ($_POST['uang_bayar'] ?? '');

        if (empty($cart)) {
            flash('danger', 'Keranjang masih kosong.');
            redirect_to('baru.php');
        }

        if (current_user_id() <= 0) {
            flash('danger', 'Sesi pengguna belum tersedia. Silakan login sebelum membuat transaksi.');
            redirect_to('baru.php');
        }

        $totalKeranjang = cart_total();
        if ($uangBayar < $totalKeranjang) {
            flash('danger', 'Uang bayar kurang dari total belanja.');
            redirect_to('baru.php');
        }

        try {
            $conn->begin_transaction();

            $validatedItems = [];
            $totalHarga = 0;

            foreach ($cart as $item) {
                $idProduk = (int) $item['id_produk'];
                $jumlah = (int) $item['jumlah'];
                $stmt = $conn->prepare('SELECT id_produk, nama_produk, harga_jual, stok FROM produk WHERE id_produk = ? FOR UPDATE');
                $stmt->bind_param('i', $idProduk);
                $stmt->execute();
                $produk = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$produk) {
                    throw new Exception('Produk dalam keranjang tidak ditemukan.');
                }

                if ($jumlah <= 0 || $jumlah > (int) $produk['stok']) {
                    throw new Exception('Stok ' . $produk['nama_produk'] . ' tidak cukup.');
                }

                $hargaSatuan = (float) $produk['harga_jual'];
                $subtotal = $jumlah * $hargaSatuan;
                $totalHarga += $subtotal;
                $validatedItems[] = [
                    'id_produk' => (int) $produk['id_produk'],
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }

            if ($uangBayar < $totalHarga) {
                throw new Exception('Uang bayar kurang dari total belanja.');
            }

            $kodeTransaksi = transaction_code();
            $kembalian = $uangBayar - $totalHarga;
            $idUser = current_user_id();
            $stmt = $conn->prepare(
                'INSERT INTO transaksi (id_user, kode_transaksi, total_harga, uang_bayar, kembalian, status) VALUES (?, ?, ?, ?, ?, "selesai")'
            );
            $stmt->bind_param('isddd', $idUser, $kodeTransaksi, $totalHarga, $uangBayar, $kembalian);
            $stmt->execute();
            $idTransaksi = $conn->insert_id;
            $stmt->close();

            $stmtDetail = $conn->prepare(
                'INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)'
            );
            $stmtStok = $conn->prepare('UPDATE produk SET stok = stok - ? WHERE id_produk = ?');

            foreach ($validatedItems as $item) {
                $stmtDetail->bind_param(
                    'iiidd',
                    $idTransaksi,
                    $item['id_produk'],
                    $item['jumlah'],
                    $item['harga_satuan'],
                    $item['subtotal']
                );
                $stmtDetail->execute();

                $stmtStok->bind_param('ii', $item['jumlah'], $item['id_produk']);
                $stmtStok->execute();
            }

            $stmtDetail->close();
            $stmtStok->close();
            $conn->commit();

            unset($_SESSION['cart']);
            flash('success', 'Transaksi ' . $kodeTransaksi . ' berhasil disimpan.');
            redirect_to('detail.php?id=' . $idTransaksi);
        } catch (Exception $e) {
            $conn->rollback();
            flash('danger', $e->getMessage());
            redirect_to('baru.php');
        }
    }
}

$products = [];
if ($conn) {
    if ($search !== '') {
        $products = fetch_all(
            'SELECT id_produk, kode_produk, nama_produk, harga_jual, stok, satuan FROM produk WHERE nama_produk LIKE ? OR kode_produk LIKE ? ORDER BY nama_produk ASC LIMIT 30',
            'ss',
            ['%' . $search . '%', '%' . $search . '%']
        );
    } else {
        $products = fetch_all(
            'SELECT id_produk, kode_produk, nama_produk, harga_jual, stok, satuan FROM produk ORDER BY nama_produk ASC LIMIT 30'
        );
    }
}

$cart = cart_items();
$total = cart_total();
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ca'lontong - Transaksi Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .qty-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .qty-input {
            width: 80px;
        }
    </style>
</head>
<body class="bg-white">
    <main class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 col-xxl-9">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-4">
                    <h1 class="h5 fw-bold mb-0">Buat transaksi</h1>
                    <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">Riwayat transaksi</a>
                </div>

                <?php if (!$conn): ?>
                    <div class="alert alert-warning">
                        Koneksi database belum tersedia. Buat <code>config.php</code> dengan variabel <code>$conn</code> agar transaksi bisa berjalan.
                    </div>
                <?php endif; ?>

                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= h($message['type']); ?> alert-dismissible fade show" role="alert">
                        <?= h($message['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endforeach; ?>

                <div class="row g-4">
                    <div class="col-12 col-xl-7">
                        <div class="card border rounded-4 mb-4">
                            <div class="card-body">
                                <div class="fw-semibold mb-2">Cari produk</div>
                                <form class="input-group mb-3" method="get">
                                    <span class="input-group-text bg-white">Cari</span>
                                    <input type="text" class="form-control" name="q" value="<?= h($search); ?>" placeholder="Ketik nama atau kode produk">
                                    <button type="submit" class="btn btn-success">Cari</button>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th class="text-end">Harga</th>
                                                <th class="text-center">Stok</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($products)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <?= $conn ? 'Belum ada produk untuk ditampilkan.' : 'Produk akan tampil setelah koneksi database tersedia.'; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <?php foreach ($products as $product): ?>
                                                <?php $formId = 'add-product-' . (int) $product['id_produk']; ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold"><?= h($product['nama_produk']); ?></div>
                                                        <div class="small text-muted"><?= h($product['kode_produk'] ?: '-'); ?></div>
                                                    </td>
                                                    <td class="text-end"><?= rupiah($product['harga_jual']); ?></td>
                                                    <td class="text-center">
                                                        <?= (int) $product['stok']; ?> <?= h($product['satuan'] ?? ''); ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" form="<?= h($formId); ?>" class="form-control form-control-sm qty-input mx-auto" name="jumlah" value="1" min="1" max="<?= (int) $product['stok']; ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="post" id="<?= h($formId); ?>">
                                                            <input type="hidden" name="action" value="add">
                                                            <input type="hidden" name="id_produk" value="<?= (int) $product['id_produk']; ?>">
                                                            <input type="hidden" name="q" value="<?= h($search); ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success" <?= ((int) $product['stok'] <= 0) ? 'disabled' : ''; ?>>Tambah</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-5">
                        <div class="card border rounded-4 mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="fw-semibold">Keranjang</div>
                                    <?php if (!empty($cart)): ?>
                                        <form method="post">
                                            <input type="hidden" name="action" value="clear">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Kosongkan</button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <form method="post">
                                    <input type="hidden" name="action" value="update_cart">
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-3">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Produk</th>
                                                    <th class="text-end">Harga</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Subtotal</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($cart)): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted py-4">Keranjang masih kosong.</td>
                                                    </tr>
                                                <?php endif; ?>

                                                <?php foreach ($cart as $item): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold"><?= h($item['nama_produk']); ?></div>
                                                            <div class="small text-muted">Stok: <?= (int) $item['stok']; ?> <?= h($item['satuan'] ?? ''); ?></div>
                                                        </td>
                                                        <td class="text-end"><?= rupiah($item['harga_satuan']); ?></td>
                                                        <td class="text-center">
                                                            <input type="number" class="form-control form-control-sm qty-input mx-auto" name="jumlah[<?= (int) $item['id_produk']; ?>]" value="<?= (int) $item['jumlah']; ?>" min="0" max="<?= (int) $item['stok']; ?>">
                                                        </td>
                                                        <td class="text-end"><?= rupiah($item['subtotal']); ?></td>
                                                        <td class="text-end">
                                                            <button type="submit" form="remove-<?= (int) $item['id_produk']; ?>" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php if (!empty($cart)): ?>
                                        <button type="submit" class="btn btn-outline-secondary rounded-pill w-100">Perbarui keranjang</button>
                                    <?php endif; ?>
                                </form>

                                <?php foreach ($cart as $item): ?>
                                    <form method="post" id="remove-<?= (int) $item['id_produk']; ?>" class="d-none">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="id_produk" value="<?= (int) $item['id_produk']; ?>">
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="card border rounded-4">
                            <div class="card-body">
                                <form method="post">
                                    <input type="hidden" name="action" value="checkout">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">Total</span>
                                        <span class="fw-semibold" id="totalText" data-total="<?= (float) $total; ?>"><?= rupiah($total); ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="uangBayar" class="form-label">Uang pembayaran</label>
                                        <input type="number" class="form-control" id="uangBayar" name="uang_bayar" min="0" step="100" value="<?= h($uangBayarInput); ?>" placeholder="Masukkan nominal" <?= empty($cart) ? 'disabled' : ''; ?>>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-semibold">Kembalian</span>
                                        <span class="fw-semibold" id="kembalianText"><?= rupiah(0); ?></span>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success rounded-pill" <?= empty($cart) ? 'disabled' : ''; ?>>Selesaikan transaksi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uangBayar = document.getElementById('uangBayar');
        const totalText = document.getElementById('totalText');
        const kembalianText = document.getElementById('kembalianText');

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }

        function updateKembalian() {
            if (!uangBayar || !totalText || !kembalianText) {
                return;
            }

            const total = Number(totalText.dataset.total || 0);
            const bayar = Number(uangBayar.value || 0);
            kembalianText.textContent = formatRupiah(Math.max(0, bayar - total));
        }

        if (uangBayar) {
            uangBayar.addEventListener('input', updateKembalian);
            updateKembalian();
        }
    </script>
</body>
</html>
