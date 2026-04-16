<?php
session_start();

// Cek apakah sudah login
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_home.php");
    exit;
}

// Username dan password admin
$admin_username = 'Kelompok5';
$admin_password = 'kelompok5123';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_home.php");
        exit;
    } else {
        $error_message = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
<div class="kotaklogin">
<h2>Login Admin</h2>

<?php if (isset($error_message)): ?>
    <p><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required>
    <br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Login</button>
</form>
</div>
<br>
<a href="menu.php">Halaman Menu</a>
</div>
</body>
</html>