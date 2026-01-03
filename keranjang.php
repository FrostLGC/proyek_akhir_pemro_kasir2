<?php
session_start();
require_once __DIR__ . '/config/database.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $c) $total += $c['subtotal'];
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Keranjang</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<header>
  <h2>Keranjang Belanja</h2>
  <nav>
    <a href="menu.php">Menu</a>
    <a href="keranjang.php">Keranjang</a>
  </nav>
</header>

<main>

<?php if (!$cart): ?>
  <p>Keranjang masih kosong.</p>
<?php else: ?>

<table class="cart">
  <tr>
    <th>Menu</th>
    <th>Harga</th>
    <th>Jumlah</th>
    <th>Aksi</th>
    <th>Subtotal</th>
  </tr>

  <?php foreach ($cart as $index => $item): ?>
  <tr>
    <td><?= htmlspecialchars($item['nama']) ?></td>
    <td>Rp <?= number_format($item['harga'],0,',','.') ?></td>

    <td style="text-align:center;">
      <a href="update_keranjang.php?index=<?= $index ?>&aksi=kurang">-</a>
      <strong><?= $item['jumlah'] ?></strong>
      <a href="update_keranjang.php?index=<?= $index ?>&aksi=tambah">+</a>
    </td>

    <td style="text-align:center;">
      <a class="btn btn-danger"
         href="update_keranjang.php?index=<?= $index ?>&aksi=hapus"
         onclick="return confirm('Hapus item ini?')">
         Hapus
      </a>
    </td>

    <td>Rp <?= number_format($item['subtotal'],0,',','.') ?></td>
  </tr>
  <?php endforeach; ?>

  <tr>
    <th colspan="4" style="text-align:right;">Total</th>
    <th>Rp <?= number_format($total,0,',','.') ?></th>
  </tr>
</table>

<h3>Checkout</h3>

<form method="post" action="checkout.php">

<?php foreach ($cart as $index => $item): ?>
  <div style="margin-bottom:15px; padding:10px; border:1px solid #ddd;">
    <strong><?= htmlspecialchars($item['nama']) ?></strong>
    (<?= $item['jumlah'] ?> item)

    <label>Catatan untuk menu ini (opsional)</label>
    <textarea name="catatan[<?= $index ?>]"
      placeholder="Contoh: pedas banget, jangan asin"
      style="width:100%;"></textarea>
  </div>
<?php endforeach; ?>

<label>Nama Pemesan</label>
<input type="text" name="nama_pemesan" required
       value="<?= $_SESSION['customer']['nama'] ?? '' ?>">

<label>Meja (opsional)</label>
<input type="text" name="meja">

<button type="submit">Buat Pesanan</button>

</form>


<?php endif; ?>

</main>
</body>
</html>
