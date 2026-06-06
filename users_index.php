<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';

// Hanya owner yang boleh akses
require_role('owner');

// Filter & search
$search   = trim($_GET['q'] ?? '');
$filterRole = $_GET['role'] ?? '';
$validRoles = ['owner', 'admin', 'kasir'];

$conditions = ['1=1'];
$params     = [];
$types      = '';

if ($search !== '') {
    $conditions[] = '(nama_lengkap LIKE ? OR username LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

if (in_array($filterRole, $validRoles, true)) {
    $conditions[] = 'role = ?';
    $params[] = $filterRole;
    $types   .= 's';
}

$where = implode(' AND ', $conditions);
$sql   = "SELECT id_user, nama_lengkap, username, role, created_at FROM users WHERE $where ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Statistik cepat
$totalUsers  = (int) $conn->query('SELECT COUNT(*) FROM users')->fetch_row()[0];
$totalOwner  = (int) $conn->query("SELECT COUNT(*) FROM users WHERE role='owner'")->fetch_row()[0];
$totalAdmin  = (int) $conn->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0];
$totalKasir  = (int) $conn->query("SELECT COUNT(*) FROM users WHERE role='kasir'")->fetch_row()[0];

render_page_start('Manajemen Pengguna', 'users');
?>

<div class="page-header mb-4">
    <div>
        <h1 class="page-title">Manajemen Pengguna</h1>
        <p class="page-subtitle text-muted">Kelola akun dan hak akses seluruh pengguna sistem</p>
    </div>
    <a class="btn btn-primary" href="<?= e(base_url('pages/users/create.php')); ?>">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
    </a>
</div>

<!-- Statistik -->
<div class="stats-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(150px,1fr));">
    <article class="stat-card green">
        <span class="stat-icon"><i class="bi bi-people-fill"></i></span>
        <div><h2>Total Pengguna</h2><strong><?= e($totalUsers); ?></strong></div>
    </article>
    <article class="stat-card green">
        <span class="stat-icon"><i class="bi bi-person-badge-fill"></i></span>
        <div><h2>Owner</h2><strong><?= e($totalOwner); ?></strong></div>
    </article>
    <article class="stat-card green">
        <span class="stat-icon"><i class="bi bi-person-gear"></i></span>
        <div><h2>Admin</h2><strong><?= e($totalAdmin); ?></strong></div>
    </article>
    <article class="stat-card green">
        <span class="stat-icon"><i class="bi bi-person-workspace"></i></span>
        <div><h2>Kasir</h2><strong><?= e($totalKasir); ?></strong></div>
    </article>
</div>

<!-- Filter & Search -->
<div class="panel-card mb-4">
    <form method="get" class="row g-2 align-items-end">
        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="q">Cari Pengguna</label>
            <div class="input-icon">
                <i class="bi bi-search"></i>
                <input class="form-control" type="search" id="q" name="q"
                       placeholder="Nama atau username..." value="<?= e($search); ?>">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label fw-semibold" for="role">Filter Role</label>
            <select class="form-select" id="role" name="role">
                <option value="">Semua Role</option>
                <?php foreach ($validRoles as $r): ?>
                    <option value="<?= e($r); ?>" <?= $filterRole === $r ? 'selected' : ''; ?>>
                        <?= e(ucfirst($r)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">
                <i class="bi bi-funnel-fill me-1"></i>Filter
            </button>
            <?php if ($search !== '' || $filterRole !== ''): ?>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('pages/users/index.php')); ?>">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Pengguna -->
<div class="panel-card">
    <?php if (empty($users)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-person-slash" style="font-size:2.5rem;"></i>
            <p class="mt-2">Tidak ada pengguna yang ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:3rem;">#</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-center" style="width:10rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $i => $u): ?>
                        <tr>
                            <td class="text-muted small"><?= e($i + 1); ?></td>
                            <td class="fw-semibold"><?= e($u['nama_lengkap']); ?></td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border">
                                    <?= e($u['username']); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $roleBadge = [
                                    'owner' => 'text-bg-warning',
                                    'admin' => 'text-bg-primary',
                                    'kasir' => 'text-bg-success',
                                ];
                                $badgeClass = $roleBadge[$u['role']] ?? 'text-bg-secondary';
                                ?>
                                <span class="badge <?= e($badgeClass); ?>">
                                    <?= e(strtoupper($u['role'])); ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?= e(date('d M Y', strtotime($u['created_at']))); ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="<?= e(base_url('pages/users/edit.php?id=' . $u['id_user'])); ?>"
                                       title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <?php if ((int)$u['id_user'] !== (int)$_SESSION['id_user']): ?>
                                        <form method="post" action="<?= e(base_url('pages/users/delete.php')); ?>"
                                              onsubmit="return confirm('Hapus pengguna <?= e(addslashes($u['nama_lengkap'])); ?>? Aksi ini tidak bisa dibatalkan.')">
                                            <input type="hidden" name="id_user" value="<?= e($u['id_user']); ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Hapus">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak bisa hapus akun sendiri">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-2 text-muted small px-1">
            Menampilkan <?= count($users); ?> dari <?= e($totalUsers); ?> pengguna.
        </div>
    <?php endif; ?>
</div>

<?php render_page_end(); ?>
