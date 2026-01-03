  <?php
  session_start();
  require_once __DIR__ . '/config/database.php';

  $sql = "SELECT m.*, k.nama AS kategori
          FROM menu m
          LEFT JOIN kategori k ON m.kategori_id = k.id
          ORDER BY k.id ASC, m.nama_menu ASC";
  $res = mysqli_query($conn, $sql);
  ?>

<!DOCTYPE HTML>
  <html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>

  <header>
    <h2>Menu Cafe</h2>
    <nav>
      <a href="index.php">Home</a>
      <a href="keranjang.php">Keranjang</a>
      <?php if (isset($_SESSION['customer'])): ?>
        <a href="riwayat_pesanan.php">Riwayat</a>
      <?php endif; ?>
      <a href="status.php">Cek Status Pesanan</a>
    </nav>
  </header>

  <main>
    <div class="menu-container">
      <?php
      $lastKategori = null;
      while ($row = mysqli_fetch_assoc($res)):
      ?>

        <?php if ($lastKategori !== $row['kategori']): ?>
          <?php if ($lastKategori !== null): ?>
            </div>
          <?php endif; ?>

          <section class="menu-category">
            <h2 class="category-title"> 
              <?= htmlspecialchars($row['kategori']) ?>
            </h2>
            <div class="cards">
          <?php $lastKategori = $row['kategori']; ?>
        <?php endif; ?>

        <article class="card">
          <div class="card-image">
            <?php if ($row['foto']): ?>
              <img src="uploads/gambar_menu/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama_menu']) ?>">
            <?php else: ?>
              <div class="noimg">
                <p>No Image</p>
              </div>
            <?php endif; ?>
          </div>

          <div class="card-content">
            <h3 class="card-title"><?= htmlspecialchars($row['nama_menu']) ?></h3>
            <p class="card-description"><?= htmlspecialchars($row['deskripsi']) ?></p>
            <p class="card-price"><strong>Rp <?= number_format($row['harga'],0,',','.') ?></strong></p>
          </div>

          <div class="card-actions">
            <form method="post" action="tambah_keranjang.php">
              <input type="hidden" name="menu_id" value="<?= $row['id'] ?>">

              <div class="quantity-selector">
                <label for="jumlah-<?= $row['id'] ?>">Jumlah</label>
                <input type="number" id="jumlah-<?= $row['id'] ?>" name="jumlah" value="1" min="1" max="99">
              </div>

              <button type="submit" class="btn btn-primary">
                Tambah ke Keranjang
              </button>
            </form>
          </div>
        </article>

      <?php endwhile; ?>
      </div>
      </section>
    </div>

  </main>
  </body>
  </html>
