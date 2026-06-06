<?php
require_once __DIR__ . "/../../includes/auth_check.php";
require_once __DIR__ . "/../../includes/sidebar.php";

require_role(['owner', 'admin']);

$id_pelanggan = $_GET["id"] ?? null;

if (!$id_pelanggan || !is_numeric($id_pelanggan)) {
    header("Location: manajemen-hutang.php");
    exit;
}

$id_pelanggan = (int) $id_pelanggan;

$query = $conn->prepare("
    SELECT 
        id_pelanggan,
        nama_pelanggan,
        no_telepon,
        alamat,
        keterangan
    FROM pelanggan
    WHERE id_pelanggan = ?
");

$query->bind_param("i", $id_pelanggan);
$query->execute();
$pelanggan = $query->get_result()->fetch_assoc();

if (!$pelanggan) {
    header("Location: manajemen-hutang.php");
    exit;
}

$errors = [];

$nama_pelanggan = $pelanggan["nama_pelanggan"];
$no_telepon = $pelanggan["no_telepon"];
$alamat = $pelanggan["alamat"];
$keterangan = $pelanggan["keterangan"];

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
        $update = $conn->prepare("
            UPDATE pelanggan
            SET 
                nama_pelanggan = ?,
                no_telepon = ?,
                alamat = ?,
                keterangan = ?
            WHERE id_pelanggan = ?
        ");

        $update->bind_param("ssssi", $nama_pelanggan, $no_telepon, $alamat, $keterangan, $id_pelanggan);
        $update->execute();

        header("Location: manajemen-hutang.php?status=edit-pelanggan-berhasil");
        exit;
    }
}
?>

<?php render_page_start('Edit Pelanggan', 'hutang', ['assets/css/hutang.css']); ?>

<div class="page-wrapper">
    <section class="form-section">

        <h4 class="page-title">Edit Pelanggan</h4>

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
                            Simpan
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </section>
</div>

<?php render_page_end(); ?>
