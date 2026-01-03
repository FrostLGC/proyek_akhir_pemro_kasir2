<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (!isset($_SESSION['admin'])) { header('Location: ../login.php'); exit; }
$q = mysqli_query($conn, "SELECT t.*, p.kode FROM transaksi t JOIN pesanan p ON t.pesanan_id=p.id ORDER BY t.created_at DESC");
?>

<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
    <link rel="stylesheet" href="../assets/style.css">
  </head>
<body>

<header>
  <h2>Laporan Transaksi</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h3>Data Transaksi</h3>

<table>
<tr>
  <th>Kode</th>
  <th>Bayar</th>
  <th>Kembali</th>
  <th>Tanggal</th>
  <th>Aksi</th>
</tr>

<?php while($r=mysqli_fetch_assoc($q)){ ?>
<tr>
  <td><?php echo $r['kode']; ?></td>
  <td>Rp <?php echo number_format($r['bayar']); ?></td>
  <td>Rp <?php echo number_format($r['kembali']); ?></td>
  <td><?php echo $r['created_at']; ?></td>
  <td>
    <a class="btn" href="print_struk.php?id=<?php echo $r['pesanan_id']; ?>" target="_blank">
      Cetak Struk
    </a>
  </td>
</tr>
<?php } ?>

</table>

</main>
</body>

</html>
