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

// Mengambil ID produk dari URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id > 0) {
    $query = "DELETE FROM products WHERE id = $product_id";
    if ($conn->query($query)) {
        echo "Produk berhasil dihapus!<br>";
        echo "<a href='admin.php'>Kembali ke Halaman Admin</a>";
    } else {
        echo "Gagal menghapus produk!<br>";
        echo "<a href='admin.php'>Kembali</a>";
    }
} else {
    echo "ID produk tidak valid.<br>";
    echo "<a href='admin.php'>Kembali</a>";
}

$conn->close();
?>