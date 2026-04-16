<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'pemesanan';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Koneksi Gagal: ' . $conn->connect_error);
}

// Menampilkan daftar pesanan
$orders = [];
$order_result = $conn->query("SELECT * FROM orders ORDER BY order_time DESC");
while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}

// Menghitung total uang
$total_income = 0;
foreach ($orders as $order) {
    $total_income += $order['total'];
}

// Menghitung jumlah barang terjual per produk
$product_sales = [];
$product_result = $conn->query("SELECT item, SUM(quantity) AS total_quantity 
                                FROM orders 
                                GROUP BY item");

while ($row = $product_result->fetch_assoc()) {
    $product_sales[$row['item']] = $row['total_quantity'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Admin - Hasil Penjualan</title>
</head>
<body>

<h1>Halaman Admin - Hasil Penjualan</h1>

<h2>Daftar Pesanan</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Nama Produk</th>
        <th>Jumlah</th>
        <th>Harga</th>
        <th>Total</th>
        <th>Nama Customer</th>
        <th>Tanggal</th>
    </tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['item']); ?></td>
            <td><?php echo $order['quantity']; ?></td>
            <td><?php echo $order['price']; ?></td>
            <td><?php echo $order['total']; ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td><?php echo $order['order_time']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Total Uang yang Diperoleh:</h3>
<p><?php echo $total_income; ?></p>

<h2>Jumlah Barang Terjual per Produk</h2>
<table border="1" cellpadding="5" cellspacing="0">
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

<br>
<a href="admin_home.php">Kembali</a>

</body>
</html>