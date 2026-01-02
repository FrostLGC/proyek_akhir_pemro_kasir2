<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (!isset($_SESSION['admin'])) { header('Location: ../public/login.php'); exit; }
if (isset($_POST['update_status'])){
    $id = intval($_POST['id']);
    $status = mysqli_real_escape_string($conn,$_POST['status']);
    mysqli_query($conn, "UPDATE pesanan SET status='{$status}' WHERE id={$id}");
}
$p = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY created_at DESC");
?>

<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan</title>
    <link rel="stylesheet" href="../assets/style.css">
  </head>
<body>

<header>
  <h2>Daftar Pesanan</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h3>Daftar Pesanan</h3>

<table>
<tr>
  <th>Kode</th>
  <th>Nama</th>
  <th>Total</th>
  <th>Status</th>
  <th>Aksi</th>
</tr>

<?php while($r = mysqli_fetch_assoc($p)){ ?>
<tr>
  <td><?php echo $r['kode']; ?></td>
  <td><?php echo htmlspecialchars($r['nama_pemesan']); ?></td>
  <td>Rp <?php echo number_format($r['total_harga']); ?></td>
  <td><?php echo ucfirst($r['status']); ?></td>
  <td>
    <a href="detail_pesanan.php?id=<?php echo $r['id']; ?>">Detail</a>

    <?php if ($_SESSION['admin']['role'] === 'kasir' && $r['status'] === 'diantar'): ?>
      | <a class="btn" href="bayar.php?id=<?php echo $r['id']; ?>">Bayar</a>
    <?php endif; ?>
  </td>
</tr>
<?php } ?>

</table>

</main>
</body>

</html>
