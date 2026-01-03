<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['customer'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer']['id'];

$pesanan = mysqli_query($conn,
    "SELECT * FROM pesanan
     WHERE customer_id = $customer_id
     ORDER BY created_at DESC"
);

function label($s){
    return match($s){
        'menunggu' => 'Menunggu',
        'diproses' => 'Sedang dimasak',
        'selesai'  => 'Siap diantar',
        'diantar'  => 'Sudah diantar',
        'dibayar'  => 'Sudah dibayar',
        default    => ucfirst($s)
    };
}
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Pesanan</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<header>
  <h2>Riwayat Pesanan</h2>
  <nav>
    <a href="menu.php">Menu</a>
    <a href="index.php">Home</a>
  </nav>
</header>

<main>

<table>
<tr>
  <th>Kode</th>
  <th>Detail Pesanan</th>
  <th>Total</th>
  <th>Status</th>
  <th>Tanggal</th>
</tr>

<?php while ($p = mysqli_fetch_assoc($pesanan)): ?>

<?php
$detail = mysqli_query($conn,
    "SELECT dp.jumlah, dp.catatan, m.nama_menu
     FROM detail_pesanan dp
     JOIN menu m ON dp.menu_id = m.id
     WHERE dp.pesanan_id = {$p['id']}"
);
?>

<tr>
  <td><?= $p['kode'] ?></td>

  <td>
    <ul style="margin:0; padding-left:16px;">
      <?php while ($d = mysqli_fetch_assoc($detail)): ?>
        <li>
  <?= htmlspecialchars($d['nama_menu']) ?> x<?= $d['jumlah'] ?>
  <?php if (!empty($d['catatan'])): ?>
    <br>
    <small style="color:#555;">
      Catatan: <?= htmlspecialchars($d['catatan']) ?>
    </small>
  <?php endif; ?>
</li>

      <?php endwhile; ?>
    </ul>
  </td>

  <td>Rp <?= number_format($p['total_harga'],0,',','.') ?></td>
  <td><?= label($p['status']) ?></td>
  <td><?= $p['created_at'] ?></td>
</tr>

<?php endwhile; ?>

</table>

</main>
</body>
</html>
