<?php
session_start();
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Nov 1997 05:00:00 GMT');

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: menu.php");
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

// Nama user
if (!isset($_SESSION['nama_user'])) {
    if (isset($_POST['set_name'])) {
        $_SESSION['nama_user'] = trim($_POST['nama_user']);
    } else {
        echo '
        <!DOCTYPE html>
        <html lang="id">
        <head><meta charset="UTF-8"><title>Cashier</title>
                <link href="assets/kasir.png" rel="icon" type="image/x-icon">
                <link rel="stylesheet" href="menu.css">
        <body class="">
        <div class="">
            <div class="container" style="">
                <div class="kotak">
                    <h3 class="">Enter Customer Name</h3>
                    <form method="POST">
                        <input type="text" name="nama_user" class="" placeholder="Your Name" required>
                        <button type="submit" name="set_name" class="">Continue</button>
                    </form>
                </div>
                    <a href="admin_home.php" class="admin">Admin Page</a>
            </div>
        </div>
        </body>
        </html>';
        exit;
    }
}

// Ambil data menu
$menu = [];
$result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}

// Tambah ke keranjang
if (isset($_POST['add_to_cart'])) {
    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];
    $product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();

    if ($product) {
        $current_stock = (int) $product['stock'];
        $existing_quantity = 0;

        if (!empty($_SESSION['orders'])) {
            foreach ($_SESSION['orders'] as $order) {
                if ($order['product_id'] == $product_id) {
                    $existing_quantity += $order['quantity'];
                }
            }
        }

        $new_total_quantity = $existing_quantity + $quantity;

        if ($new_total_quantity > $current_stock) {
            $_SESSION['error'] = "Jumlah melebihi stok tersedia untuk produk " . htmlspecialchars($product['name']);
        } else {
            $_SESSION['orders'][] = [
                'product_id' => $product_id,
                'item'       => $product['name'],
                'quantity'   => $quantity,
                'price'      => $product['price'],
                'total'      => $product['price'] * $quantity
            ];
        }
    }

    header("Location: menu.php");
    exit;
}

// Checkout
if (isset($_POST['checkout'])) {
    $jumlah_bayar = isset($_POST['jumlah_bayar']) ? (int) $_POST['jumlah_bayar'] : 0;
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? '';
    $total = 0;

    foreach ($_SESSION['orders'] as $order) {
        $total += $order['total'];
    }


    // Hanya validasi jumlah bayar jika metode adalah cash
    if ($metode_pembayaran === 'cash' && $jumlah_bayar < $total) {
        $_SESSION['error'] = "Uang yang dibayarkan kurang dari total belanja.";
        header("Location: menu.php");
        exit;
    }

    $nama_user = $conn->real_escape_string($_SESSION['nama_user']);
    foreach ($_SESSION['orders'] as $order) {
        $product_id = $order['product_id'];
        $item = $conn->real_escape_string($order['item']);
        $quantity = $order['quantity'];
        $price = $order['price'];
        $total_item = $order['total'];

        $conn->query("INSERT INTO orders (item, quantity, price, total, customer_name) 
                      VALUES ('$item', $quantity, $price, $total_item, '$nama_user')");
        $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
    }

    $_SESSION['struk'] = $_SESSION['orders'];
    $_SESSION['struk_nama'] = $nama_user;
    $_SESSION['jumlah_bayar'] = $jumlah_bayar;
    $_SESSION['metode_pembayaran'] = $metode_pembayaran;
    $_SESSION['orders'] = [];

    header("Location: struk.php");
    exit;
}


// Hapus produk dari keranjang
if (isset($_GET['remove_product_id'])) {
    $remove_product_id = (int) $_GET['remove_product_id'];

    foreach ($_SESSION['orders'] as $key => $order) {
        if ($order['product_id'] == $remove_product_id) {
            unset($_SESSION['orders'][$key]);
            break;
        }
    }

    $_SESSION['orders'] = array_values($_SESSION['orders']);
    header("Location: menu.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link href="assets/kasir.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="menu.css">
</head>
<body>

<div class="menu-container">
    <div class="products">
        <center><h1 style="color:#fff">Welcome, <?php echo htmlspecialchars($_SESSION['nama_user']); ?>!</h1></center>
        <center><h2 style="color:#fff">Choose Menu</h2></center>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="container2">
        <?php foreach ($menu as $item): ?>
            <div class="menu">
                <div>
                    <?php if (!empty($item['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top" style="border-radius: 8px;" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <?php else: ?>
                        <img src="assets/placeholder.png" class="card-img-top" alt="Tidak ada gambar" style="max-height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div id="menu-info">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3><hr>
                        <p>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?><br>
                        Stock: <?php echo $item['stock']; ?></p>
                    </div>
                        <?php
                        $in_cart = 0;
                        if (!empty($_SESSION['orders'])) {
                            foreach ($_SESSION['orders'] as $order) {
                                if ($order['product_id'] == $item['id']) {
                                    $in_cart += $order['quantity'];
                                }
                            }
                        }

                        $remaining_stock = $item['stock'] - $in_cart;
                        $is_disabled = $remaining_stock <= 0;
                        ?>

                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" class="form-control mb-2" min="1" max="<?php echo max($remaining_stock, 0); ?>" value="1" <?php echo $is_disabled ? 'disabled' : ''; ?> required>
                            <button type="submit" name="add_to_cart" class="btn btn-success w-100" class="keranjang" <?php echo $is_disabled ? 'disabled' : ''; ?>>
                                <?php echo $is_disabled ? 'Out of Stock' : 'Add to Cart'; ?>
                            </button>
                        </form>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

        <p><i>Items will not be displayed if they are out of stock.</i></p>
    </div>

    <div class="cart">
        <center><h2>Shopping Cart</h2></center>

        <?php if (!empty($_SESSION['orders'])): ?>
            <form method="POST">
                <table class="">
                    <thead class="">
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_total = 0;
                        foreach ($_SESSION['orders'] as $order): 
                            $grand_total += $order['total'];
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['item']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>Rp <?php echo number_format($order['price'], 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="menu.php?remove_product_id=<?php echo $order['product_id']; ?>" class="">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="">
                            <td colspan="3">Grand Total</td>
                            <td colspan="2">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="pembayaran" id="jumlahBayarContainer">
                    <br>
                    <label for="jumlah_bayar" class="form-label fw-bold">Amount Paid:</label>
                    <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" min="<?php echo $grand_total; ?>" required>
                    <form action="/action_page.php">
                    </select>
                </div>

                <button type="submit" name="checkout" class="">Proceed to Payment</button>
            </form>
        <?php else: ?>
            <div class="alert alert-info text-center">Your cart is empty.</div>
        <?php endif; ?>

        <a href="menu.php?logout=1" class="batal">Cancel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const metodeSelect = document.getElementById('metode_pembayaran');
    const jumlahBayarContainer = document.getElementById('jumlahBayarContainer');
    const jumlahBayarInput = document.getElementById('jumlah_bayar');

});
</script>

</body>
</html>
