<?php
require_once __DIR__ . "/../../includes/auth_check.php";
require_once __DIR__ . "/../../includes/sidebar.php";

require_role(['owner', 'admin', 'kasir']);

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

        $query = $conn->prepare("
            SELECT 
                id_produk,
                stok
            FROM produk
            WHERE id_produk = ?
        ");

        $query->bind_param("i", $id_produk);
        $query->execute();
        $produk = $query->get_result()->fetch_assoc();

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
    $search = "%" . $keyword . "%";
    $queryProduk = $conn->prepare("
        SELECT 
            id_produk,
            kode_produk,
            nama_produk,
            harga_jual,
            stok
        FROM produk
        WHERE 
            nama_produk LIKE ?
            OR kode_produk LIKE ?
        ORDER BY nama_produk ASC
    ");

    $queryProduk->bind_param("ss", $search, $search);
    $queryProduk->execute();
    $dataProduk = $queryProduk->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $queryProduk = $conn->query("
        SELECT 
            id_produk,
            kode_produk,
            nama_produk,
            harga_jual,
            stok
        FROM produk
        ORDER BY nama_produk ASC
    ");

    $dataProduk = $queryProduk->fetch_all(MYSQLI_ASSOC);
}
?>

<?php render_page_start('Tambah Barang Hutang', 'hutang', ['assets/css/hutang.css']); ?>

<div class="page-wrapper">
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
</div>

<?php render_page_end(); ?>
