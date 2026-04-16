<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: menu.php");
    exit;
}

// Hapus data struk jika ada
unset($_SESSION['struk']);
unset($_SESSION['struk_nama']);

// Opsional: kalau mau sekaligus hapus keranjang juga
unset($_SESSION['orders']);

// Redirect balik ke menu.php
header("Location: menu.php");
exit;
?>
