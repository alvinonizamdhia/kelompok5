<?php
header('Location: menu.php');
exit;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Kelompok 5</title>
    <link href="assets/cashier-icon.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="menu.css">
</head>
<body>
    <!-- Auto redirect ke menu.php -->
    <div class="loading">
        <div class="spinner"></div>
        <p>Selamat Datang di Kasir - Loading Menu...</p>
    </div>
    
    <script>
        window.location.href = 'menu.php';
    </script>
</body>
</html>

