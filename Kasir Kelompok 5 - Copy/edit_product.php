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

// Ambil data produk
$product_result = $conn->query("SELECT * FROM products WHERE id = $product_id");
$product = $product_result ? $product_result->fetch_assoc() : null;

if (!$product) {
    echo "Produk tidak ditemukan.<br>";
    echo "<a href='admin_home.php'>Kembali</a>";
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    $image_sql = '';
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = 'uploads/' . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_sql = ", image = '$image_name'";

            if (!empty($product['image']) && file_exists('uploads/' . $product['image'])) {
                unlink('uploads/' . $product['image']);
            }
        }
    }

    $query = "UPDATE products 
              SET name = '$name', price = $price, stock = $stock $image_sql 
              WHERE id = $product_id";

    if ($conn->query($query)) {
        header("Location: admin_home.php");
        exit;
    } else {
        echo "Gagal mengupdate produk.<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="edit_product.css">
</head>
<body>
<div class="container">
<h2>Edit Produk</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Nama Produk:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
    <br><br>

    <label>Harga:</label><br>
    <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
    <br><br>

    <label>Stok:</label><br>
    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
    <br><br>

    <label>Gambar Produk:</label><br>
    <?php if (!empty($product['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Gambar Produk" width="150">
        <br><br>
    <?php endif; ?>
    <input type="file" name="image" accept="image/*">
    <br><br>

    <button type="submit">Update Produk</button>
</form>

<br>
<button class="batal" onclick="window.location.href='admin_home.php'">Batal</button>
</div>
</body>
</html>