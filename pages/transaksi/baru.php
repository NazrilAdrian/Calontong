<?php
include '../../config.php';
include '../../includes/menu.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ca'lontong - Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-rounded {
            border-radius: 18px;
        }
        .btn-rounded {
            border-radius: 999px;
        }
    </style>
</head>
<body class="bg-white">
    <main class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 col-xxl-9">
                <h1 class="h5 fw-bold mb-4">Riwayat</h1>

                <div class="card border card-rounded mb-4">
                    <div class="card-body">
                        <div class="fw-semibold mb-3">Filter tanggal</div>
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-6">
                                <label for="tanggalMulai" class="form-label">Dari tanggal</label>
                                <input type="date" class="form-control" id="tanggalMulai">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggalSelesai" class="form-label">Sampai tanggal</label>
                                <input type="date" class="form-control" id="tanggalSelesai">
                            </div>
                            <div class="col-12">
                                <div class="d-grid">
                                    <button type="button" class="btn btn-success btn-rounded">Tampilkan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border card-rounded mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Trx</th>
                                        <th>Tgl</th>
                                        <th>Kasir</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>TRX-20260605-001</td>
                                        <td>05/06/2026</td>
                                        <td>Kasir 1</td>
                                        <td class="text-end">Rp 0</td>
                                        <td><span class="badge text-bg-success">Selesai</span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary">Detail</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>TRX-20260605-002</td>
                                        <td>05/06/2026</td>
                                        <td>Kasir 2</td>
                                        <td class="text-end">Rp 0</td>
                                        <td><span class="badge text-bg-secondary">Batal</span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary">Detail</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-success btn-rounded px-4">Transaksi baru</button>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
