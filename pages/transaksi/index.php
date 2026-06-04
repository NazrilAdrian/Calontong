<?php
include '../../config.php';
include '../../includes/menu.php';
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
        .qty-value {
            min-width: 20px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-white">
    <main class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 col-xxl-9">
                <h1 class="h5 fw-bold mb-4">Buat transaksi</h1>

                <div class="card border rounded-4 mb-4">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Cari produk</div>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white">Cari</span>
                            <input type="text" class="form-control" placeholder="Ketik nama produk">
                        </div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-semibold">mie goreng</span>
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button" class="btn btn-sm btn-outline-success qty-btn">+</button>
                                    <span class="text-muted">Rp.5000</span>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-semibold">teh botol</span>
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button" class="btn btn-sm btn-outline-success qty-btn">+</button>
                                    <span class="text-muted">Rp.4000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border rounded-4 mb-4">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Keranjang</div>
                        <div class="overflow-auto" style="max-height: 160px;">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-semibold">mie goreng</span>
                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary qty-btn">-</button>
                                        <span class="fw-semibold qty-value">1</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary qty-btn">+</button>
                                        <span class="text-muted">Rp.5000</span>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-semibold">teh botol</span>
                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary qty-btn">-</button>
                                        <span class="fw-semibold qty-value">2</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary qty-btn">+</button>
                                        <span class="text-muted">Rp.8000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">Subtotal</span>
                        <span class="fw-semibold">Rp.13000</span>
                    </div>
                    <div class="mb-3">
                        <label for="uangBayar" class="form-label">Uang pembayaran</label>
                        <input type="number" class="form-control" id="uangBayar" placeholder="Masukkan nominal">
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-semibold">Kembalian</span>
                        <span class="fw-semibold">Rp.0</span>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-success rounded-pill">Selesaikan transaksi</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
