<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

// ambil menu dan kategori
$res = mysqli_query($conn,"
    SELECT m.*, k.nama AS kategori
    FROM menu m
    LEFT JOIN kategori k ON m.kategori_id = k.id
    ORDER BY k.id ASC, m.nama_menu ASC
");

// proses submit pos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {

    $items = $_POST['items'];
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_pemesan']);
    $meja  = mysqli_real_escape_string($conn, $_POST['meja']);

    $total = 0;
    $dataItem = [];

    foreach ($items as $mid => $jml) {
        $mid = intval($mid);
        $jml = intval($jml);

        if ($jml > 0) {
            $q = mysqli_query($conn, "SELECT * FROM menu WHERE id=$mid");
            if ($q && mysqli_num_rows($q)) {
                $m = mysqli_fetch_assoc($q);
                $sub = $m['harga'] * $jml;
                $total += $sub;

                $dataItem[] = [
                    'menu_id' => $mid,
                    'jumlah'  => $jml,
                    'subtotal'=> $sub
                ];
            }
        }
    }

    if ($total > 0) {
        $kode = 'P' . date('ymdHis') . rand(10,99);

        mysqli_query($conn,"
            INSERT INTO pesanan
            (kode, nama_pemesan, meja, total_harga, status)
            VALUES
            ('$kode','$nama','$meja',$total,'menunggu')
        ");

        $pid = mysqli_insert_id($conn);

        foreach ($dataItem as $d) {
            mysqli_query($conn,"
                INSERT INTO detail_pesanan
                (pesanan_id, menu_id, jumlah, subtotal)
                VALUES
                ($pid, {$d['menu_id']}, {$d['jumlah']}, {$d['subtotal']})
            ");
        }

        header("Location: proses_bayar.php?id=$pid");
        exit;
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POS Kasir</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Cafe AHMF - POS Kasir</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h3>Buat Pesanan</h3>

<form method="post" class="form-stack">

  <label>Nama Pemesan</label>
  <input type="text" name="nama_pemesan" required>

  <label>Meja (opsional)</label>
  <input type="text" name="meja">

  <hr>

<div class="pos-list">

<?php
$lastKategori = null;
while ($m = mysqli_fetch_assoc($res)):
?>

  <?php if ($lastKategori !== $m['kategori']): ?>
    <h4><?= htmlspecialchars($m['kategori']) ?></h4>
    <?php $lastKategori = $m['kategori']; ?>
  <?php endif; ?>

  <div class="pos-item">
    <div class="pos-info">
      <strong><?= htmlspecialchars($m['nama_menu']) ?></strong><br>
      <small>Rp <?= number_format($m['harga'],0,',','.') ?></small>
    </div>

    <input type="number"
    name="items[<?= $m['id'] ?>]"
    min="0"
    value="0">
  </div>

<?php endwhile; ?>

</div>

  <button type="submit">Buat Pesanan</button>

</form>

</main>
</body>
</html>
