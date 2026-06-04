<?php
session_start();

require_once __DIR__ . "/../config/koneksi.php";

function e($value) {
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function rupiah($angka) {
    return "Rp " . number_format((float) $angka, 0, ",", ".");
}

if (!isset($_SESSION["hutang_cart"])) {
    $_SESSION["hutang_cart"] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_produk = $_POST["id_produk"] ?? null;

    if ($id_produk && is_numeric($id_produk)) {
        $id_produk = (int) $id_produk;

        $query = $pdo->prepare("
            SELECT 
                id_produk,
                stok
            FROM produk
            WHERE id_produk = :id_produk
        ");

        $query->execute([
            ":id_produk" => $id_produk
        ]);

        $produk = $query->fetch(PDO::FETCH_ASSOC);

        if (!$produk) {
            header("Location: tambah-barang-hutang.php?status=produk-tidak-ditemukan");
            exit;
        }

        $stok = (int) $produk["stok"];
        $qtySekarang = (int) ($_SESSION["hutang_cart"][$id_produk] ?? 0);

        if ($stok <= 0) {
            header("Location: tambah-barang-hutang.php?status=stok-kosong");
            exit;
        }

        if ($qtySekarang >= $stok) {
            header("Location: tambah-barang-hutang.php?status=stok-maksimal");
            exit;
        }

        $_SESSION["hutang_cart"][$id_produk] = $qtySekarang + 1;

        header("Location: tambah-hutang.php?status=barang-ditambahkan");
        exit;
    }

    header("Location: tambah-barang-hutang.php?status=produk-tidak-valid");
    exit;
}

$keyword = trim($_GET["q"] ?? "");
$status = $_GET["status"] ?? "";

if ($keyword !== "") {
    $queryProduk = $pdo->prepare("
        SELECT 
            id_produk,
            kode_produk,
            nama_produk,
            harga_jual,
            stok
        FROM produk
        WHERE 
            nama_produk LIKE :keyword
            OR kode_produk LIKE :keyword
        ORDER BY nama_produk ASC
    ");

    $queryProduk->execute([
        ":keyword" => "%" . $keyword . "%"
    ]);
} else {
    $queryProduk = $pdo->query("
        SELECT 
            id_produk,
            kode_produk,
            nama_produk,
            harga_jual,
            stok
        FROM produk
        ORDER BY nama_produk ASC
    ");
}

$dataProduk = $queryProduk->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Hutang - Ca'lontong</title>

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/css/hutang.css">
</head>
<body>

    <main class="page-wrapper">
        <section class="debt-section">

            <div class="page-title-row">
                <a href="tambah-hutang.php" class="back-link">
                    ←
                </a>

                <h4 class="page-title mb-0">Catat Hutang Kasbon</h4>
            </div>

            <?php if ($status === "stok-kosong"): ?>
                <div class="alert alert-warning mt-3">
                    Stok produk kosong.
                </div>
            <?php elseif ($status === "stok-maksimal"): ?>
                <div class="alert alert-warning mt-3">
                    Qty sudah mencapai batas stok.
                </div>
            <?php elseif ($status === "produk-tidak-ditemukan"): ?>
                <div class="alert alert-danger mt-3">
                    Produk tidak ditemukan.
                </div>
            <?php elseif ($status === "produk-tidak-valid"): ?>
                <div class="alert alert-danger mt-3">
                    Produk tidak valid.
                </div>
            <?php endif; ?>

            <form action="" method="GET" class="search-form mt-3 mb-3">
                <input 
                    type="text" 
                    name="q" 
                    class="form-control search-input" 
                    placeholder="Cari produk"
                    value="<?= e($keyword); ?>"
                >
            </form>

            <div class="table-responsive table-box product-list-box">
                <table class="table table-borderless align-middle mb-0 product-list-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($dataProduk) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($dataProduk as $produk): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <div class="product-name">
                                            <?= e($produk["nama_produk"]); ?>
                                        </div>

                                        <div class="product-stock">
                                            Stok: <?= e($produk["stok"]); ?>
                                        </div>
                                    </td>
                                    <td><?= rupiah($produk["harga_jual"]); ?></td>
                                    <td>
                                        <form action="" method="POST">
                                            <input 
                                                type="hidden" 
                                                name="id_produk" 
                                                value="<?= e($produk["id_produk"]); ?>"
                                            >

                                            <button 
                                                type="submit" 
                                                class="btn btn-sm btn-success btn-action"
                                                <?= ((int) $produk["stok"] <= 0) ? "disabled" : ""; ?>
                                            >
                                                Tambah
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="empty-row empty-product-row">
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada data produk
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>
    </main>

    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>
</body>
</html>