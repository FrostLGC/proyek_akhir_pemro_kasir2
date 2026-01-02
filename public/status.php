<?php
require_once __DIR__ . '/../config/database.php';

$kode = isset($_GET['kode'])
    ? mysqli_real_escape_string($conn, $_GET['kode'])
    : '';

$pesanan = null;
$details = [];

if ($kode) {
    $q = mysqli_query($conn, "SELECT * FROM pesanan WHERE kode='$kode' LIMIT 1");
    if ($q && mysqli_num_rows($q) === 1) {
        $pesanan = mysqli_fetch_assoc($q);

        $dq = mysqli_query($conn,
        "SELECT dp.jumlah, dp.subtotal, dp.catatan, m.nama_menu
        FROM detail_pesanan dp
        JOIN menu m ON dp.menu_id = m.id
        WHERE dp.pesanan_id = {$pesanan['id']}"
);


        while ($r = mysqli_fetch_assoc($dq)) {
            $details[] = $r;
        }
    }
}

function labelStatus($s){
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
  <title>Status Pesanan</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<header>
  <h2>Status Pesanan</h2>
  <nav>
    <a href="index.php">Home</a>
    <a href="menu.php">Menu</a>
  </nav>
</header>

<main>

<form method="get">
  <label>Kode Pesanan</label>
  <input type="text" name="kode" required value="<?= htmlspecialchars($kode) ?>">
  <button type="submit">Cek Status</button>
</form>

<?php if ($kode && !$pesanan): ?>
  <p style="color:red;">Pesanan tidak ditemukan.</p>
<?php endif; ?>

<?php if ($pesanan): ?>
  <hr>

  <h3>Pesanan <?= htmlspecialchars($pesanan['kode']) ?></h3>

  <p><strong>Nama:</strong> <?= htmlspecialchars($pesanan['nama_pemesan']) ?></p>

  <?php if (!empty($pesanan['meja'])): ?>
    <p><strong>Meja:</strong> <?= htmlspecialchars($pesanan['meja']) ?></p>
  <?php endif; ?>

  <p>
    <strong>Status:</strong>
    <?= labelStatus($pesanan['status']) ?>
  </p>

  <p>
    <strong>Total:</strong>
    Rp <?= number_format($pesanan['total_harga'],0,',','.') ?>
  </p>

  <h4>Detail Pesanan</h4>
<ul>
<?php foreach ($details as $d): ?>
  <li>
    <?= htmlspecialchars($d['nama_menu']) ?>
    x<?= $d['jumlah'] ?>
    = Rp <?= number_format($d['subtotal'],0,',','.') ?>

    <?php if (!empty($d['catatan'])): ?>
      <br>
      <small><em>Catatan: <?= htmlspecialchars($d['catatan']) ?></em></small>
    <?php endif; ?>
  </li>
<?php endforeach; ?>
</ul>


<?php endif; ?>

<p>
  <a href="index.php">Kembali ke Home</a>
</p>

</main>
</body>
</html>


