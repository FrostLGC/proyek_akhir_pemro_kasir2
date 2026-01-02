<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'dapur') {
    echo "Akses ditolak";
    exit;
}

// update status
if (isset($_GET['id'], $_GET['aksi'])) {
    $id = intval($_GET['id']);

    if ($_GET['aksi'] === 'proses') {
        mysqli_query($conn, "UPDATE pesanan SET status='diproses' WHERE id=$id");
    }
    if ($_GET['aksi'] === 'selesai') {
        mysqli_query($conn, "UPDATE pesanan SET status='selesai' WHERE id=$id");
    }
    header('Location: dapur.php');
    exit;
}

$pesanan = mysqli_query($conn,
    "SELECT * FROM pesanan 
     WHERE status IN ('menunggu','diproses')
     ORDER BY created_at ASC"
);
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dapur</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Pesanan Masuk Dapur</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h2>Pesanan Masuk Dapur</h2>
<table>
  <thead>
    <tr>
      <th>Kode</th>
      <th>Nama</th>
      <th>Meja</th>
      <th>Status</th>
      <th>Detail</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody id="dapur-body">
    <script>
async function loadDapur() {
  try {
    const res = await fetch('../api/dapur_api.php');
    const data = await res.json();

    const tbody = document.getElementById('dapur-body');
    tbody.innerHTML = '';

    data.forEach(p => {
      let aksi = '';
      if (p.status === 'menunggu') {
        aksi = `<a class="btn" href="?id=${p.id}&aksi=proses">Proses</a>`;
      } else if (p.status === 'diproses') {
        aksi = `<a class="btn" href="?id=${p.id}&aksi=selesai">Selesai</a>`;
      }

      tbody.innerHTML += `
        <tr>
          <td>${p.kode}</td>
          <td>${p.nama_pemesan}</td>
          <td>${p.meja ?? '-'}</td>
          <td>${p.status}</td>
          <td><a href="detail_pesanan.php?id=${p.id}">Lihat</a></td>
          <td>${aksi}</td>
        </tr>
      `;
    });

  } catch (err) {
    console.error(err);
  }
}

// load pertama
loadDapur();

// polling tiap 12 detik
setInterval(loadDapur, 12000);
</script>
  </tbody>
</table>


</main>
</body>

</html>
