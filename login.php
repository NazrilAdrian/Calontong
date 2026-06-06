<?php
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['id_user'])) {
    redirect('dashboard.php');
}

$errors = [];
$username = '';

function record_login_attempt($conn, $idUser, $usernameInput, $status, $message)
{
    try {
        $stmt = $conn->prepare('INSERT INTO log_login (id_user, username_input, status, keterangan) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('isss', $idUser, $usernameInput, $status, $message);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        // Login must still work if the audit table has not been initialized yet.
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare('SELECT id_user, nama_lengkap, username, password, role FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !password_verify($password, $user['password'])) {
            $idUser = $user ? (int) $user['id_user'] : null;
            record_login_attempt($conn, $idUser, $username, 'gagal', 'Username atau password salah');
            $errors[] = 'Username atau password salah.';
        } else {
            session_regenerate_id(true);
            $_SESSION['id_user'] = (int) $user['id_user'];
            $_SESSION['nama'] = $user['nama_lengkap'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $idUser = (int) $user['id_user'];
            record_login_attempt($conn, $idUser, $username, 'berhasil', 'Login berhasil');
            redirect('dashboard.php');
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Ca'lontong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/style.css')); ?>" rel="stylesheet">
</head>
<body class="login-body">
    <main class="login-shell">
        <section class="login-card">
            <div class="login-logo">
                <div class="store-icon">
                    <i class="bi bi-shop"></i>
                    <span><i class="bi bi-basket2-fill"></i><i class="bi bi-cup-straw"></i></span>
                </div>
                <div class="brand login-brand"><span>Ca'</span>lontong</div>
            </div>

            <?php if ($errors): ?>
                <div class="alert alert-danger py-2">
                    <?= e($errors[0]); ?>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-2">
                    <label class="form-label login-label" for="username">Username</label>
                    <input class="form-control login-input" type="text" id="username" name="username" value="<?= e($username); ?>" autocomplete="username">
                </div>
                <div class="mb-4">
                    <label class="form-label login-label" for="password">Password</label>
                    <input class="form-control login-input" type="password" id="password" name="password" autocomplete="current-password">
                </div>
                <button class="btn btn-primary w-100 login-button" type="submit">LOGIN</button>
            </form>
        </section>
    </main>
</body>
</html>
