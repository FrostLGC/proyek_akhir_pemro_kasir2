<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../public/login.php');
    exit;
}

$role = $_SESSION['admin']['role'];
$id = intval($_GET['id']);

$q = mysqli_query($conn, "SELECT * FROM pesanan WHERE id={$id}");
if (!$q || mysqli_num_rows($q) == 0) {
    echo 'Pesanan tidak ditemukan';
    exit;
}

$pes = mysqli_fetch_assoc($q);
$det = mysqli_query($conn,
    "SELECT dp.*, m.nama_menu 
     FROM detail_pesanan dp 
     JOIN menu m ON dp.menu_id=m.id 
     WHERE dp.pesanan_id={$id}"
);
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Pesanan</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Detail Pesanan</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="pesanan.php">Pesanan</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h3>Kode: <?php echo $pes['kode']; ?></h3>

<p><strong>Nama:</strong> <?php echo htmlspecialchars($pes['nama_pemesan']); ?></p>
<p><strong>Meja:</strong> <?php echo $pes['meja'] ? htmlspecialchars($pes['meja']) : '-'; ?></p>
<p><strong>Status:</strong> <?php echo ucfirst($pes['status']); ?></p>

<?php if ($role === 'kasir' && $pes['status'] === 'diantar'): ?>
  <a class="btn" href="bayar.php?id=<?php echo $pes['id']; ?>">Proses Pembayaran</a>
<?php endif; ?>

<?php if (($role === 'admin' || $role === 'kasir') && $pes['status'] === 'dibayar'): ?>
  <a class="btn" href="print_struk.php?id=<?php echo $pes['id']; ?>" target="_blank">
    Cetak Struk
  </a>
<?php endif; ?>

<?php if ($role === 'dapur'): ?>
    <?php if ($pes['status'] === 'menunggu'): ?>
        <a href="dapur.php?id=<?php echo $pes['id']; ?>&aksi=proses">Proses Masak</a>
    <?php elseif ($pes['status'] === 'diproses'): ?>
        <a href="dapur.php?id=<?php echo $pes['id']; ?>&aksi=selesai">Selesai</a>
    <?php endif; ?>
<?php endif; ?>

<?php if ($role === 'waiter' && $pes['status'] === 'selesai'): ?>
    <a href="waiter.php?id=<?php echo $pes['id']; ?>">Pesanan Sudah Diantar</a>
<?php endif; ?>

<hr>

<h4>Detail Item</h4>
<ul>
<?php while($d = mysqli_fetch_assoc($det)){ ?>
<li>
  <strong><?php echo htmlspecialchars($d['nama_menu']); ?></strong>
  x<?php echo $d['jumlah']; ?>
  - Rp <?php echo number_format($d['subtotal'],0,',','.'); ?>

  <?php if (!empty($d['catatan'])): ?>
    <br>
    <em style="color:#555;">
      Catatan: <?php echo htmlspecialchars($d['catatan']); ?>
    </em>
  <?php endif; ?>
</li>
<?php } ?>
</ul>

</main>
</body>

</html>
