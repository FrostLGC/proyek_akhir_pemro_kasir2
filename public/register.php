<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);

    $q = mysqli_query($conn,
        "INSERT INTO customers (nama, email, password)
         VALUES ('$nama', '$email', '$pass')"
    );

    if ($q) {
        header('Location: login.php');
        exit;
    } else {
        $err = 'Gagal register, email mungkin sudah digunakan.';
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<header>
  <h2>Register Pelanggan</h2>
  <nav>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
  </nav>
</header>

<main>

<?php if ($err): ?>
  <p style="color:red"><?= $err ?></p>
<?php endif; ?>

<form method="post">
  <label>Nama</label>
  <input type="text" name="nama" required>

  <label>Email</label>
  <input type="email" name="email" required>

  <label>Password</label>
  <input type="password" name="password" required>

  <button type="submit">Daftar</button>
</form>

<p>
  Sudah punya akun?
  <a href="login.php">Login</a>
</p>

</main>
</body>
</html>
