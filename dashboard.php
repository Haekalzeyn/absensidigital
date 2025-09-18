<?php
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['guru'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="d-flex flex-column min-vh-100">

  <nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand">📘 Absensi Digital</a>
      <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </nav>

  
  <div class="container text-center py-5">
    <h2 class="fw-bold text-dark">
      Selamat datang, 
      <?php 
        if (isset($_SESSION['admin'])) echo " <span class='text-primary'>" . $_SESSION['admin'] . "</span>";
        if (isset($_SESSION['guru'])) echo "<span class='text-success'>" . $_SESSION['guru'] . "</span>";
      ?> 🎉
    </h2>
    <p class="text-muted">Silakan pilih menu di bawah untuk melanjutkan</p>
  </div>

  
  <div class="container pb-5">
    <div class="row g-4 justify-content-center">

  
      <div class="col-md-4">
        <div class="card text-center p-4">
          <div class="icon-bg bg-primary mx-auto mb-3">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <h5 class="fw-bold">Data User</h5>
          <p class="text-muted">Kelola akun dan informasi User</p>
          <a href="guru.php" class="btn btn-primary">Lihat Data</a>
        </div>
      </div>

      
      <div class="col-md-4">
        <div class="card text-center p-4">
          <div class="icon-bg bg-success mx-auto mb-3">
            <i class="fas fa-user-graduate"></i>
          </div>
          <h5 class="fw-bold">Data Siswa</h5>
          <p class="text-muted">Atur data siswa dan absensi mereka</p>
          <a href="siswa.php" class="btn btn-success">Lihat Data</a>
        </div>
      </div>

   
      <div class="col-md-4">
        <div class="card text-center p-4">
          <div class="icon-bg bg-info mx-auto mb-3">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <h5 class="fw-bold">Absensi</h5>
          <p class="text-muted">Cek dan kelola kehadiran siswa</p>
          <a href="absensi.php" class="btn btn-info text-white">Buka Absensi</a>
        </div>
      </div>

    </div>
  </div>

  <footer class="text-center py-3 bg-light border-top mt-auto">
    <small class="text-muted">&copy; <?= date("Y") ?> Absensi Digital</small>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
