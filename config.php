<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'calontong';

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Koneksi database gagal. Pastikan database calontong sudah dibuat dan MySQL aktif.');
}

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', __DIR__);
}

if (!defined('BASE_URL')) {
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $projectRoot = realpath(PROJECT_ROOT);
    $baseUrl = '';

    if ($documentRoot && $projectRoot) {
        $documentRoot = str_replace('\\', '/', $documentRoot);
        $projectRoot = str_replace('\\', '/', $projectRoot);

        if (strpos($projectRoot, $documentRoot) === 0) {
            $baseUrl = '/' . trim(substr($projectRoot, strlen($documentRoot)), '/');
        }
    }

    define('BASE_URL', rtrim($baseUrl, '/'));
}

function base_url($path = '')
{
    $path = ltrim($path, '/');
    return BASE_URL . ($path !== '' ? '/' . $path : '');
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect($path)
{
    header('Location: ' . base_url($path));
    exit;
}

function set_flash($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash()
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function format_rupiah($amount)
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function current_user_role()
{
    return $_SESSION['role'] ?? null;
}

function is_owner_or_admin()
{
    return in_array(current_user_role(), ['owner', 'admin'], true);
}

function current_user_id()
{
    if (isset($_SESSION['id_user'])) {
        return (int) $_SESSION['id_user'];
    }

    if (isset($_SESSION['user_id'])) {
        return (int) $_SESSION['user_id'];
    }

    return 0;
}

function redirect_to($path)
{
    header('Location: ' . $path);
    exit;
}

function add_flash($type, $message)
{
    if (
        !isset($_SESSION['flash']) ||
        !is_array($_SESSION['flash']) ||
        (isset($_SESSION['flash']['type']) && isset($_SESSION['flash']['message']))
    ) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function take_flash()
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    if (empty($flash)) {
        return [];
    }

    if (isset($flash['type']) && isset($flash['message'])) {
        return [$flash];
    }

    $messages = [];

    foreach ($flash as $item) {
        if (is_array($item) && isset($item['type'], $item['message'])) {
            $messages[] = $item;
        }
    }

    return $messages;
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
    global $conn;
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
    global $conn;
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
?>
