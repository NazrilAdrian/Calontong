<?php
require_once __DIR__ . "/../../includes/auth_check.php";
require_once __DIR__ . "/../../includes/sidebar.php";

require_role(['owner', 'admin', 'kasir']);

function rupiah($angka) {
    return "Rp " . number_format((float) $angka, 0, ",", ".");
}

function formatTanggal($tanggal) {
    if (empty($tanggal)) {
        return "-";
    }

    return date("d/m/Y", strtotime($tanggal));
}

function bersihkanNominal($value) {
    $value = trim($value ?? "");

    if ($value === "") {
        return 0;
    }

    $value = str_replace("Rp", "", $value);
    $value = str_replace("rp", "", $value);
    $value = str_replace(".", "", $value);
    $value = str_replace(",", ".", $value);
    $value = preg_replace("/[^0-9.]/", "", $value);

    return (float) $value;
}

$id_hutang = $_GET["id"] ?? null;

if (!$id_hutang || !is_numeric($id_hutang)) {
    header("Location: manajemen-hutang.php");
    exit;
}

$id_hutang = (int) $id_hutang;

$queryHutang = $conn->prepare("
    SELECT
        h.id_hutang,
        h.kode_hutang,
        h.total_hutang,
        h.jumlah_terbayar,
        COALESCE(h.sisa_hutang, h.total_hutang - h.jumlah_terbayar) AS sisa_hutang,
        h.status,
        h.tanggal_hutang,
        h.tanggal_lunas,
        h.keterangan,
        p.nama_pelanggan,
        p.no_telepon,
        p.alamat,
        u.nama_lengkap AS nama_user
    FROM hutang h
    INNER JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan
    INNER JOIN users u ON h.id_user = u.id_user
    WHERE h.id_hutang = ?
");

$queryHutang->bind_param("i", $id_hutang);
$queryHutang->execute();
$hutang = $queryHutang->get_result()->fetch_assoc();

if (!$hutang) {
    header("Location: manajemen-hutang.php");
    exit;
}

$queryDetail = $conn->prepare("
    SELECT
        dh.id_detail_hutang,
        dh.id_produk,
        dh.jumlah,
        dh.harga_satuan,
        dh.subtotal,
        pr.nama_produk,
        pr.kode_produk
    FROM detail_hutang dh
    INNER JOIN produk pr ON dh.id_produk = pr.id_produk
    WHERE dh.id_hutang = ?
    ORDER BY dh.id_detail_hutang ASC
");

$queryDetail->bind_param("i", $id_hutang);
$queryDetail->execute();
$detailHutang = $queryDetail->get_result()->fetch_all(MYSQLI_ASSOC);

$totalQty = 0;

foreach ($detailHutang as $detail) {
    $totalQty += (int) $detail["jumlah"];
}

$errors = [];
$tambah_bayar = "";
$keterangan = $hutang["keterangan"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tambah_bayar = trim($_POST["tambah_bayar"] ?? "");
    $keterangan = trim($_POST["keterangan"] ?? "");

    $nominalBayar = bersihkanNominal($tambah_bayar);

    $total_hutang = (float) $hutang["total_hutang"];
    $jumlah_terbayar_lama = (float) $hutang["jumlah_terbayar"];
    $sisa_hutang_lama = (float) $hutang["sisa_hutang"];

    if ($nominalBayar < 0) {
        $errors[] = "Nominal cicilan tidak boleh kurang dari 0.";
    }

    if ($nominalBayar > $sisa_hutang_lama) {
        $errors[] = "Nominal cicilan tidak boleh lebih besar dari sisa hutang.";
    }

    if ($hutang["status"] === "lunas" && $nominalBayar > 0) {
        $errors[] = "Hutang sudah lunas, tidak bisa menambah cicilan lagi.";
    }

    if (empty($errors)) {
        $jumlah_terbayar_baru = $jumlah_terbayar_lama + $nominalBayar;
        $sisa_hutang_baru = $total_hutang - $jumlah_terbayar_baru;

        if ($sisa_hutang_baru <= 0) {
            $sisa_hutang_baru = 0;
            $status_baru = "lunas";
            $tanggal_lunas = date("Y-m-d");
        } else {
            $status_baru = "aktif";
            $tanggal_lunas = null;
        }

        $update = $conn->prepare("
            UPDATE hutang
            SET
                jumlah_terbayar = ?,
                sisa_hutang = ?,
                status = ?,
                tanggal_lunas = ?,
                keterangan = ?
            WHERE id_hutang = ?
        ");

        $update->bind_param("ddsssi", $jumlah_terbayar_baru, $sisa_hutang_baru, $status_baru, $tanggal_lunas, $keterangan, $id_hutang);
        $update->execute();

        header("Location: manajemen-hutang.php?status=edit-hutang-berhasil");
        exit;
    }
}
?>

<?php render_page_start('Catat Pembayaran Hutang', 'hutang', ['assets/css/hutang.css']); ?>

<div class="page-wrapper">
    <section class="debt-section">

        <div class="page-title-row">
            <a href="manajemen-hutang.php" class="back-link">←</a>
            <h4 class="page-title mb-0">Catat Pembayaran Hutang</h4>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mt-3">
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">

            <div class="select-card mt-3 mb-3">
                <label class="form-label">Pelanggan</label>
                <input 
                    type="text" 
                    class="form-control readonly-control" 
                    value="<?= e($hutang["nama_pelanggan"]); ?>" 
                    readonly
                >
            </div>

            <div class="detail-info-card mb-3">
                <div class="info-grid">

                    <div class="info-item">
                        <div class="info-label">Kode Hutang</div>
                        <div class="info-value"><?= e($hutang["kode_hutang"]); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tanggal Hutang</div>
                        <div class="info-value"><?= formatTanggal($hutang["tanggal_hutang"]); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Dicatat Oleh</div>
                        <div class="info-value"><?= e($hutang["nama_user"]); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge <?= $hutang["status"] === "lunas" ? "status-lunas" : "status-aktif"; ?>">
                                <?= e(ucfirst($hutang["status"])); ?>
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="table-responsive table-box product-cart-box mb-3">
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
                        <?php if (count($detailHutang) > 0): ?>
                            <?php foreach ($detailHutang as $detail): ?>
                                <tr>
                                    <td><?= e($detail["nama_produk"]); ?></td>
                                    <td><?= rupiah($detail["harga_satuan"]); ?></td>
                                    <td><?= e($detail["jumlah"]); ?></td>
                                    <td><?= rupiah($detail["subtotal"]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="empty-row">
                                <td colspan="4" class="text-center text-muted">
                                    Tidak ada detail produk
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="summary-card mb-3">
                <div>
                    <div class="summary-label">Jumlah Item</div>
                    <div class="summary-title">Total Hutang</div>
                </div>

                <div class="text-end">
                    <div class="summary-label"><?= e($totalQty); ?> Item</div>
                    <div class="summary-price"><?= rupiah($hutang["total_hutang"]); ?></div>
                </div>
            </div>

            <div class="payment-card mb-3">
                <div class="payment-row">
                    <span>Sudah Terbayar</span>
                    <strong><?= rupiah($hutang["jumlah_terbayar"]); ?></strong>
                </div>

                <div class="payment-row">
                    <span>Sisa Hutang</span>
                    <strong class="text-success"><?= rupiah($hutang["sisa_hutang"]); ?></strong>
                </div>
            </div>

            <div class="mb-3">
                <label for="tambah_bayar" class="form-label">
                    Tambah Cicilan
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="tambah_bayar" 
                    name="tambah_bayar"
                    placeholder="Contoh: 5000"
                    value="<?= e($tambah_bayar); ?>"
                    <?= $hutang["status"] === "lunas" ? "readonly" : ""; ?>
                >
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

            <div class="row g-3">
                <div class="col-6">
                    <a href="manajemen-hutang.php" class="btn btn-outline-secondary w-100">
                        Batal
                    </a>
                </div>

                <div class="col-6">
                    <button type="submit" class="btn btn-success w-100">
                        Simpan
                    </button>
                </div>
            </div>

        </form>

    </section>
</div>

<?php render_page_end(); ?>
