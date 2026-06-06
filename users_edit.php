<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';

require_role('owner');

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'ID pengguna tidak valid.');
    redirect('pages/users/index.php');
}

// Ambil data user yang akan diedit
$stmt = $conn->prepare('SELECT id_user, nama_lengkap, username, role FROM users WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    set_flash('danger', 'Pengguna tidak ditemukan.');
    redirect('pages/users/index.php');
}

$errors = [];
$data = [
    'nama_lengkap' => $user['nama_lengkap'],
    'username'     => $user['username'],
    'role'         => $user['role'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_lengkap'] = trim($_POST['nama_lengkap'] ?? '');
    $data['username']     = trim($_POST['username'] ?? '');
    $data['role']         = trim($_POST['role'] ?? '');
    $password             = $_POST['password'] ?? '';
    $konfirmasi           = $_POST['konfirmasi_password'] ?? '';
    $ubahPassword         = !empty($password) || !empty($konfirmasi);

    // Validasi nama
    if ($data['nama_lengkap'] === '') {
        $errors['nama_lengkap'] = 'Nama lengkap wajib diisi.';
    }

    // Validasi username
    if ($data['username'] === '') {
        $errors['username'] = 'Username wajib diisi.';
    } elseif (strlen($data['username']) < 4) {
        $errors['username'] = 'Username minimal 4 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
        $errors['username'] = 'Username hanya boleh huruf, angka, dan underscore.';
    } else {
        // Cek duplikat, kecuali user ini sendiri
        $chk = $conn->prepare('SELECT id_user FROM users WHERE username = ? AND id_user != ? LIMIT 1');
        $chk->bind_param('si', $data['username'], $id);
        $chk->execute();
        if ($chk->get_result()->fetch_assoc()) {
            $errors['username'] = 'Username sudah digunakan pengguna lain.';
        }
    }

    // Validasi role
    if (!in_array($data['role'], ['owner', 'admin', 'kasir'], true)) {
        $errors['role'] = 'Role tidak valid.';
    }

    // Validasi password (hanya jika diisi)
    if ($ubahPassword) {
        if (strlen($password) < 6) {
            $errors['password'] = 'Password minimal 6 karakter.';
        }
        if ($konfirmasi !== $password) {
            $errors['konfirmasi_password'] = 'Konfirmasi password tidak cocok.';
        }
    }

    if (empty($errors)) {
        if ($ubahPassword) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                'UPDATE users SET nama_lengkap=?, username=?, role=?, password=? WHERE id_user=?'
            );
            $stmt->bind_param('ssssi', $data['nama_lengkap'], $data['username'], $data['role'], $hash, $id);
        } else {
            $stmt = $conn->prepare(
                'UPDATE users SET nama_lengkap=?, username=?, role=? WHERE id_user=?'
            );
            $stmt->bind_param('sssi', $data['nama_lengkap'], $data['username'], $data['role'], $id);
        }
        $stmt->execute();

        // Jika user mengedit akunnya sendiri, update session
        if ((int) $_SESSION['id_user'] === $id) {
            $_SESSION['nama']     = $data['nama_lengkap'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role']     = $data['role'];
        }

        set_flash('success', 'Data pengguna <strong>' . htmlspecialchars($data['nama_lengkap']) . '</strong> berhasil diperbarui.');
        redirect('pages/users/index.php');
    }
}

render_page_start('Edit Pengguna', 'users');
?>

<div class="page-header mb-4">
    <div>
        <a class="btn btn-sm btn-outline-secondary mb-2" href="<?= e(base_url('pages/users/index.php')); ?>">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <h1 class="page-title">Edit Pengguna</h1>
        <p class="page-subtitle text-muted">Memperbarui data untuk: <strong><?= e($user['username']); ?></strong></p>
    </div>
</div>

<div class="panel-card" style="max-width:540px;">
    <form method="post" novalidate>

        <!-- Nama Lengkap -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
            <input class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : ''; ?>"
                   type="text" id="nama_lengkap" name="nama_lengkap"
                   value="<?= e($data['nama_lengkap']); ?>">
            <?php if (isset($errors['nama_lengkap'])): ?>
                <div class="invalid-feedback"><?= e($errors['nama_lengkap']); ?></div>
            <?php endif; ?>
        </div>

        <!-- Username -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="username">Username <span class="text-danger">*</span></label>
            <input class="form-control <?= isset($errors['username']) ? 'is-invalid' : ''; ?>"
                   type="text" id="username" name="username"
                   value="<?= e($data['username']); ?>" autocomplete="off">
            <?php if (isset($errors['username'])): ?>
                <div class="invalid-feedback"><?= e($errors['username']); ?></div>
            <?php endif; ?>
        </div>

        <!-- Role -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="role">Role <span class="text-danger">*</span></label>
            <?php if ((int)$_SESSION['id_user'] === $id): ?>
                <!-- Owner tidak boleh mengubah role sendiri -->
                <input type="hidden" name="role" value="<?= e($data['role']); ?>">
                <input class="form-control" type="text" value="<?= e(ucfirst($data['role'])); ?>" disabled>
                <div class="form-text text-warning"><i class="bi bi-info-circle me-1"></i>Anda tidak bisa mengubah role akun sendiri.</div>
            <?php else: ?>
                <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : ''; ?>" id="role" name="role">
                    <option value="kasir" <?= $data['role'] === 'kasir' ? 'selected' : ''; ?>>Kasir — Pelayan Transaksi</option>
                    <option value="admin" <?= $data['role'] === 'admin' ? 'selected' : ''; ?>>Admin — Pengelola Sistem</option>
                    <option value="owner" <?= $data['role'] === 'owner' ? 'selected' : ''; ?>>Owner — Pemilik Warung</option>
                </select>
                <?php if (isset($errors['role'])): ?>
                    <div class="invalid-feedback"><?= e($errors['role']); ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <hr class="my-3">
        <p class="text-muted small mb-3">
            <i class="bi bi-shield-lock me-1"></i>
            Kosongkan kolom password jika tidak ingin mengubahnya.
        </p>

        <!-- Password Baru -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="password">Password Baru</label>
            <div class="input-group">
                <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : ''; ?>"
                       type="password" id="password" name="password"
                       placeholder="Isi jika ingin ganti password" autocomplete="new-password">
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
            <label class="form-label fw-semibold" for="konfirmasi_password">Konfirmasi Password Baru</label>
            <input class="form-control <?= isset($errors['konfirmasi_password']) ? 'is-invalid' : ''; ?>"
                   type="password" id="konfirmasi_password" name="konfirmasi_password"
                   placeholder="Ulangi password baru" autocomplete="new-password">
            <?php if (isset($errors['konfirmasi_password'])): ?>
                <div class="invalid-feedback"><?= e($errors['konfirmasi_password']); ?></div>
            <?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-floppy-fill me-1"></i>Simpan Perubahan
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
