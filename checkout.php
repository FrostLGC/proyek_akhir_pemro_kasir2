<?php
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: keranjang.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: menu.php');
    exit;
}

$cart = $_SESSION['cart'];

$nama = mysqli_real_escape_string($conn, $_POST['nama_pemesan']);

$meja = !empty($_POST['meja'])
    ? mysqli_real_escape_string($conn, $_POST['meja'])
    : null;

$customer_id = $_SESSION['customer']['id'] ?? null;

// hitung total
$total = 0;
foreach ($cart as $c) {
    $total += $c['subtotal'];
}

$kode = 'C' . date('ymdHis') . rand(10,99);

$meja_sql = $meja ? "'$meja'" : "NULL";
$cust_sql = $customer_id ? $customer_id : "NULL";

// insert pesanan
mysqli_query($conn,"
    INSERT INTO pesanan
    (kode, customer_id, nama_pemesan, meja, total_harga, status)
    VALUES
    ('$kode', $cust_sql, '$nama', $meja_sql, $total, 'menunggu')
");

$pesanan_id = mysqli_insert_id($conn);

// insert detail dan catatan
$catatanItems = $_POST['catatan'] ?? [];

foreach ($cart as $index => $item) {

    $catatan = mysqli_real_escape_string(
        $conn,
        $catatanItems[$index] ?? ''
    );

    mysqli_query($conn,"
        INSERT INTO detail_pesanan
        (pesanan_id, menu_id, jumlah, subtotal, catatan)
        VALUES
        ($pesanan_id,
         {$item['menu_id']},
         {$item['jumlah']},
         {$item['subtotal']},
         '$catatan')
    ");
}

// kosongkan keranjang
unset($_SESSION['cart']);

header("Location: status.php?kode=$kode");
exit;
