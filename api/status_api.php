<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$kode = isset($_GET['kode'])
    ? mysqli_real_escape_string($conn, $_GET['kode'])
    : '';

if (!$kode) {
    echo json_encode(['error' => 'Kode kosong']);
    exit;
}

$q = mysqli_query($conn, "SELECT * FROM pesanan WHERE kode='$kode' LIMIT 1");

if (!$q || mysqli_num_rows($q) === 0) {
    echo json_encode(['error' => 'Pesanan tidak ditemukan']);
    exit;
}

$pesanan = mysqli_fetch_assoc($q);

$detail = [];
$dq = mysqli_query($conn,"
    SELECT m.nama_menu, dp.jumlah, dp.subtotal, dp.catatan
    FROM detail_pesanan dp
    JOIN menu m ON dp.menu_id = m.id
    WHERE dp.pesanan_id = {$pesanan['id']}
");

while ($r = mysqli_fetch_assoc($dq)) {
    $detail[] = $r;
}

echo json_encode([
    'pesanan' => $pesanan,
    'detail'  => $detail
]);
