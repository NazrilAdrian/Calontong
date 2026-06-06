<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_role(['owner', 'admin']);

$errors = [];
$data = [
    'kode_produk' => '',
    'nama_produk' => '',
    'id_kategori' => '',
    'harga_beli' => '',
    'harga_jual' => '',
    'stok' => '',
    'stok_minimum' => '5',
    'satuan' => 'pcs',
];

$kategoriResult = $conn->query('SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC');
$kategoriOptions = $kategoriResult->fetch_all(MYSQLI_ASSOC);

if (!$kategoriOptions) {
    set_flash('warning', 'Tambahkan kategori terlebih dahulu sebelum membuat produk.');
    redirect('pages/kategori/tambah.php');
}

function product_decimal($value)
{
    return (float) str_replace(',', '.', trim((string) $value));
}

function product_int($value)
{
    return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $default) {
        $data[$key] = trim($_POST[$key] ?? '');
    }

    $idKategori = (int) $data['id_kategori'];
    $hargaBeli = product_decimal($data['harga_beli']);
    $hargaJual = product_decimal($data['harga_jual']);
    $stok = product_int($data['stok']);
    $stokMinimum = product_int($data['stok_minimum']);

    if ($data['nama_produk'] === '') {
        $errors[] = 'Nama produk wajib diisi.';
    }

    $stmt = $conn->prepare('SELECT COUNT(*) FROM kategori WHERE id_kategori = ?');
    $stmt->bind_param('i', $idKategori);
    $stmt->execute();
    if ((int) ($stmt->get_result()->fetch_row()[0] ?? 0) === 0) {
        $errors[] = 'Kategori produk wajib dipilih.';
    }

    if ($data['harga_beli'] === '' || $data['harga_jual'] === '' || $hargaBeli < 0 || $hargaJual < 0 || $stok === false || $stokMinimum === false) {
        $errors[] = 'Harga, stok, dan stok minimum wajib berupa nilai tidak negatif.';
    }

    if ($data['kode_produk'] !== '') {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM produk WHERE kode_produk = ?');
        $stmt->bind_param('s', $data['kode_produk']);
        $stmt->execute();
        if ((int) ($stmt->get_result()->fetch_row()[0] ?? 0) > 0) {
            $errors[] = 'Kode produk sudah digunakan.';
        }
    }

    if (!$errors) {
        $kodeProduk = $data['kode_produk'] !== '' ? $data['kode_produk'] : null;
        $stmt = $conn->prepare('
            INSERT INTO produk (id_kategori, kode_produk, nama_produk, harga_beli, harga_jual, stok, stok_minimum, satuan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->bind_param('issddiis', $idKategori, $kodeProduk, $data['nama_produk'], $hargaBeli, $hargaJual, $stok, $stokMinimum, $data['satuan']);
        $stmt->execute();

        set_flash('success', 'Produk berhasil ditambahkan.');
        redirect('pages/produk/index.php');
    }
}

render_page_start('Tambah Produk', 'produk', ['assets/css/produk.css']);
?>
<section class="form-section">
    <h1 class="form-title">Tambah Produk</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= e($errors[0]); ?></div>
    <?php endif; ?>

    <form class="module-form" method="post" novalidate>
        <label class="form-label" for="nama_produk">Nama Produk</label>
        <div class="input-icon mb-3">
            <i class="bi bi-tag"></i>
            <input class="form-control" id="nama_produk" name="nama_produk" value="<?= e($data['nama_produk']); ?>" placeholder="Masukkan nama produk">
        </div>

        <label class="form-label" for="id_kategori">Kategori Produk</label>
        <div class="select-icon mb-3">
            <i class="bi bi-grid"></i>
            <select class="form-select" id="id_kategori" name="id_kategori">
                <option value="">Pilih Kategori Produk</option>
                <?php foreach ($kategoriOptions as $kategori): ?>
                    <option value="<?= e($kategori['id_kategori']); ?>" <?= (string) $data['id_kategori'] === (string) $kategori['id_kategori'] ? 'selected' : ''; ?>>
                        <?= e($kategori['nama_kategori']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label" for="harga_beli">Harga Beli</label>
                <div class="input-icon mb-3">
                    <i class="bi bi-currency-dollar"></i>
                    <input class="form-control" type="number" min="0" step="0.01" id="harga_beli" name="harga_beli" value="<?= e($data['harga_beli']); ?>" placeholder="Masukkan Harga Beli">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="harga_jual">Harga Jual</label>
                <div class="input-icon mb-3">
                    <i class="bi bi-currency-dollar"></i>
                    <input class="form-control" type="number" min="0" step="0.01" id="harga_jual" name="harga_jual" value="<?= e($data['harga_jual']); ?>" placeholder="Masukkan Harga Jual">
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label" for="stok">Stok</label>
                <div class="input-icon mb-3">
                    <i class="bi bi-box"></i>
                    <input class="form-control" type="number" min="0" id="stok" name="stok" value="<?= e($data['stok']); ?>" placeholder="Masukkan stok">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="stok_minimum">Stok Minimum</label>
                <div class="input-icon mb-3">
                    <i class="bi bi-exclamation-circle"></i>
                    <input class="form-control" type="number" min="0" id="stok_minimum" name="stok_minimum" value="<?= e($data['stok_minimum']); ?>" placeholder="Batas stok kritis">
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label" for="satuan">Satuan</label>
                <div class="input-icon mb-3">
                    <i class="bi bi-rulers"></i>
                    <input class="form-control" id="satuan" name="satuan" value="<?= e($data['satuan']); ?>" placeholder="pcs, bungkus, liter">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="kode_produk">Kode Produk</label>
                <div class="input-icon mb-3">
                    <i class="bi bi-upc-scan"></i>
                    <input class="form-control" id="kode_produk" name="kode_produk" value="<?= e($data['kode_produk']); ?>" placeholder="Opsional">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a class="btn btn-outline-secondary" href="<?= e(base_url('pages/produk/index.php')); ?>">Batal</a>
            <button class="btn btn-primary" type="submit">Tambah</button>
        </div>
    </form>
</section>
<?php render_page_end(); ?>
