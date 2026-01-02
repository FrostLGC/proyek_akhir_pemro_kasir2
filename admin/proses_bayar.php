<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../public/login.php');
    exit;
}

$id = intval($_GET['id']);
$q = mysqli_query($conn, "SELECT * FROM pesanan WHERE id=$id");
if (!$q || mysqli_num_rows($q) == 0) {
    echo "Pesanan tidak ditemukan";
    exit;
}

$pes = mysqli_fetch_assoc($q);
$det = mysqli_query($conn,
    "SELECT dp.*, m.nama_menu
     FROM detail_pesanan dp
     JOIN menu m ON dp.menu_id = m.id
     WHERE dp.pesanan_id = $id"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bayar = intval($_POST['bayar']);
    $kembali = $bayar - intval($pes['total_harga']);

    mysqli_query($conn,
        "INSERT INTO transaksi (pesanan_id, bayar, kembali, metode)
         VALUES ($id, $bayar, $kembali, 'tunai')"
    );

    mysqli_query($conn,
        "UPDATE pesanan SET status='dibayar' WHERE id=$id"
    );

    $done = true;
}
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Proses Pembayaran</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Pembayaran Pesanan</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="pesanan.php">Pesanan</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h3>Kode Pesanan: <?php echo $pes['kode']; ?></h3>
<p><strong>Nama:</strong> <?php echo htmlspecialchars($pes['nama_pemesan']); ?></p>
<p><strong>Meja:</strong> <?php echo $pes['meja'] ?: '-'; ?></p>

<hr>

<h4>Detail Pesanan</h4>
<ul>
<?php while($d = mysqli_fetch_assoc($det)) { ?>
  <li>
    <?php echo htmlspecialchars($d['nama_menu']); ?>
    x<?php echo $d['jumlah']; ?>
    - Rp <?php echo number_format($d['subtotal']); ?>
  </li>
<?php } ?>
</ul>

<p><strong>Total:</strong> Rp <?php echo number_format($pes['total_harga']); ?></p>

<?php if (!isset($done)) { ?>
<form method="post">
  <label>Uang Bayar</label>
  <input type="number" name="bayar" required>
  <button type="submit" class="btn">Bayar</button>
</form>
<?php } else { ?>

<p style="color:green;font-weight:bold;">
  Pembayaran berhasil
</p>

<a class="btn" href="print_struk.php?id=<?php echo $id; ?>" target="_blank">
  Cetak Struk
</a>

<a class="btn" href="dashboard.php">
  Kembali ke Dashboard
</a>

<?php } ?>

</main>
</body>
</html>
