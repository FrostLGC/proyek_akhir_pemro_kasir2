<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// auth
if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['admin']['role'] !== 'admin') {
    echo "Akses ditolak";
    exit;
}

// hapus kategori
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // cek apakah kategori masih dipakai menu
    $cek = mysqli_query($conn, "SELECT COUNT(*) AS total FROM menu WHERE kategori_id=$id");
    $row = mysqli_fetch_assoc($cek);

    if ($row['total'] > 0) {
        // kategori masih dipakai tidak boleh dihapus
        header('Location: kategori.php?error=dipakai');
        exit;
    }

    mysqli_query($conn, "DELETE FROM kategori WHERE id=$id");
    header('Location: kategori.php');
    exit;
}

// mode edit
$edit = false;
$dataEdit = null;

if (isset($_GET['edit'])) {
    $edit = true;
    $id = intval($_GET['edit']);
    $q = mysqli_query($conn, "SELECT * FROM kategori WHERE id=$id");
    if ($q && mysqli_num_rows($q)) {
        $dataEdit = mysqli_fetch_assoc($q);
    }
}

// tambah kategori
if (isset($_POST['add'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    mysqli_query($conn, "INSERT INTO kategori (nama) VALUES ('$nama')");
    header('Location: kategori.php');
    exit;
}

// update kategori
if (isset($_POST['update'])) {
    $id   = intval($_POST['id']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    mysqli_query($conn,
        "UPDATE kategori SET nama='$nama' WHERE id=$id"
    );

    header('Location: kategori.php');
    exit;
}

// data kategori
$cats = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Kategori</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Kelola Kategori</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="menu.php">Menu</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h3><?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?></h3>

<form method="post">

<input type="hidden" name="id"
       value="<?= $edit ? $dataEdit['id'] : '' ?>">

<label>Nama Kategori</label>
<input type="text" name="nama" required
       value="<?= $edit ? htmlspecialchars($dataEdit['nama']) : '' ?>">

<button type="submit"
        name="<?= $edit ? 'update' : 'add' ?>"
        class="btn">
  <?= $edit ? 'Update' : 'Tambah' ?>
</button>

<?php if ($edit): ?>
  <a href="kategori.php" class="btn btn-secondary">Batal</a>
<?php endif; ?>

</form>

<?php if (isset($_GET['error']) && $_GET['error'] === 'dipakai'): ?>
  <p style="color:red; margin-top:10px;">
    Kategori tidak bisa dihapus karena masih digunakan oleh menu.
  </p>
<?php endif; ?>

<hr>

<h3>Daftar Kategori</h3>

<table>
<tr>
  <th>Nama</th>
  <th>Aksi</th>
</tr>

<?php while ($c = mysqli_fetch_assoc($cats)) { ?>
<tr>
  <td><?= htmlspecialchars($c['nama']) ?></td>
  <td>
    <a href="?edit=<?= $c['id'] ?>">Edit</a> |
    <a href="?hapus=<?= $c['id'] ?>"
       onclick="return confirm('Yakin hapus kategori ini?')"
       style="color:red;">
       Hapus
    </a>
  </td>
</tr>
<?php } ?>

</table>

</main>
</body>
</html>
