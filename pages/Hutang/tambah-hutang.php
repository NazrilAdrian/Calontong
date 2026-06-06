<?php
require_once __DIR__ . "/../../includes/auth_check.php";
require_once __DIR__ . "/../../includes/sidebar.php";

require_role(['owner', 'admin', 'kasir']);

function rupiah($angka) {
    return "Rp " . number_format((float) $angka, 0, ",", ".");
}

function generateKodeHutang(mysqli $conn) {
    do {
        $kode = "HTG-" . date("Ymd-His") . "-" . rand(100, 999);

        $cek = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM hutang 
            WHERE kode_hutang = ?
        ");

        $cek->bind_param("s", $kode);
        $cek->execute();
        $jumlah = (int) $cek->get_result()->fetch_assoc()["total"];

    } while ($jumlah > 0);

    return $kode;
}

function getCurrentUserId(mysqli $conn) {
    if (isset($_SESSION["id_user"]) && is_numeric($_SESSION["id_user"])) {
        $id_user = (int) $_SESSION["id_user"];

        $cekUser = $conn->prepare("
            SELECT id_user 
            FROM users 
            WHERE id_user = ?
        ");

        $cekUser->bind_param("i", $id_user);
        $cekUser->execute();
        $user = $cekUser->get_result()->fetch_assoc();

        if ($user) {
            return $id_user;
        }
    }

    $query = $conn->query("
        SELECT id_user 
        FROM users 
        ORDER BY id_user ASC 
        LIMIT 1
    ");

    $user = $query->fetch_assoc();

    if ($user) {
        return (int) $user["id_user"];
    }

    return null;
}

function getCartItems(mysqli $conn) {
    $cart = $_SESSION["hutang_cart"] ?? [];

    if (empty($cart)) {
        return [];
    }

    $ids = array_keys($cart);
    $ids = array_values(array_filter($ids, function ($id) {
        return is_numeric($id);
    }));

    if (empty($ids)) {
        return [];
    }

    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $types = str_repeat("i", count($ids));
    $ids = array_map("intval", $ids);

    $query = $conn->prepare("
        SELECT 
            id_produk,
            nama_produk,
            harga_jual,
            stok
        FROM produk
        WHERE id_produk IN ($placeholders)
        ORDER BY nama_produk ASC
    ");

    $query->bind_param($types, ...$ids);
    $query->execute();
    $produkList = $query->get_result()->fetch_all(MYSQLI_ASSOC);

    $items = [];

    foreach ($produkList as $produk) {
        $id_produk = (int) $produk["id_produk"];
        $qty = (int) ($cart[$id_produk] ?? 1);

        if ($qty < 1) {
            $qty = 1;
        }

        $harga = (float) $produk["harga_jual"];
        $subtotal = $harga * $qty;

        $items[] = [
            "id_produk" => $id_produk,
            "nama_produk" => $produk["nama_produk"],
            "harga_jual" => $harga,
            "stok" => (int) $produk["stok"],
            "qty" => $qty,
            "subtotal" => $subtotal
        ];
    }

    return $items;
}

function hitungRingkasan($items) {
    $jumlah_item = 0;
    $total_harga = 0;

    foreach ($items as $item) {
        $jumlah_item += (int) $item["qty"];
        $total_harga += (float) $item["subtotal"];
    }

    return [
        "jumlah_item" => $jumlah_item,
        "total_harga" => $total_harga
    ];
}

function tambahQtyProduk(mysqli $conn, $id_produk) {
    $query = $conn->prepare("
        SELECT stok 
        FROM produk 
        WHERE id_produk = ?
    ");

    $query->bind_param("i", $id_produk);
    $query->execute();
    $produk = $query->get_result()->fetch_assoc();

    if (!$produk) {
        return "produk-tidak-ditemukan";
    }

    $stok = (int) $produk["stok"];
    $qtySekarang = (int) ($_SESSION["hutang_cart"][$id_produk] ?? 0);

    if ($stok <= 0) {
        return "stok-kosong";
    }

    if ($qtySekarang >= $stok) {
        return "stok-maksimal";
    }

    $_SESSION["hutang_cart"][$id_produk] = $qtySekarang + 1;

    return "qty-bertambah";
}

function kurangQtyProduk($id_produk) {
    $qtySekarang = (int) ($_SESSION["hutang_cart"][$id_produk] ?? 0);

    if ($qtySekarang <= 1) {
        unset($_SESSION["hutang_cart"][$id_produk]);
    } else {
        $_SESSION["hutang_cart"][$id_produk] = $qtySekarang - 1;
    }
}

if (!isset($_SESSION["hutang_form"])) {
    $_SESSION["hutang_form"] = [
        "id_pelanggan" => "",
        "keterangan" => ""
    ];
}

if (!isset($_SESSION["hutang_cart"])) {
    $_SESSION["hutang_cart"] = [];
}

$errors = [];
$status = $_GET["status"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_action = $_POST["form_action"] ?? "";

    $_SESSION["hutang_form"]["id_pelanggan"] = trim($_POST["id_pelanggan"] ?? "");
    $_SESSION["hutang_form"]["keterangan"] = trim($_POST["keterangan"] ?? "");

    if ($form_action === "open_product") {
        header("Location: tambah-barang-hutang.php");
        exit;
    }

    if (strpos($form_action, "plus-") === 0) {
        $id_produk = (int) str_replace("plus-", "", $form_action);
        $hasil = tambahQtyProduk($conn, $id_produk);

        header("Location: tambah-hutang.php?status=" . $hasil);
        exit;
    }

    if (strpos($form_action, "minus-") === 0) {
        $id_produk = (int) str_replace("minus-", "", $form_action);
        kurangQtyProduk($id_produk);

        header("Location: tambah-hutang.php?status=qty-berkurang");
        exit;
    }

    if ($form_action === "save") {
        $id_pelanggan = $_SESSION["hutang_form"]["id_pelanggan"];
        $keterangan = $_SESSION["hutang_form"]["keterangan"];

        if ($id_pelanggan === "") {
            $errors[] = "Pelanggan wajib dipilih.";
        }

        if ($id_pelanggan !== "") {
            $id_pelanggan_int = (int) $id_pelanggan;
            $cekPelanggan = $conn->prepare("
                SELECT COUNT(*) AS total
                FROM pelanggan 
                WHERE id_pelanggan = ?
            ");

            $cekPelanggan->bind_param("i", $id_pelanggan_int);
            $cekPelanggan->execute();

            if ((int) $cekPelanggan->get_result()->fetch_assoc()["total"] < 1) {
                $errors[] = "Data pelanggan tidak ditemukan.";
            }
        }

        $items = getCartItems($conn);

        if (empty($items)) {
            $errors[] = "Minimal tambahkan 1 barang.";
        }

        foreach ($items as $item) {
            if ($item["qty"] > $item["stok"]) {
                $errors[] = "Stok " . $item["nama_produk"] . " tidak cukup.";
            }
        }

        $id_user = getCurrentUserId($conn);

        if (!$id_user) {
            $errors[] = "Data user belum ada. Tambahkan minimal 1 user terlebih dahulu karena tabel hutang membutuhkan id_user.";
        }

        if (empty($errors)) {
            try {
                $conn->begin_transaction();

                $ringkasan = hitungRingkasan($items);
                $total_hutang = (float) $ringkasan["total_harga"];
                $kode_hutang = generateKodeHutang($conn);
                $id_pelanggan_int = (int) $id_pelanggan;
                $sisa_hutang = $total_hutang;
                $status_hutang = "aktif";
                $tanggal_hutang = date("Y-m-d");

                $insertHutang = $conn->prepare("
                    INSERT INTO hutang
                    (
                        id_pelanggan,
                        id_user,
                        kode_hutang,
                        total_hutang,
                        jumlah_terbayar,
                        sisa_hutang,
                        status,
                        tanggal_hutang,
                        keterangan
                    )
                    VALUES
                    (?, ?, ?, ?, 0, ?, ?, ?, ?)
                ");

                $insertHutang->bind_param("iisddsss", $id_pelanggan_int, $id_user, $kode_hutang, $total_hutang, $sisa_hutang, $status_hutang, $tanggal_hutang, $keterangan);
                $insertHutang->execute();

                $id_hutang = $conn->insert_id;

                $insertDetail = $conn->prepare("
                    INSERT INTO detail_hutang
                    (
                        id_hutang,
                        id_produk,
                        jumlah,
                        harga_satuan,
                        subtotal
                    )
                    VALUES
                    (?, ?, ?, ?, ?)
                ");

                $updateStok = $conn->prepare("
                    UPDATE produk
                    SET stok = stok - ?
                    WHERE id_produk = ?
                    AND stok >= ?
                ");

                foreach ($items as $item) {
                    $id_produk = (int) $item["id_produk"];
                    $jumlah = (int) $item["qty"];
                    $harga_satuan = (float) $item["harga_jual"];
                    $subtotal = (float) $item["subtotal"];

                    $insertDetail->bind_param("iiidd", $id_hutang, $id_produk, $jumlah, $harga_satuan, $subtotal);
                    $insertDetail->execute();

                    $updateStok->bind_param("iii", $jumlah, $id_produk, $jumlah);
                    $updateStok->execute();

                    if ($updateStok->affected_rows < 1) {
                        throw new Exception("Stok " . $item["nama_produk"] . " tidak cukup.");
                    }
                }

                $conn->commit();

                unset($_SESSION["hutang_form"]);
                unset($_SESSION["hutang_cart"]);

                header("Location: manajemen-hutang.php?status=tambah-hutang-berhasil");
                exit;

            } catch (Exception $e) {
                $conn->rollback();
                $errors[] = "Gagal mencatat hutang: " . $e->getMessage();
            }
        }
    }
}

$queryPelanggan = $conn->query("
    SELECT 
        id_pelanggan,
        nama_pelanggan,
        no_telepon
    FROM pelanggan
    ORDER BY nama_pelanggan ASC
");

$dataPelanggan = $queryPelanggan->fetch_all(MYSQLI_ASSOC);

$items = getCartItems($conn);
$ringkasan = hitungRingkasan($items);

$id_pelanggan_terpilih = $_SESSION["hutang_form"]["id_pelanggan"] ?? "";
$keterangan = $_SESSION["hutang_form"]["keterangan"] ?? "";
?>

<?php render_page_start('Catat Hutang Kasbon', 'hutang', ['assets/css/hutang.css']); ?>

<div class="page-wrapper">
    <section class="debt-section">

        <h4 class="page-title">Catat Hutang Kasbon</h4>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($status === "barang-ditambahkan"): ?>
            <div class="alert alert-success">
                Barang berhasil ditambahkan.
            </div>
        <?php elseif ($status === "stok-maksimal"): ?>
            <div class="alert alert-warning">
                Qty sudah mencapai batas stok.
            </div>
        <?php elseif ($status === "stok-kosong"): ?>
            <div class="alert alert-warning">
                Stok produk kosong.
            </div>
        <?php endif; ?>

        <form action="" method="POST">

            <div class="select-card mb-3">
                <label for="id_pelanggan" class="form-label">
                    Pilih Pelanggan
                </label>

                <select 
                    class="form-select" 
                    id="id_pelanggan" 
                    name="id_pelanggan"
                >
                    <option value="">Pilih Pelanggan</option>

                    <?php foreach ($dataPelanggan as $pelanggan): ?>
                        <option 
                            value="<?= e($pelanggan["id_pelanggan"]); ?>"
                            <?= ((string) $id_pelanggan_terpilih === (string) $pelanggan["id_pelanggan"]) ? "selected" : ""; ?>
                        >
                            <?= e($pelanggan["nama_pelanggan"]); ?>
                            <?php if (!empty($pelanggan["no_telepon"])): ?>
                                - <?= e($pelanggan["no_telepon"]); ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="table-responsive table-box product-cart-box mb-2">
                <table class="table table-borderless align-middle mb-0 debt-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($items) > 0): ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= e($item["nama_produk"]); ?></td>
                                    <td><?= rupiah($item["harga_jual"]); ?></td>
                                    <td>
                                        <div class="qty-control">
                                            <button 
                                                type="submit" 
                                                name="form_action" 
                                                value="minus-<?= e($item["id_produk"]); ?>"
                                                class="qty-btn"
                                            >
                                                -
                                            </button>

                                            <span class="qty-number">
                                                <?= e($item["qty"]); ?>
                                            </span>

                                            <button 
                                                type="submit" 
                                                name="form_action" 
                                                value="plus-<?= e($item["id_produk"]); ?>"
                                                class="qty-btn"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td><?= rupiah($item["subtotal"]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="empty-row">
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada barang yang ditambahkan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <button 
                type="submit" 
                name="form_action" 
                value="open_product" 
                class="btn btn-success w-100 btn-add-product mb-3"
            >
                Tambah Barang
            </button>

            <div class="summary-card mb-3">
                <div>
                    <div class="summary-label">Jumlah Item</div>
                    <div class="summary-title">Subtotal</div>
                </div>

                <div class="text-end">
                    <div class="summary-label">
                        <?= e($ringkasan["jumlah_item"]); ?> Item
                    </div>
                    <div class="summary-price">
                        <?= rupiah($ringkasan["total_harga"]); ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="page-subtitle">
                    Catatan
                </label>

                <textarea 
                    class="form-control textarea-catatan" 
                    id="keterangan" 
                    name="keterangan"
                    placeholder="Tulis catatan jika ada"
                ><?= e($keterangan); ?></textarea>
            </div>

            <button 
                type="submit" 
                name="form_action" 
                value="save" 
                class="btn btn-success w-100"
            >
                Catat Hutang
            </button>

        </form>

    </section>
</div>

<?php render_page_end(); ?>
