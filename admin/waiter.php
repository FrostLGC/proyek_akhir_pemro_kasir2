<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'waiter') {
    echo "Akses ditolak";
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($conn,
        "UPDATE pesanan SET status='diantar' WHERE id=$id"
    );
    header('Location: waiter.php');
    exit;
}

$pesanan = mysqli_query($conn,
    "SELECT * FROM pesanan 
     WHERE status='selesai'
     ORDER BY created_at ASC"
);
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Waiter</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Pesanan Siap Diantarr</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h2>Pesanan Siap Diantar</h2>

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
  <tbody id="waiter-body">
    <script>
async function loadWaiter() {
  try {
    const res = await fetch('../api/waiter_api.php');
    const data = await res.json();

    const tbody = document.getElementById('waiter-body');
    tbody.innerHTML = '';

    data.forEach(p => {
      tbody.innerHTML += `
        <tr>
          <td>${p.kode}</td>
          <td>${p.nama_pemesan}</td>
          <td>${p.meja ?? '-'}</td>
          <td>${p.status}</td>
          <td>
            <a href="detail_pesanan.php?id=${p.id}">Lihat</a>
          </td>
          <td>
            <button class="btn" onclick="antar(${p.id})">
              Antar
            </button>
          </td>
        </tr>
      `;
    });

  } catch (err) {
    console.error('Gagal load waiter', err);
  }
}

async function antar(id) {
  if (!confirm('Pesanan sudah diantar?')) return;

  await fetch('../api/waiter_update.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + id
  });

  loadWaiter(); // refresh data tanpa reload halaman
}

// load awal
loadWaiter();

// polling tiap 12 detik
setInterval(loadWaiter, 12000);
</script>

  </tbody>
</table>


</main>
</body>

</html>
