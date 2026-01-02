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
  <thead>
    <tr>
      <th>Kode</th>
      <th>Nama</th>
      <th>Total</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody id="pesanan-body">
    <script>
async function loadPesanan() {
  try {
    const res = await fetch('../api/pesanan_api.php');
    const data = await res.json();

    const tbody = document.getElementById('pesanan-body');
    tbody.innerHTML = '';

    data.forEach(p => {

      let aksi = `
        <a href="detail_pesanan.php?id=${p.id}">Detail</a>
      `;

      // tombol bayar hanya untuk kasir & status diantar
      <?php if ($_SESSION['admin']['role'] === 'kasir'): ?>
        if (p.status === 'diantar') {
          aksi += ` | <a class="btn" href="bayar.php?id=${p.id}">Bayar</a>`;
        }
      <?php endif; ?>

      tbody.innerHTML += `
        <tr>
          <td>${p.kode}</td>
          <td>${p.nama_pemesan}</td>
          <td>Rp ${Number(p.total_harga).toLocaleString('id-ID')}</td>
          <td>${p.status}</td>
          <td>${aksi}</td>
        </tr>
      `;
    });

  } catch (err) {
    console.error('Gagal load pesanan', err);
  }
}

// load awal
loadPesanan();

// polling tiap 12 detik 
setInterval(loadPesanan, 12000);
</script>
  </tbody>
</table>


</main>
</body>

</html>
