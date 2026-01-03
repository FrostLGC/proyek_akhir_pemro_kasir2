<?php
session_start();
require_once __DIR__ . '/config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);

    // cek admin
    $qAdmin = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email' AND password='$pass' LIMIT 1"
    );
    if ($qAdmin && mysqli_num_rows($qAdmin) === 1) {
        $_SESSION['admin'] = mysqli_fetch_assoc($qAdmin);
        header('Location: admin/dashboard.php');
        exit;
    }

    // cek customer
    $qCust = mysqli_query($conn,
        "SELECT * FROM customers WHERE email='$email' AND password='$pass' LIMIT 1"
    );
    if ($qCust && mysqli_num_rows($qCust) === 1) {
        $_SESSION['customer'] = mysqli_fetch_assoc($qCust);
        header('Location: menu.php');
        exit;
    }

    $error = 'Email atau password salah';
}
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<header>
  <h2>Login</h2>
  <nav>
    <a href="index.php">Home</a>
    <a href="menu.php">Menu</a>
  </nav>
</header>

<main>

<?php if ($error): ?>
  <p style="color:red"><?= $error ?></p>
<?php endif; ?>

<form method="post">
  <label>Email</label>
  <input type="email" name="email" required>

  <label>Password</label>
  <input type="password" name="password" required>

  <button type="submit">Login</button>
</form>

<p>
  Belum punya akun?
  <a href="register.php">Daftar Pelanggan</a>
</p>

</main>
</body>
</html>
