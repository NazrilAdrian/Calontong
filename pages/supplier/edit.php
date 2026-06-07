<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../includes/module4_helpers.php';
require_role(['owner', 'admin']);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'Supplier tidak valid.');
    redirect('pages/restock/index.php#daftar-supplier');
}

$stmt = $conn->prepare('SELECT * FROM supplier WHERE id_supplier = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    set_flash('danger', 'Supplier tidak ditemukan.');
    redirect('pages/restock/index.php#daftar-supplier');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_supplier'] = trim($_POST['nama_supplier'] ?? '');
    $data['nama_kontak'] = trim($_POST['nama_kontak'] ?? '');
    $data['no_telepon'] = trim($_POST['no_telepon'] ?? '');
    $data['alamat'] = trim($_POST['alamat'] ?? '');

    if ($data['nama_supplier'] === '') {
        $errors[] = 'Nama supplier wajib diisi.';
    } else {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM supplier WHERE nama_supplier = ? AND id_supplier <> ?');
        $stmt->bind_param('si', $data['nama_supplier'], $id);
        $stmt->execute();

        if ((int) ($stmt->get_result()->fetch_row()[0] ?? 0) > 0) {
            $errors[] = 'Supplier sudah terdaftar.';
        }
    }

    if (!$errors) {
        $stmt = $conn->prepare('
            UPDATE supplier
            SET nama_supplier = ?, nama_kontak = ?, no_telepon = ?, alamat = ?
            WHERE id_supplier = ?
        ');
        $stmt->bind_param('ssssi', $data['nama_supplier'], $data['nama_kontak'], $data['no_telepon'], $data['alamat'], $id);
        $stmt->execute();

        set_flash('success', 'Supplier berhasil diperbarui.');
        redirect('pages/restock/index.php#daftar-supplier');
    }
}

render_page_start('Edit Supplier', 'supplier', ['assets/css/supplier-restock.css']);
?>
<section class="form-section">
    <h1 class="form-title">Edit Supplier</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e($errors[0]); ?></div>
    <?php endif; ?>

    <form class="module-form" method="post" novalidate>
        <label class="form-label" for="nama_supplier">Nama Agen/ Toko/ Supplier</label>
        <div class="input-icon mb-3">
            <i class="bi bi-building"></i>
            <input class="form-control" id="nama_supplier" name="nama_supplier" value="<?= e($data['nama_supplier']); ?>" placeholder="Masukkan nama supplier">
        </div>

        <label class="form-label" for="nama_kontak">Kontak Person</label>
        <div class="input-icon mb-3">
            <i class="bi bi-person"></i>
            <input class="form-control" id="nama_kontak" name="nama_kontak" value="<?= e($data['nama_kontak']); ?>" placeholder="Nama kontak">
        </div>

        <label class="form-label" for="no_telepon">No Telepon/ WA</label>
        <div class="input-icon mb-3">
            <i class="bi bi-telephone"></i>
            <input class="form-control" id="no_telepon" name="no_telepon" value="<?= e($data['no_telepon']); ?>" placeholder="Nomor telepon atau WhatsApp">
        </div>

        <label class="form-label" for="alamat">Alamat Lengkap</label>
        <div class="textarea-icon mb-4">
            <i class="bi bi-geo-alt"></i>
            <textarea class="form-control" id="alamat" name="alamat" rows="6" placeholder="Masukkan alamat lengkap"><?= e($data['alamat']); ?></textarea>
        </div>

        <div class="form-actions">
            <a class="btn btn-outline-secondary" href="<?= e(base_url('pages/restock/index.php#daftar-supplier')); ?>">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
    </form>
</section>
<?php render_page_end(); ?>
