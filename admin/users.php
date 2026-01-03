<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// auth
if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['admin']['role'] !== 'admin') {
    echo 'Akses ditolak';
    exit;
}

// hapus user
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // cegah admin hapus dirinya sendiri
    if ($id == $_SESSION['admin']['id']) {
        header('Location: users.php');
        exit;
    }

    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header('Location: users.php');
    exit;
}

// mode edit
$edit = false;
$dataEdit = null;

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);

    // cegah admin edit dirinya sendiri
    if ($id == $_SESSION['admin']['id']) {
        header('Location: users.php');
        exit;
    }

    $edit = true;
    $q = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    if ($q && mysqli_num_rows($q)) {
        $dataEdit = mysqli_fetch_assoc($q);
    }
}

// tambah user
if (isset($_POST['add'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);
    $role  = $_POST['role'];

    mysqli_query($conn,
        "INSERT INTO users (nama, email, password, role)
         VALUES ('$nama', '$email', '$pass', '$role')"
    );

    header('Location: users.php');
    exit;
}

// update user
if (isset($_POST['update'])) {
    $id    = intval($_POST['id']);
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role  = $_POST['role'];

    // proteksi backend 
    if ($id == $_SESSION['admin']['id']) {
        header('Location: users.php');
        exit;
    }

    mysqli_query($conn,
        "UPDATE users SET
         nama='$nama',
         email='$email',
         role='$role'
         WHERE id=$id"
    );

    header('Location: users.php');
    exit;
}

// ambil data user
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role, nama");
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manajemen User</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Manajemen User</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="menu.php">Menu</a>
    <a href="kategori.php">Kategori</a>
    <a href="pesanan.php">Pesanan</a>
    <a href="transaksi.php">Transaksi</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h3><?= $edit ? 'Edit User' : 'Tambah User' ?></h3>

<form method="post">

<input type="hidden" name="id"
       value="<?= $edit ? $dataEdit['id'] : '' ?>">

<label>Nama</label>
<input type="text" name="nama" required
       value="<?= $edit ? htmlspecialchars($dataEdit['nama']) : '' ?>">

<label>Email</label>
<input type="email" name="email" required
       value="<?= $edit ? htmlspecialchars($dataEdit['email']) : '' ?>">

<?php if (!$edit): ?>
<label>Password</label>
<input type="password" name="password" required>
<?php endif; ?>

<label>Role</label>
<select name="role" required>
  <?php foreach (['admin','kasir','dapur','waiter'] as $r): ?>
    <option value="<?= $r ?>"
      <?= $edit && $dataEdit['role'] == $r ? 'selected' : '' ?>>
      <?= ucfirst($r) ?>
    </option>
  <?php endforeach; ?>
</select>

<button type="submit"
        name="<?= $edit ? 'update' : 'add' ?>"
        class="btn">
  <?= $edit ? 'Update User' : 'Tambah User' ?>
</button>

<?php if ($edit): ?>
  <a href="users.php" class="btn btn-secondary">Batal</a>
<?php endif; ?>

</form>

<hr>

<h3>Daftar User</h3>

<table>
<tr>
  <th>Nama</th>
  <th>Email</th>
  <th>Role</th>
  <th>Aksi</th>
</tr>

<?php while ($u = mysqli_fetch_assoc($users)) { ?>
<tr>
  <td><?= htmlspecialchars($u['nama']) ?></td>
  <td><?= htmlspecialchars($u['email']) ?></td>
  <td><?= ucfirst($u['role']) ?></td>
  <td>
    <?php if ($u['id'] != $_SESSION['admin']['id']) { ?>
      <a href="?edit=<?= $u['id'] ?>">Edit</a> |
      <a href="?hapus=<?= $u['id'] ?>"
         onclick="return confirm('Yakin hapus user ini?')"
         style="color:red;">Hapus</a>
    <?php } else { ?>
      <span style="color:#6b7280;">(Anda)</span>
    <?php } ?>
  </td>
</tr>
<?php } ?>

</table>

</main>
</body>
</html>
