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

// Menangani pengiriman form
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    // Tangani upload gambar
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($image_ext, $allowed_ext)) {
        $new_image_name = uniqid('prod_', true) . '.' . $image_ext;
        $upload_path = 'uploads/' . $new_image_name;

        if (move_uploaded_file($image_tmp, $upload_path)) {
            $query = "INSERT INTO products (name, price, stock, image) 
                      VALUES ('$name', $price, $stock, '$new_image_name')";
            
            if ($conn->query($query)) {
                $berhasil = 'Produk berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan produk ke database!';
            }
        } else {
            $error = 'Gagal mengupload gambar!';
        }
    } else {
        $error = 'Format gambar tidak didukung!';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="add_product.css">
</head>
<body>
<div class="container">
<h1>Tambah Produk</h1>

<?php
if (!empty($error)) {
    echo '<div class="error">' . htmlspecialchars($error) . '</div>';
}
?>

<?php
if (!empty($berhasil)) {
    echo '<div class="berhasil">' . htmlspecialchars($berhasil) . '<br> <a href="admin_home.php">Lihat Produk</a></div>';
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Nama Produk:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Harga:</label><br>
    <input type="number" name="price" required><br><br>

    <label>Stok:</label><br>
    <input type="number" name="stock" required><br><br>

    <label>Gambar Produk:</label><br>
    <input type="file" name="image" required><br><br>

    <button type="submit">Tambah Produk</button>
</form>

<br>
<button class="batal" onclick="window.location.href='admin_home.php'">Batal</button>
</div>
</body>
</html>