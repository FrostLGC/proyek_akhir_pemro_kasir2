<?php
$host = "localhost";
$user = "root";
$psw = "";
$db = "dbcafeproyekakhir";

//membeuat koneksi
$conn = mysqli_connect($host, $user,$psw, $db);

//cek koneksi
if (!$conn) {
  die("Koneksi gagal: " . mysqli_connect_error());
}
// echo "koneksi berhasil!";
?>