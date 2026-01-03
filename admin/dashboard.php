<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}
$role = $_SESSION['admin']['role'];
$pes = mysqli_query($conn, 
    "SELECT * FROM pesanan ORDER BY created_at DESC LIMIT 20"
);
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Cafe AHMF</h2>
  <nav>
    <?php if ($role === 'admin'): ?>
        <a href="menu.php">Kelola Menu</a>
        <a href="kategori.php">Kategori</a>
        <a href="users.php">Users</a>
        <a href="pesanan.php">Pesanan</a>
        <a href="transaksi.php">Laporan</a>
    <?php endif; ?>

    <?php if ($role === 'kasir'): ?>
        <a href="pos.php">Buat Pesanan</a>
        <a href="pesanan.php">Pesanan</a>
        <a href="transaksi.php">Transaksi</a>
    <?php endif; ?>

    <?php if ($role === 'dapur'): ?>
        <a href="dapur.php">Pesanan Masuk Dapur</a>
    <?php endif; ?>

    <?php if ($role === 'waiter'): ?>
        <a href="waiter.php">Pesanan Siap Antar</a>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

  <h1>Halo, <?php echo htmlspecialchars($_SESSION['admin']['nama']); ?></h1>
  <p>Role: <b><?php echo ucfirst($role); ?></b></p>

  <?php
  if ($role === 'admin') {
      echo "<p>Anda masuk sebagai <b>Admin</b>. Silakan kelola sistem.</p>";
  }
  if ($role === 'kasir') {
      echo "<p>Anda masuk sebagai <b>Kasir</b>. Silakan buat dan proses pesanan.</p>";
  }
  if ($role === 'dapur') {
      echo "<p>Anda masuk sebagai <b>Bagian Dapur</b>. Silakan memproses pesanan.</p>";
  }
  if ($role === 'waiter') {
      echo "<p>Anda masuk sebagai <b>Waiter</b>. Silakan mengantarkan pesanan.</p>";
  }
  ?>

  <h3>Pesanan Terbaru</h3>
  <table>
    <tr>
      <th>Kode</th>
      <th>Nama</th>
      <th>Total</th>
      <th>Status</th>
      <th>Tgl</th>
    </tr>

    <?php while($r = mysqli_fetch_assoc($pes)){ ?>
    <tr>
      <td><?php echo $r['kode']; ?></td>
      <td><?php echo $r['nama_pemesan']; ?></td>
      <td><?php echo number_format($r['total_harga']); ?></td>
      <td><?php echo ucfirst($r['status']); ?></td>
      <td><?php echo $r['created_at']; ?></td>
    </tr>
    <?php } ?>
  </table>

</main>

</body>

</html>
