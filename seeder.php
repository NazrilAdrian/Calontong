<?php
require_once __DIR__ . '/config.php';

$conn->query("
    CREATE TABLE IF NOT EXISTS log_login (
        id_log INT PRIMARY KEY AUTO_INCREMENT,
        id_user INT DEFAULT NULL,
        username_input VARCHAR(50) NOT NULL,
        waktu_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('berhasil','gagal') NOT NULL,
        keterangan VARCHAR(100) DEFAULT NULL,
        FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$accounts = [
    ['Owner Ca-lontong', 'owner', 'owner123', 'owner'],
    ['Admin Ca-lontong', 'admin', 'admin123', 'admin'],
    ['Kasir Ca-lontong', 'kasir', 'kasir123', 'kasir'],
];

$inserted = [];
$skipped = [];

foreach ($accounts as $account) {
    [$name, $username, $password, $role] = $account;

    $stmt = $conn->prepare('SELECT id_user FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();

    if ($exists) {
        $skipped[] = $username;
        continue;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $name, $username, $hash, $role);
    $stmt->execute();
    $inserted[] = $username;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seeder - Ca'lontong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/style.css')); ?>" rel="stylesheet">
</head>
<body class="seed-body">
    <main class="seed-card">
        <h1>Seeder selesai</h1>
        <p>Akun awal sudah dicek dan dibuat jika belum tersedia.</p>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Owner</td><td>owner</td><td>owner123</td></tr>
                    <tr><td>Admin</td><td>admin</td><td>admin123</td></tr>
                    <tr><td>Kasir</td><td>kasir</td><td>kasir123</td></tr>
                </tbody>
            </table>
        </div>
        <?php if ($inserted): ?>
            <div class="alert alert-success">Dibuat: <?= e(implode(', ', $inserted)); ?></div>
        <?php endif; ?>
        <?php if ($skipped): ?>
            <div class="alert alert-info">Sudah ada: <?= e(implode(', ', $skipped)); ?></div>
        <?php endif; ?>
        <a class="btn btn-primary" href="<?= e(base_url('login.php')); ?>">Ke Login</a>
    </main>
</body>
</html>
