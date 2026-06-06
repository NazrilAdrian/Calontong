<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_role(['owner', 'admin']);

$errors = [];
$data = [
    'nama_kategori' => '',
    'deskripsi' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_kategori'] = trim($_POST['nama_kategori'] ?? '');
    $data['deskripsi'] = trim($_POST['deskripsi'] ?? '');

    if ($data['nama_kategori'] === '') {
        $errors[] = 'Nama kategori wajib diisi.';
    } else {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM kategori WHERE nama_kategori = ?');
        $stmt->bind_param('s', $data['nama_kategori']);
        $stmt->execute();

        if ((int) ($stmt->get_result()->fetch_row()[0] ?? 0) > 0) {
            $errors[] = 'Kategori sudah terdaftar.';
        }
    }

    if (!$errors) {
        $stmt = $conn->prepare('INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)');
        $stmt->bind_param('ss', $data['nama_kategori'], $data['deskripsi']);
        $stmt->execute();

        set_flash('success', 'Kategori berhasil ditambahkan.');
        redirect('pages/produk/index.php#kategori');
    }
}

render_page_start('Tambah Kategori', 'produk', ['assets/css/produk.css']);
?>
<section class="form-section">
    <h1 class="form-title">Tambah Kategori</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e($errors[0]); ?></div>
    <?php endif; ?>

    <form class="module-form" method="post" novalidate>
        <label class="form-label" for="nama_kategori">Nama Kategori</label>
        <div class="input-icon mb-3">
            <i class="bi bi-grid"></i>
            <input class="form-control" id="nama_kategori" name="nama_kategori" value="<?= e($data['nama_kategori']); ?>" placeholder="Masukkan nama Kategori">
        </div>

        <label class="form-label" for="deskripsi">Deskripsi Kategori</label>
        <div class="textarea-icon mb-4">
            <i class="bi bi-list"></i>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="7" placeholder="Masukkan Deskripsi Kategori..."><?= e($data['deskripsi']); ?></textarea>
        </div>

        <div class="form-actions">
            <a class="btn btn-outline-secondary" href="<?= e(base_url('pages/produk/index.php#kategori')); ?>">Batal</a>
            <button class="btn btn-primary" type="submit">Tambah</button>
        </div>
    </form>
</section>
<?php render_page_end(); ?>
