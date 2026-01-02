<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$q = mysqli_query($conn,"
  SELECT id, kode, nama_pemesan, meja, status
  FROM pesanan
  WHERE status IN ('menunggu','diproses')
  ORDER BY created_at ASC
");

$data = [];
while ($r = mysqli_fetch_assoc($q)) {
  $data[] = $r;
}

echo json_encode($data);
