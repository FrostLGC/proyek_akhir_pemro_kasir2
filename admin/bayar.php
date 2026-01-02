<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'kasir') {
    echo "Akses ditolak";
    exit;
}

$id = intval($_GET['id']);
$q = mysqli_query($conn, "SELECT * FROM pesanan WHERE id=$id");

if (!$q || mysqli_num_rows($q) == 0) {
    echo "Pesanan tidak ditemukan";
    exit;
}

$pes = mysqli_fetch_assoc($q);

// hanya boleh bayar kalau sudah diantar
if ($pes['status'] !== 'diantar') {
    echo "Pesanan belum siap dibayar";
    exit;
}

if (isset($_POST['bayar'])) {
    $bayar = intval($_POST['jumlah_bayar']);
    $total = intval($pes['total_harga']);

    if ($bayar < $total) {
        $error = "Uang bayar kurang";
    } else {
        $kembali = $bayar - $total;

        mysqli_query($conn,
            "INSERT INTO transaksi (pesanan_id, bayar, kembali)
              VALUES ($id, $bayar, $kembali)"
        );

        mysqli_query($conn,
            "UPDATE pesanan SET status='dibayar' WHERE id=$id"
        );

        $success = true;
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Cafe AHMF - Kasir</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h2>Pembayaran Pesanan <?php echo $pes['kode']; ?></h2>

<?php if (!empty($success)): ?>
<script>
  window.open('print_struk.php?id=<?php echo $id; ?>', '_blank');
  setTimeout(() => window.location.href = 'pesanan.php', 1000);
</script>
<p style="color:green;">Pembayaran berhasil. Struk dibuka di tab baru.</p>
<?php endif; ?>

<p>Total: <b>Rp <?php echo number_format($pes['total_harga'],0,',','.'); ?></b></p>

<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>

<form method="post">
  <label>Uang Bayar</label>
  <input type="number" name="jumlah_bayar" required>
  <button type="submit" name="bayar">Bayar</button>
</form>

</main>
</body>

</html>
