<?php
require_once __DIR__ . "/../../config/koneksi.php";

function e($value) {
    return htmlspecialchars($value ?? "-", ENT_QUOTES, "UTF-8");
}

function rupiah($angka) {
    return "Rp " . number_format((float) $angka, 0, ",", ".");
}

function formatTanggal($tanggal) {
    if (empty($tanggal)) {
        return "-";
    }

    return date("d/m/Y", strtotime($tanggal));
}

$queryHutang = $pdo->query("
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
    LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan
    LEFT JOIN users u ON h.id_user = u.id_user
    ORDER BY h.tanggal_hutang DESC, h.id_hutang DESC
");

$dataHutang = $queryHutang->fetchAll(PDO::FETCH_ASSOC);

$detailByHutang = [];

if (count($dataHutang) > 0) {
    $idHutangList = array_column($dataHutang, "id_hutang");
    $placeholders = implode(",", array_fill(0, count($idHutangList), "?"));

    $queryDetail = $pdo->prepare("
        SELECT
            dh.id_hutang,
            dh.id_detail_hutang,
            dh.id_produk,
            dh.jumlah,
            dh.harga_satuan,
            dh.subtotal,
            pr.kode_produk,
            pr.nama_produk
        FROM detail_hutang dh
        LEFT JOIN produk pr ON dh.id_produk = pr.id_produk
        WHERE dh.id_hutang IN ($placeholders)
        ORDER BY dh.id_hutang ASC, dh.id_detail_hutang ASC
    ");

    $queryDetail->execute($idHutangList);
    $dataDetail = $queryDetail->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dataDetail as $detail) {
        $idHutang = $detail["id_hutang"];

        if (!isset($detailByHutang[$idHutang])) {
            $detailByHutang[$idHutang] = [];
        }

        $detailByHutang[$idHutang][] = $detail;
    }
}

$totalTransaksi = count($dataHutang);
$totalHutang = 0;
$totalTerbayar = 0;
$totalSisa = 0;
$totalAktif = 0;
$totalLunas = 0;

foreach ($dataHutang as $hutang) {
    $totalHutang += (float) $hutang["total_hutang"];
    $totalTerbayar += (float) $hutang["jumlah_terbayar"];
    $totalSisa += (float) $hutang["sisa_hutang"];

    if ($hutang["status"] === "lunas") {
        $totalLunas++;
    } else {
        $totalAktif++;
    }
}

$tanggalCetak = date("d/m/Y H:i");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan Hutang - Ca'lontong</title>

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../../assets/css/hutang.css">
</head>
<body>

    <main class="report-wrapper">

        <div class="report-action no-print">
            <a href="manajemen-hutang.php" class="btn btn-outline-secondary">
                Kembali
            </a>

            <button type="button" onclick="window.print()" class="btn btn-success">
                Cetak / Simpan PDF
            </button>
        </div>

        <section class="report-paper">

            <div class="report-header">
                <h1>Laporan Hutang Kasbon</h1>
                <h2>Ca'lontong</h2>
                <p>Dicetak pada: <?= e($tanggalCetak); ?></p>
            </div>

            <div class="report-summary-grid">
                <div class="report-summary-item">
                    <span>Total Transaksi</span>
                    <strong><?= e($totalTransaksi); ?></strong>
                </div>

                <div class="report-summary-item">
                    <span>Hutang Aktif</span>
                    <strong><?= e($totalAktif); ?></strong>
                </div>

                <div class="report-summary-item">
                    <span>Hutang Lunas</span>
                    <strong><?= e($totalLunas); ?></strong>
                </div>

                <div class="report-summary-item">
                    <span>Total Hutang</span>
                    <strong><?= rupiah($totalHutang); ?></strong>
                </div>

                <div class="report-summary-item">
                    <span>Total Terbayar</span>
                    <strong><?= rupiah($totalTerbayar); ?></strong>
                </div>

                <div class="report-summary-item">
                    <span>Total Sisa</span>
                    <strong><?= rupiah($totalSisa); ?></strong>
                </div>
            </div>

            <h3 class="report-section-title">Ringkasan Hutang</h3>

            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Terbayar</th>
                            <th>Sisa</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($dataHutang) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($dataHutang as $hutang): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= e($hutang["kode_hutang"]); ?></td>
                                    <td>
                                        <strong><?= e($hutang["nama_pelanggan"]); ?></strong><br>
                                        <small><?= e($hutang["no_telepon"]); ?></small>
                                    </td>
                                    <td><?= formatTanggal($hutang["tanggal_hutang"]); ?></td>
                                    <td><?= rupiah($hutang["total_hutang"]); ?></td>
                                    <td><?= rupiah($hutang["jumlah_terbayar"]); ?></td>
                                    <td><?= rupiah($hutang["sisa_hutang"]); ?></td>
                                    <td><?= e(ucfirst($hutang["status"])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    Belum ada data hutang.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h3 class="report-section-title mt-4">Detail Hutang</h3>

            <?php if (count($dataHutang) > 0): ?>
                <?php foreach ($dataHutang as $index => $hutang): ?>
                    <?php
                        $idHutang = $hutang["id_hutang"];
                        $detailList = $detailByHutang[$idHutang] ?? [];
                        $totalQty = 0;

                        foreach ($detailList as $detail) {
                            $totalQty += (int) $detail["jumlah"];
                        }
                    ?>

                    <div class="report-detail-card">
                        <div class="report-detail-header">
                            <div>
                                <h4><?= ($index + 1); ?>. <?= e($hutang["nama_pelanggan"]); ?></h4>
                                <p>
                                    Kode: <?= e($hutang["kode_hutang"]); ?> |
                                    Tanggal: <?= formatTanggal($hutang["tanggal_hutang"]); ?> |
                                    Status: <?= e(ucfirst($hutang["status"])); ?>
                                </p>
                            </div>

                            <div class="report-detail-total">
                                <span>Total</span>
                                <strong><?= rupiah($hutang["total_hutang"]); ?></strong>
                            </div>
                        </div>

                        <div class="report-customer-info">
                            <div>
                                <span>No Telepon</span>
                                <strong><?= e($hutang["no_telepon"]); ?></strong>
                            </div>

                            <div>
                                <span>Alamat</span>
                                <strong><?= e($hutang["alamat"]); ?></strong>
                            </div>

                            <div>
                                <span>Dicatat Oleh</span>
                                <strong><?= e($hutang["nama_user"]); ?></strong>
                            </div>
                        </div>

                        <table class="report-table report-detail-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($detailList) > 0): ?>
                                    <?php $noDetail = 1; ?>
                                    <?php foreach ($detailList as $detail): ?>
                                        <tr>
                                            <td><?= $noDetail++; ?></td>
                                            <td><?= e($detail["nama_produk"]); ?></td>
                                            <td><?= rupiah($detail["harga_satuan"]); ?></td>
                                            <td><?= e($detail["jumlah"]); ?></td>
                                            <td><?= rupiah($detail["subtotal"]); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Tidak ada detail produk.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <div class="report-payment-info">
                            <div>
                                <span>Jumlah Item</span>
                                <strong><?= e($totalQty); ?> Item</strong>
                            </div>

                            <div>
                                <span>Total Hutang</span>
                                <strong><?= rupiah($hutang["total_hutang"]); ?></strong>
                            </div>

                            <div>
                                <span>Terbayar</span>
                                <strong><?= rupiah($hutang["jumlah_terbayar"]); ?></strong>
                            </div>

                            <div>
                                <span>Sisa</span>
                                <strong><?= rupiah($hutang["sisa_hutang"]); ?></strong>
                            </div>
                        </div>

                        <div class="report-note">
                            <span>Catatan:</span>
                            <p><?= e($hutang["keterangan"]); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="report-empty">
                    Belum ada detail hutang.
                </div>
            <?php endif; ?>

            <div class="report-footer">
                <p>Ca'lontong - Laporan Hutang Kasbon</p>
            </div>

        </section>

    </main>

</body>
</html>