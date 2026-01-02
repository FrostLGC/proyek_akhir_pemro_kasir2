<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../public/login.php');
    exit;
}

if ($_SESSION['admin']['role'] !== 'admin') {
    echo "Akses ditolak. Hanya admin yang boleh mengelola menu.";
    exit;
}

// hapus menu
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    $q = mysqli_query($conn, "SELECT foto FROM menu WHERE id=$id");
    if ($q && mysqli_num_rows($q)) {
        $m = mysqli_fetch_assoc($q);

        if ($m['foto']) {
            $path = __DIR__ . '/../uploads/gambar_menu/' . $m['foto'];
            if (file_exists($path)) unlink($path);
        }

        mysqli_query($conn, "DELETE FROM menu WHERE id=$id");
    }

    header('Location: menu.php');
    exit;
}

// mode edit
$edit = false;
$dataEdit = null;

if (isset($_GET['edit'])) {
    $edit = true;
    $id = intval($_GET['edit']);
    $q = mysqli_query($conn, "SELECT * FROM menu WHERE id=$id");
    if ($q && mysqli_num_rows($q)) {
        $dataEdit = mysqli_fetch_assoc($q);
    }
}

// tambah menu
if (isset($_POST['add'])) {

    $nama     = mysqli_real_escape_string($conn,$_POST['nama_menu']);
    $harga    = intval($_POST['harga']);
    $kategori = intval($_POST['kategori_id']);
    $des      = mysqli_real_escape_string($conn,$_POST['deskripsi']);
    $foto     = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {

        $f = $_FILES['foto'];
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext,$allowed)) {
            echo "Format gambar tidak didukung";
            exit;
        }

        if ($f['size'] > 2 * 1024 * 1024) {
            echo "Ukuran gambar maksimal 2MB";
            exit;
        }

        $foto = time().'_'.rand(100,999).'.'.$ext;
        move_uploaded_file(
            $f['tmp_name'],
            __DIR__ . '/../uploads/gambar_menu/' . $foto
        );
    }

    mysqli_query($conn,
        "INSERT INTO menu (kategori_id,nama_menu,harga,foto,deskripsi)
         VALUES ($kategori,'$nama',$harga,'$foto','$des')"
    );

    header('Location: menu.php');
    exit;
}

// update menu
if (isset($_POST['update'])) {

    $id       = intval($_POST['id']);
    $nama     = mysqli_real_escape_string($conn,$_POST['nama_menu']);
    $harga    = intval($_POST['harga']);
    $kategori = intval($_POST['kategori_id']);
    $des      = mysqli_real_escape_string($conn,$_POST['deskripsi']);

    $q = mysqli_query($conn, "SELECT foto FROM menu WHERE id=$id");
    $old = mysqli_fetch_assoc($q);
    $foto = $old['foto'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {

        $f = $_FILES['foto'];
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext,$allowed)) {
            echo "Format gambar tidak valid";
            exit;
        }

        if ($f['size'] > 2 * 1024 * 1024) {
            echo "Ukuran gambar maksimal 2MB";
            exit;
        }

        if ($foto) {
            $oldPath = __DIR__ . '/../uploads/gambar_menu/' . $foto;
            if (file_exists($oldPath)) unlink($oldPath);
        }

        $foto = time().'_'.rand(100,999).'.'.$ext;
        move_uploaded_file(
            $f['tmp_name'],
            __DIR__ . '/../uploads/gambar_menu/' . $foto
        );
    }

    mysqli_query($conn,
        "UPDATE menu SET
         kategori_id=$kategori,
         nama_menu='$nama',
         harga=$harga,
         foto='$foto',
         deskripsi='$des'
         WHERE id=$id"
    );

    header('Location: menu.php');
    exit;
}

// ambil data
$cats = mysqli_query($conn, "SELECT * FROM kategori");
$menus = mysqli_query($conn,
    "SELECT m.*, k.nama AS kategori
     FROM menu m
     LEFT JOIN kategori k ON m.kategori_id=k.id
     ORDER BY k.id ASC, m.nama_menu ASC"
);
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Menu</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
  <h2>Kelola Menu</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="kategori.php">Kategori</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

<h3><?php echo $edit ? 'Edit Menu' : 'Tambah Menu'; ?></h3>

<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?php echo $edit ? $dataEdit['id'] : ''; ?>">

<label>Nama Menu</label>
<input type="text" name="nama_menu" required
value="<?php echo $edit ? htmlspecialchars($dataEdit['nama_menu']) : ''; ?>">

<label>Harga</label>
<input type="number" name="harga" required
value="<?php echo $edit ? $dataEdit['harga'] : ''; ?>">

<label>Kategori</label>
<select name="kategori_id">
<?php mysqli_data_seek($cats,0); while($c=mysqli_fetch_assoc($cats)){ ?>
<option value="<?php echo $c['id']; ?>"
<?php if($edit && $c['id']==$dataEdit['kategori_id']) echo 'selected'; ?>>
<?php echo htmlspecialchars($c['nama']); ?>
</option>
<?php } ?>
</select>

<label>Foto</label>
<input type="file" name="foto">
<?php if($edit && $dataEdit['foto']){ ?>
<small>Foto lama: <?php echo $dataEdit['foto']; ?></small>
<?php } ?>

<label>Deskripsi</label>
<textarea name="deskripsi"><?php
echo $edit ? htmlspecialchars($dataEdit['deskripsi']) : '';
?></textarea>

<button type="submit" name="<?php echo $edit ? 'update' : 'add'; ?>">
<?php echo $edit ? 'Update' : 'Simpan'; ?>
</button>

<?php if($edit){ ?>
<a href="menu.php" class="btn btn-secondary">Batal</a>
<?php } ?>

</form>

<hr>

<h3>Daftar Menu</h3>

<table>
<tr>
  <th>Nama</th>
  <th>Kategori</th>
  <th>Harga</th>
  <th>Aksi</th>
</tr>

<?php while($m=mysqli_fetch_assoc($menus)){ ?>
<tr>
  <td><?php echo htmlspecialchars($m['nama_menu']); ?></td>
  <td><?php echo htmlspecialchars($m['kategori']); ?></td>
  <td>Rp <?php echo number_format($m['harga']); ?></td>
  <td>
    <a href="?edit=<?php echo $m['id']; ?>">Edit</a> |
    <a href="?hapus=<?php echo $m['id']; ?>"
       onclick="return confirm('Yakin hapus menu ini?')"
       style="color:red;">Hapus</a>
  </td>
</tr>
<?php } ?>

</table>

</main>
</body>
</html>
