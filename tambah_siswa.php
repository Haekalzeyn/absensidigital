<?php
session_start();
require "koneksi.php";


if (!isset($_SESSION['admin']) && !isset($_SESSION['guru'])) {
    header("Location: index.php");
    exit;
}


if (isset($_POST['simpan'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $nis   = mysqli_real_escape_string($conn, $_POST['nis']);

    $query = "INSERT INTO murid (nama, kelas, nis) VALUES ('$nama','$kelas','$nis')";
    if (mysqli_query($conn, $query)) {
        header("Location: siswa.php");
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan data siswa!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">


  <nav class="navbar navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand">Absensi Digital</a>
      <a href="dashboard.php" class="btn btn-outline-light">Dashboard</a>
    </div>
  </nav>


  <div class="container flex-grow-1 py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg">
          <div class="card-header bg-success text-white text-center">
            <h4>Tambah Data Siswa</h4>
          </div>
          <div class="card-body">
            <form method="post">
              <div class="mb-3">
                <label for="nama" class="form-label">Nama Siswa</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
              </div>
              <div class="mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <input type="text" class="form-control" id="kelas" name="kelas" placeholder="Contoh: XI RPL 5" required>
              </div>
              <div class="mb-3">
                <label for="nis" class="form-label">NIS</label>
                <input type="text" class="form-control" id="nis" name="nis" required>
              </div>
              <div class="d-grid">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="siswa.php" class="btn btn-secondary mt-2">Kembali</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>


  <footer class="bg-dark text-white text-center py-3 mt-auto">
    <small>© <?= date("Y") ?> Absensi Digital | SMK Taruna Bangsa</small>
  </footer>

</body>
</html>
