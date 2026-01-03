<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['admin'])) {
    header('Location: admin/dashboard.php');
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe AHMF</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <h2>Cafe AHMF</h2>
    <nav>
        <a href="menu.php">Menu</a>
        <?php if (isset($_SESSION['customer'])): ?>
            <a href="riwayat_pesanan.php">Riwayat</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di Cafe AHMF</h1>
            <p class="hero-subtitle">Cafe AHMF menyediakan berbagai pilihan makanan dan minuman yang cocok untuk nongkrong maupun makan santai.</p>
            <div class="hero-features">
                <div class="feature">
                    <span>Harga Terjangkau</span>
                </div>
                <div class="feature">
                    <span>Menu Lengkap</span>
                </div>
                <div class="feature">
                    <span>Suasana Nyaman</span>
                </div>
            </div>
            <div class="hero-actions">
                <a href="menu.php" class="btn btn-primary">Lihat Menu</a>
                <?php if (!isset($_SESSION['customer'])): ?>
                    <a href="register.php" class="btn btn-secondary">Daftar Sekarang</a>
                <?php endif; ?>
            </div>
        </div>
          <div class="hero-image">
    <div class="hero-box">
      <img src="assets/gambar_toko/logocafe.png" alt="Menu Cafe">
    </div>
  </div>
    </section>
</main>

</body>
</html>
