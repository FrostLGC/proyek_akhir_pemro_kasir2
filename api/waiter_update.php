<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);

  mysqli_query($conn,
    "UPDATE pesanan SET status='diantar' WHERE id=$id"
  );

  echo json_encode(['success' => true]);
}
