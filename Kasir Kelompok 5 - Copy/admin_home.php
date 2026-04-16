<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'pemesanan';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Koneksi Gagal: ' . $conn->connect_error);
}

// Ambil data produk
$products = [];
$result = $conn->query("SELECT * FROM products");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Hapus produk
if (isset($_GET['delete_product_id'])) {
    $product_id = (int)$_GET['delete_product_id'];
    $conn->query("DELETE FROM products WHERE id = $product_id");
    header("Location: admin_home.php");
    exit;
}

// Ambil data pesanan
$orders = [];
$order_result = $conn->query("SELECT * FROM orders ORDER BY order_time DESC");
while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}

// Hitung total income
$total_income = 0;
foreach ($orders as $order) {
    $total_income += $order['total'];
}

// Hitung jumlah barang terjual
$product_sales = [];
$product_result = $conn->query("SELECT item, SUM(quantity) AS total_quantity 
                                FROM orders GROUP BY item");

while ($row = $product_result->fetch_assoc()) {
    $product_sales[$row['item']] = $row['total_quantity'];
}

// Reset transaksi
if (isset($_POST['reset_orders'])) {
    $conn->query("DELETE FROM orders");
    $_SESSION['error'] = "Data transaksi berhasil direset.";
    header("Location: admin_home.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Halaman Utama Admin</title>
    <link rel="stylesheet" href="admin_home.css">
</head>
<body>

<?php
if (isset($_SESSION['error'])) {
    echo "<p class='error'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
?>
<div class="container">
<h1>Selamat Datang, Admin</h1>

<p>
    <a href="menu.php">Menu / Kasir</a> |
    <a href="add_product.php">Tambah Produk</a> |
    <a href="logout.php">Logout</a>
</p>

<hr>

<h2>Daftar Produk</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Nama Produk</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
    </tr>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?php echo $product['id']; ?></td>
        <td><?php echo htmlspecialchars($product['name']); ?></td>
        <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
        <td><?php echo $product['stock']; ?></td>
        <td>
            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="ubah">Ubah</a> |
            <a href="admin_home.php?delete_product_id=<?php echo $product['id']; ?>" 
               onclick="return confirm('Yakin ingin menghapus produk ini?')" class="hapus">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<hr>

<h2>Jumlah Barang Terjual per Produk</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>Nama Produk</th>
        <th>Jumlah Terjual</th>
    </tr>
    <?php foreach ($product_sales as $product_name => $quantity): ?>
    <tr>
        <td><?php echo htmlspecialchars($product_name); ?></td>
        <td><?php echo $quantity; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<p><strong>Total Uang yang Diperoleh:</strong> Rp <?php echo number_format($total_income, 0, ',', '.'); ?></p>

<hr>

<h2>Laporan Penjualan - Daftar Pesanan</h2>
<p><a href="print_laporan.php" target="_blank">Cetak Laporan</a></p>

<table border="1" cellpadding="5">
    <tr>
        <th>Nama Produk</th>
        <th>Jumlah</th>
        <th>Harga</th>
        <th>Total</th>
        <th>Nama Pembeli</th>
        <th>Tanggal</th>
    </tr>
    <?php foreach ($orders as $order): ?>
    <tr>
        <td><?php echo htmlspecialchars($order['item']); ?></td>
        <td><?php echo $order['quantity']; ?></td>
        <td>Rp <?php echo number_format($order['price'], 0, ',', '.'); ?></td>
        <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
        <td><?php echo $order['order_time']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<br>

<form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset semua data transaksi?');">
    <button type="submit" name="reset_orders">Reset Transaksi</button>
</form>
    </div>
</body>
</html>