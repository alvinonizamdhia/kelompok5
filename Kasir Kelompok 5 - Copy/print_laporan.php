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

// Ambil data pesanan
$orders = [];
$order_result = $conn->query("SELECT * FROM orders ORDER BY order_time DESC");
while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}

// Hitung total uang
$total_income = 0;
foreach ($orders as $order) {
    $total_income += $order['total'];
}

// Tanggal dan jam cetak
date_default_timezone_set('Asia/Jakarta');
$tanggal_cetak = date('d-m-Y');
$jam_cetak = date('H:i:s');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan Penjualan</title>
    <link rel="stylesheet" href="print_laporan.css">
</head>
<body>
<div class="container">
<h2>Laporan Penjualan</h2>
<p>Tanggal: <?php echo $tanggal_cetak; ?></p>
<p>Jam: <?php echo $jam_cetak; ?></p>

<h3>Daftar Pesanan</h3>

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

<br>
<a href="admin_home.php">Kembali</a>
</div>
</body>
</html>