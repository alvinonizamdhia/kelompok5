<?php
session_start();

if (!isset($_SESSION['struk']) || 
    !isset($_SESSION['struk_nama']) || 
    !isset($_SESSION['jumlah_bayar']) || 
    !isset($_SESSION['metode_pembayaran'])) {
    header("Location: menu.php");
    exit;
}

$orders = $_SESSION['struk'];
$nama_user = $_SESSION['struk_nama'];
$jumlah_bayar = $_SESSION['jumlah_bayar'];
$metode_pembayaran = $_SESSION['metode_pembayaran'];

$grand_total = 0;
foreach ($orders as $order) {
    $grand_total += $order['total'];
}

$kembalian = $jumlah_bayar - $grand_total;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Purchase Receipt</title>
    <link href="assets/kasir.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="struk.css">
</head>
<body>
<div class="container">
<center><h1>Purchase Receipt</h1></center>

<h3>Customer Name: <?php echo htmlspecialchars($nama_user); ?></h3>

<hr>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Item</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
    </tr>

    <?php foreach ($orders as $order): ?>
    <tr>
        <td><?php echo htmlspecialchars($order['item']); ?></td>
        <td><?php echo number_format($order['quantity'], 0, ',', '.'); ?></td>
        <td><?php echo number_format($order['price'], 0, ',', '.'); ?></td>
        <td><?php echo number_format($order['total'], 0, ',', '.'); ?></td>
    </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="3">Grand Total</td>
        <td><?php echo number_format($grand_total, 0, ',', '.');; ?></td>
    </tr>

    <?php if ($metode_pembayaran !== 'qris'): ?>
    <tr>
        <td colspan="3">Amount Paid</td>
        <td><?php echo number_format($jumlah_bayar, 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td colspan="3">Change</td>
        <td><?php echo number_format($kembalian, 0, ',', '.'); ?></td>
    </tr>
    <?php endif; ?>
</table>

<?php if ($metode_pembayaran === 'qris'): ?>
    <p>Silakan scan QR untuk pembayaran.</p>
    <img src="assets/ewallet.png" alt="QR Code" width="150">
<?php endif; ?>
<p>Thank you for your purchase!     -Group 5</p>
<a href="menu.php?logout=1" class="selesai">Finish</a>
<br>
<a href="cancel_payment.php?logout=1" class="batal">Cancel Payment</a>
</div>
</body>
</html>

<?php
unset($_SESSION['struk']);
unset($_SESSION['struk_nama']);
unset($_SESSION['jumlah_bayar']);
unset($_SESSION['metode_pembayaran']);
?>