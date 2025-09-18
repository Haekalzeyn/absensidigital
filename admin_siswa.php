<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}


if (isset($_POST['tambah'])) {
    $nama  = $_POST['nama'];
    $kelas = $_POST['kelas'];
    mysqli_query($conn, "INSERT INTO siswa (nama, kelas) VALUES ('$nama','$kelas')");
    header("Location: admin_siswa.php");
    exit;
}


if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM siswa WHERE id='$id'");
    header("Location: admin_siswa.php");
    exit;
}


if (isset($_POST['update'])) {
    $id    = $_POST['id'];
    $nama  = $_POST['nama'];
    $kelas = $_POST['kelas'];
    mysqli_query($conn, "UPDATE siswa SET nama='$nama', kelas='$kelas' WHERE id='$id'");
    header("Location: admin_siswa.php");
    exit;
}

$siswa = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand">Kelola Siswa (Admin)</a>
      <a href="dashboard.php" class="btn btn-outline-light">⬅ Kembali</a>
    </div>
  </nav>

  <div class="container py-4">
    <h2 class="mb-4">📚 Data Siswa</h2>

 
    <div class="card mb-4">
      <div class="card-body">
        <h5>Tambah Siswa</h5>
        <form method="post" class="row g-2">
          <div class="col-md-5">
            <input type="text" name="nama" class="form-control" placeholder="Nama siswa" required>
          </div>
          <div class="col-md-5">
            <input type="text" name="kelas" class="form-control" placeholder="Kelas" required>
          </div>
          <div class="col-md-2">
            <button type="submit" name="tambah" class="btn btn-success w-100">Tambah</button>
          </div>
        </form>
      </div>
    </div>


  <table class="table table-bordered table-striped" id="adminSiswaTable">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Kelas</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($row=mysqli_fetch_assoc($siswa)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['nama'] ?></td>
          <td><?= $row['kelas'] ?></td>
          <td>
        
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id'] ?>">Edit</button>
            
            <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus siswa ini?')" class="btn btn-danger btn-sm">Hapus</a>
          </td>
        </tr>

        <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="post">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Siswa</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" value="<?= $row['nama'] ?>" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>Kelas</label>
                    <input type="text" name="kelas" value="<?= $row['kelas'] ?>" class="form-control" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new DataTable('#adminSiswaTable');
    });
  </script>
</body>
</html>
