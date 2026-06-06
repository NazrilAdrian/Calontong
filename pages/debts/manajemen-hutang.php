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

// Ambil data pelanggan sesuai database calontong.sql
$queryPelanggan = $pdo->query("
    SELECT 
        id_pelanggan,
        nama_pelanggan,
        no_telepon,
        alamat,
        keterangan
    FROM pelanggan
    ORDER BY id_pelanggan DESC
");

$dataPelanggan = $queryPelanggan->fetchAll(PDO::FETCH_ASSOC);

// Ambil data hutang sesuai database calontong.sql
$queryHutang = $pdo->query("
    SELECT 
        h.id_hutang,
        h.kode_hutang,
        p.nama_pelanggan,
        h.tanggal_hutang,
        h.total_hutang,
        h.jumlah_terbayar,
        COALESCE(h.sisa_hutang, h.total_hutang - h.jumlah_terbayar) AS sisa_hutang,
        h.status
    FROM hutang h
    INNER JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan
    ORDER BY h.id_hutang DESC
");

$dataHutang = $queryHutang->fetchAll(PDO::FETCH_ASSOC);

$status = $_GET["status"] ?? "";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Hutang - Ca'lontong</title>

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../../assets/css/hutang.css">
</head>
<body>

    <main class="page-wrapper">

        <?php if ($status === "tambah-pelanggan-berhasil"): ?>
            <div class="alert alert-success">
                Data pelanggan berhasil ditambahkan.
            </div>
        <?php elseif ($status === "edit-pelanggan-berhasil"): ?>
            <div class="alert alert-success">
                Data pelanggan berhasil diperbarui.
            </div>
        <?php elseif ($status === "hapus-pelanggan-berhasil"): ?>
            <div class="alert alert-success">
                Data pelanggan berhasil dihapus.
            </div>
        <?php elseif ($status === "hapus-pelanggan-gagal"): ?>
            <div class="alert alert-danger">
                Pelanggan tidak bisa dihapus karena masih memiliki data hutang.
            </div>
        <?php elseif ($status === "tambah-hutang-berhasil"): ?>
            <div class="alert alert-success">
                Data hutang berhasil dicatat.
            </div>
        <?php elseif ($status === "edit-hutang-berhasil"): ?>
            <div class="alert alert-success">
                Data hutang berhasil diperbarui.
            </div>
        <?php elseif ($status === "hapus-hutang-berhasil"): ?>
            <div class="alert alert-success">
                Data hutang berhasil dihapus.
            </div>
        <?php elseif ($status === "hapus-hutang-gagal"): ?>
            <div class="alert alert-danger">
                Data hutang gagal dihapus.
            </div>
        <?php endif; ?>

        <section class="section-content">
            <h5 class="section-title">Daftar Pelanggan</h5>

            <div class="table-responsive table-box">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Nomor</th>
                            <th>Alamat</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($dataPelanggan) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($dataPelanggan as $pelanggan): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= e($pelanggan["nama_pelanggan"]); ?></td>
                                    <td><?= e($pelanggan["no_telepon"]); ?></td>
                                    <td><?= e($pelanggan["alamat"]); ?></td>
                                    <td><?= e($pelanggan["keterangan"]); ?></td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a 
                                                href="edit-pelanggan.php?id=<?= e($pelanggan["id_pelanggan"]); ?>" 
                                                class="btn btn-sm btn-outline-warning btn-action"
                                            >
                                                Edit
                                            </a>

                                            <a 
                                                href="hapus-pelanggan.php?id=<?= e($pelanggan["id_pelanggan"]); ?>" 
                                                class="btn btn-sm btn-outline-danger btn-action"
                                                onclick="return confirm('Apakah kamu yakin ingin menghapus pelanggan ini?')"
                                            >
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="empty-row">
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada data pelanggan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <a href="tambah-pelanggan.php" class="btn btn-success btn-add mt-3">
                + Tambah Pelanggan
            </a>
        </section>

        <section class="section-content mt-4">
            <h5 class="section-title">Daftar Hutang</h5>

            <div class="table-responsive table-box">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Sisa</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($dataHutang) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($dataHutang as $hutang): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= e($hutang["nama_pelanggan"]); ?></td>
                                    <td><?= formatTanggal($hutang["tanggal_hutang"]); ?></td>
                                    <td><?= rupiah($hutang["total_hutang"]); ?></td>
                                    <td><?= rupiah($hutang["sisa_hutang"]); ?></td>
                                    <td>
                                        <span class="status-badge <?= $hutang["status"] === "lunas" ? "status-lunas" : "status-aktif"; ?>">
                                            <?= e(ucfirst($hutang["status"])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a 
                                                href="detail-hutang.php?id=<?= e($hutang["id_hutang"]); ?>" 
                                                class="btn btn-sm btn-outline-primary btn-action"
                                            >
                                                Detail
                                            </a>

                                            <a 
                                                href="edit-hutang.php?id=<?= e($hutang["id_hutang"]); ?>" 
                                                class="btn btn-sm btn-outline-warning btn-action"
                                            >
                                                Edit
                                            </a>

                                            <a 
                                                href="hapus-hutang.php?id=<?= e($hutang["id_hutang"]); ?>" 
                                                class="btn btn-sm btn-outline-danger btn-action"
                                                onclick="return confirm('Apakah kamu yakin ingin menghapus data hutang ini? Stok produk akan dikembalikan.')"
                                            >
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="empty-row">
                                <td colspan="7" class="text-center text-muted">
                                    Belum ada data hutang
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <a href="tambah-hutang.php" class="btn btn-success btn-add mt-3">
                + Tambah Hutang
            </a>

            <div class="mt-2">
                <a href="laporan-hutang.php" class="link-report">
                    Lihat Seluruh Laporan Hutang
                </a>
            </div>
        </section>

    </main>

    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>
</body>
</html>