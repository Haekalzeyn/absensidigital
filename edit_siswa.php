<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin']) && !isset($_SESSION['guru'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM murid WHERE id=$id");
$siswa = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);

    $query = "UPDATE murid SET nama='$nama', kelas='$kelas', nis='$nis' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        header("Location: siswa.php");
        exit;
    } else {
        echo "<script>alert('Gagal update siswa!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h2>Edit Siswa</h2>
  <form method="post">
    <div class="mb-3">
      <label>Nama</label>
      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($siswa['nama']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Kelas</label>
      <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($siswa['kelas']) ?>" required>
    </div>
    <div class="mb-3">
      <label>NIS</label>
      <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($siswa['nis']) ?>" required>
    </div>
    <button type="submit" name="update" class="btn btn-primary">Update</button>
    <a href="siswa.php" class="btn btn-secondary">Batal</a>
  </form>
</body>
</html>
