<?php

function module4_decimal($value)
{
    $normalized = trim((string) $value);
    $normalized = str_replace(' ', '', $normalized);

    if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
        $normalized = str_replace('.', '', $normalized);
        $normalized = str_replace(',', '.', $normalized);
    } elseif (str_contains($normalized, ',')) {
        $normalized = str_replace(',', '.', $normalized);
    }

    return (float) $normalized;
}

function module4_int($value)
{
    return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
}

function module4_generate_code($prefix)
{
    return $prefix . '-' . date('YmdHis') . '-' . random_int(1000, 9999);
}

function module4_fetch_suppliers(mysqli $conn)
{
    return $conn->query('SELECT * FROM supplier ORDER BY nama_supplier ASC');
}

function module4_fetch_products(mysqli $conn)
{
    return $conn->query('
        SELECT id_produk, nama_produk, kode_produk, stok, harga_beli, satuan
        FROM produk
        ORDER BY nama_produk ASC
    ');
}

function module4_fetch_supplier_options(mysqli $conn)
{
    return $conn->query('SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier ASC')->fetch_all(MYSQLI_ASSOC);
}

function module4_fetch_product_options(mysqli $conn)
{
    return $conn->query('
        SELECT id_produk, nama_produk, kode_produk, stok, harga_beli, satuan
        FROM produk
        ORDER BY nama_produk ASC
    ')->fetch_all(MYSQLI_ASSOC);
}

function module4_fetch_user_name(mysqli $conn, $idUser)
{
    $stmt = $conn->prepare('SELECT nama_lengkap FROM users WHERE id_user = ? LIMIT 1');
    $stmt->bind_param('i', $idUser);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    return $row['nama_lengkap'] ?? '-';
}

function module4_fetch_purchase_with_details(mysqli $conn, $idPembelian)
{
    $stmt = $conn->prepare('
        SELECT p.*, s.nama_supplier, u.nama_lengkap
        FROM pembelian p
        JOIN supplier s ON s.id_supplier = p.id_supplier
        JOIN users u ON u.id_user = p.id_user
        WHERE p.id_pembelian = ?
        LIMIT 1
    ');
    $stmt->bind_param('i', $idPembelian);
    $stmt->execute();
    $purchase = $stmt->get_result()->fetch_assoc();

    if (!$purchase) {
        return null;
    }

    $stmt = $conn->prepare('
        SELECT dp.*, pr.nama_produk, pr.kode_produk, pr.satuan
        FROM detail_pembelian dp
        JOIN produk pr ON pr.id_produk = dp.id_produk
        WHERE dp.id_pembelian = ?
        ORDER BY dp.id_detail_beli ASC
    ');
    $stmt->bind_param('i', $idPembelian);
    $stmt->execute();
    $purchase['details'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $purchase;
}

function module4_extract_cart(array $post)
{
    $productIds = $post['id_produk'] ?? [];
    $quantities = $post['jumlah'] ?? [];
    $prices = $post['harga_beli'] ?? [];

    $count = max(count($productIds), count($quantities), count($prices));
    $items = [];
    $errors = [];

    for ($i = 0; $i < $count; $i++) {
        $productId = (int) ($productIds[$i] ?? 0);
        $quantityRaw = $quantities[$i] ?? '';
        $priceRaw = $prices[$i] ?? '';
        $hasAnyValue = trim((string) $quantityRaw) !== '' || trim((string) $priceRaw) !== '' || $productId > 0;

        if (!$hasAnyValue) {
            continue;
        }

        $quantity = module4_int($quantityRaw);
        $price = module4_decimal($priceRaw);

        if ($productId <= 0) {
            $errors[] = 'Pilih produk untuk setiap item restock.';
            continue;
        }

        if ($quantity === false || $quantity <= 0) {
            $errors[] = 'Jumlah restock harus lebih dari 0.';
            continue;
        }

        if ($price < 0) {
            $errors[] = 'Harga beli tidak boleh negatif.';
            continue;
        }

        if (!isset($items[$productId])) {
            $items[$productId] = [
                'id_produk' => $productId,
                'nama_produk' => '',
                'jumlah' => 0,
                'harga_beli' => $price,
            ];
        }

        $items[$productId]['jumlah'] += (int) $quantity;
        $items[$productId]['harga_beli'] = $price;
    }

    return [
        'items' => array_values($items),
        'errors' => $errors,
    ];
}

function module4_enrich_cart(array $items, array $productMap)
{
    $enriched = [];

    foreach ($items as $item) {
        $idProduk = (int) $item['id_produk'];
        if (!isset($productMap[$idProduk])) {
            continue;
        }

        $product = $productMap[$idProduk];
        $jumlah = (int) $item['jumlah'];
        $hargaBeli = (float) $item['harga_beli'];
        $subtotal = $jumlah * $hargaBeli;

        $enriched[] = [
            'id_produk' => $idProduk,
            'nama_produk' => $product['nama_produk'],
            'kode_produk' => $product['kode_produk'] ?? null,
            'satuan' => $product['satuan'] ?? '',
            'stok' => (int) ($product['stok'] ?? 0),
            'jumlah' => $jumlah,
            'harga_beli' => $hargaBeli,
            'subtotal' => $subtotal,
        ];
    }

    return $enriched;
}

function module4_cart_total(array $items)
{
    $total = 0;

    foreach ($items as $item) {
        $total += (float) $item['subtotal'];
    }

    return $total;
}

function module4_sync_stok(mysqli $conn, array $items, $direction)
{
    $stmt = $conn->prepare('UPDATE produk SET stok = stok + ? WHERE id_produk = ?');

    foreach ($items as $item) {
        $delta = (int) $item['jumlah'] * (int) $direction;
        $idProduk = (int) $item['id_produk'];
        $stmt->bind_param('ii', $delta, $idProduk);
        $stmt->execute();
    }
}
