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
?>

<?php render_page_start('Detail Hutang', 'hutang', ['assets/css/hutang.css']); ?>

<div class="page-wrapper">
    <section class="debt-section">

        <div class="page-title-row">
            <a href="manajemen-hutang.php" class="back-link">←</a>
            <h4 class="page-title mb-0">Detail Hutang Kasbon</h4>
        </div>

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
                <span>Jumlah Terbayar</span>
                <strong><?= rupiah($hutang["jumlah_terbayar"]); ?></strong>
            </div>

            <div class="payment-row">
                <span>Sisa Hutang</span>
                <strong class="text-success"><?= rupiah($hutang["sisa_hutang"]); ?></strong>
            </div>

            <div class="payment-row">
                <span>Tanggal Lunas</span>
                <strong><?= formatTanggal($hutang["tanggal_lunas"]); ?></strong>
            </div>
        </div>

        <div class="mb-3">
            <label class="page-subtitle">Catatan</label>
            <textarea 
                class="form-control textarea-catatan readonly-control" 
                readonly
            ><?= e($hutang["keterangan"]); ?></textarea>
        </div>

    </section>
</div>

<?php render_page_end(); ?>
