<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';

require_role('owner');

$errors = [];
$data = [
    'nama_lengkap' => '',
    'username'     => '',
    'role'         => 'kasir',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_lengkap'] = trim($_POST['nama_lengkap'] ?? '');
    $data['username']     = trim($_POST['username'] ?? '');
    $data['role']         = trim($_POST['role'] ?? '');
    $password             = $_POST['password'] ?? '';
    $konfirmasi           = $_POST['konfirmasi_password'] ?? '';

    // Validasi
    if ($data['nama_lengkap'] === '') {
        $errors['nama_lengkap'] = 'Nama lengkap wajib diisi.';
    }
    if ($data['username'] === '') {
        $errors['username'] = 'Username wajib diisi.';
    } elseif (strlen($data['username']) < 4) {
        $errors['username'] = 'Username minimal 4 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
        $errors['username'] = 'Username hanya boleh huruf, angka, dan underscore.';
    } else {
        // Cek duplikat
        $chk = $conn->prepare('SELECT id_user FROM users WHERE username = ? LIMIT 1');
        $chk->bind_param('s', $data['username']);
        $chk->execute();
        if ($chk->get_result()->fetch_assoc()) {
            $errors['username'] = 'Username sudah digunakan.';
        }
    }
    if ($password === '') {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }
    if ($konfirmasi !== $password) {
        $errors['konfirmasi_password'] = 'Konfirmasi password tidak cocok.';
    }
    if (!in_array($data['role'], ['owner', 'admin', 'kasir'], true)) {
        $errors['role'] = 'Role tidak valid.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            'INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->bind_param('ssss', $data['nama_lengkap'], $data['username'], $hash, $data['role']);
        $stmt->execute();

        set_flash('success', 'Pengguna <strong>' . htmlspecialchars($data['nama_lengkap']) . '</strong> berhasil ditambahkan.');
        redirect('pages/users/index.php');
    }
}

render_page_start('Tambah Pengguna', 'users', ['assets/css/users.css']);
?>

<div class="page-header mb-4">
    <div>
        <a class="btn btn-sm btn-outline-secondary mb-2" href="<?= e(base_url('pages/users/index.php')); ?>">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <h1 class="page-title">Tambah Pengguna Baru</h1>
    </div>
</div>

<div class="panel-card user-form-card">
    <form method="post" novalidate>

        <!-- Nama Lengkap -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
            <input class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : ''; ?>"
                   type="text" id="nama_lengkap" name="nama_lengkap"
                   value="<?= e($data['nama_lengkap']); ?>" placeholder="Contoh: Budi Santoso">
            <?php if (isset($errors['nama_lengkap'])): ?>
                <div class="invalid-feedback"><?= e($errors['nama_lengkap']); ?></div>
            <?php endif; ?>
        </div>

        <!-- Username -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="username">Username <span class="text-danger">*</span></label>
            <input class="form-control <?= isset($errors['username']) ? 'is-invalid' : ''; ?>"
                   type="text" id="username" name="username"
                   value="<?= e($data['username']); ?>" placeholder="Contoh: budi_kasir"
                   autocomplete="off">
            <?php if (isset($errors['username'])): ?>
                <div class="invalid-feedback"><?= e($errors['username']); ?></div>
            <?php else: ?>
                <div class="form-text">Minimal 4 karakter, hanya huruf, angka, dan underscore.</div>
            <?php endif; ?>
        </div>

        <!-- Role -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="role">Role <span class="text-danger">*</span></label>
            <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : ''; ?>"
                    id="role" name="role">
                <option value="kasir"  <?= $data['role'] === 'kasir'  ? 'selected' : ''; ?>>Kasir — Pelayan Transaksi</option>
                <option value="admin"  <?= $data['role'] === 'admin'  ? 'selected' : ''; ?>>Admin — Pengelola Sistem</option>
                <option value="owner"  <?= $data['role'] === 'owner'  ? 'selected' : ''; ?>>Owner — Pemilik Warung</option>
            </select>
            <?php if (isset($errors['role'])): ?>
                <div class="invalid-feedback"><?= e($errors['role']); ?></div>
            <?php endif; ?>
        </div>

        <hr class="my-3">

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="password">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : ''; ?>"
                       type="password" id="password" name="password"
                       placeholder="Minimal 6 karakter" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" id="togglePass" tabindex="-1">
                    <i class="bi bi-eye" id="togglePassIcon"></i>
                </button>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?= e($errors['password']); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-4">
            <label class="form-label fw-semibold" for="konfirmasi_password">Konfirmasi Password <span class="text-danger">*</span></label>
            <input class="form-control <?= isset($errors['konfirmasi_password']) ? 'is-invalid' : ''; ?>"
                   type="password" id="konfirmasi_password" name="konfirmasi_password"
                   placeholder="Ulangi password" autocomplete="new-password">
            <?php if (isset($errors['konfirmasi_password'])): ?>
                <div class="invalid-feedback"><?= e($errors['konfirmasi_password']); ?></div>
            <?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-person-plus-fill me-1"></i>Simpan Pengguna
            </button>
            <a class="btn btn-outline-secondary" href="<?= e(base_url('pages/users/index.php')); ?>">Batal</a>
        </div>
    </form>
</div>

<script>
document.getElementById('togglePass').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon  = document.getElementById('togglePassIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
});
</script>

<?php render_page_end(); ?>
