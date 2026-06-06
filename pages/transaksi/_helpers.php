<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$configPath = __DIR__ . '/../../config.php';
if (file_exists($configPath)) {
    include_once $configPath;
}

function calontong_db()
{
    global $conn;

    if (isset($conn) && $conn instanceof mysqli) {
        return $conn;
    }

    return null;
}

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah($value)
{
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
}

function current_user_id()
{
    if (isset($_SESSION['id_user'])) {
        return (int) $_SESSION['id_user'];
    }

    if (isset($_SESSION['user_id'])) {
        return (int) $_SESSION['user_id'];
    }

    if (defined('CALONTONG_DEV_USER_ID')) {
        return (int) CALONTONG_DEV_USER_ID;
    }

    return 0;
}

function current_role()
{
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }

    if (defined('CALONTONG_DEV_ROLE')) {
        return CALONTONG_DEV_ROLE;
    }

    return '';
}

function is_manager_role()
{
    return in_array(current_role(), ['owner', 'admin'], true);
}

function flash($type, $message)
{
    $_SESSION['flash'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function take_flash()
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

function redirect_to($path)
{
    header('Location: ' . $path);
    exit;
}

function bind_params($stmt, $types, $params)
{
    if ($types === '' || empty($params)) {
        return;
    }

    $refs = [];
    foreach ($params as $key => $value) {
        $refs[$key] = $value;
    }

    $args = [$types];
    foreach ($refs as $key => &$value) {
        $args[] = &$value;
    }

    call_user_func_array([$stmt, 'bind_param'], $args);
}

function fetch_all($sql, $types = '', $params = [])
{
    $conn = calontong_db();
    if (!$conn) {
        return [];
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    bind_params($stmt, $types, $params);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    return $rows;
}

function fetch_one($sql, $types = '', $params = [])
{
    $rows = fetch_all($sql, $types, $params);

    return $rows[0] ?? null;
}

function execute_query($sql, $types = '', $params = [])
{
    $conn = calontong_db();
    if (!$conn) {
        return false;
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    bind_params($stmt, $types, $params);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function cart_items()
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    return $_SESSION['cart'];
}

function set_cart_items($items)
{
    $_SESSION['cart'] = $items;
}

function cart_total()
{
    $total = 0;
    foreach (cart_items() as $item) {
        $total += (float) $item['subtotal'];
    }

    return $total;
}

function transaction_code()
{
    $today = date('Ymd');
    $row = fetch_one(
        "SELECT COUNT(*) AS total FROM transaksi WHERE DATE(created_at) = CURDATE()"
    );
    $sequence = ((int) ($row['total'] ?? 0)) + 1;

    return 'TRX-' . $today . '-' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
}
