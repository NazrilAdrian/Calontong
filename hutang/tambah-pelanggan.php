<?php
require_once __DIR__ . "/../config/koneksi.php";

$errors = [];

$nama_pelanggan = "";
$no_telepon = "";
$alamat = "";
$keterangan = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama_pelanggan = trim($_POST["nama_pelanggan"] ?? "");
    $no_telepon = trim($_POST["no_telepon"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");
    $keterangan = trim($_POST["keterangan"] ?? "");

    if ($nama_pelanggan === "") {
        $errors[] = "Nama lengkap wajib diisi.";
    }

    if ($no_telepon === "") {
        $errors[] = "Nomor telepon wajib diisi.";
    }

    if ($alamat === "") {
        $errors[] = "Alamat wajib diisi.";
    }

    if (empty($errors)) {
        $query = $pdo->prepare("
            INSERT INTO pelanggan 
            (
                nama_pelanggan,
                no_telepon,
                alamat,
                keterangan
            ) 
            VALUES 
            (
                :nama_pelanggan,
                :no_telepon,
                :alamat,
                :keterangan
            )
        ");

        $query->execute([
            ":nama_pelanggan" => $nama_pelanggan,
            ":no_telepon" => $no_telepon,
            ":alamat" => $alamat,
            ":keterangan" => $keterangan
        ]);

        header("Location: manajemen-hutang.php?status=tambah-pelanggan-berhasil");
        exit;
    }
}

function e($value) {
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan - Ca'lontong</title>

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/css/hutang.css">
</head>
<body>

    <main class="page-wrapper">
        <section class="form-section">

            <h4 class="page-title">Tambah Pelanggan</h4>

            <div class="form-card">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?= e($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">

                    <div class="mb-3">
                        <label for="nama_pelanggan" class="form-label">
                            Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="nama_pelanggan" 
                            name="nama_pelanggan"
                            placeholder="Masukkan nama lengkap"
                            value="<?= e($nama_pelanggan); ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="no_telepon" class="form-label">
                            Nomor Telepon
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="no_telepon" 
                            name="no_telepon"
                            placeholder="Masukkan nomor telepon"
                            value="<?= e($no_telepon); ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">
                            Alamat
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="alamat" 
                            name="alamat"
                            placeholder="Masukkan alamat"
                            value="<?= e($alamat); ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">
                            Keterangan
                        </label>
                        <textarea 
                            class="form-control textarea-keterangan" 
                            id="keterangan" 
                            name="keterangan"
                            placeholder="Masukkan keterangan"
                        ><?= e($keterangan); ?></textarea>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-6">
                            <a href="manajemen-hutang.php" class="btn btn-outline-secondary w-100">
                                Batal
                            </a>
                        </div>

                        <div class="col-6">
                            <button type="submit" class="btn btn-success w-100">
                                Tambah
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </section>
    </main>

    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>
</body>
</html>