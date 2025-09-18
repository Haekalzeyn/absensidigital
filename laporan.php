<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin']) && !isset($_SESSION['guru'])) {
    header("Location: index.php");
    exit;
}

$isAdmin = isset($_SESSION['admin']);


$guru_id = null;
if (!$isAdmin && isset($_SESSION['guru'])) {
    $usernameGuru = $_SESSION['guru'];
    $qGuru = mysqli_query($conn, "SELECT id FROM guru WHERE username='$usernameGuru' LIMIT 1");
    if ($qGuru && mysqli_num_rows($qGuru) > 0) {
        $guru_id = mysqli_fetch_assoc($qGuru)['id'];
    }
}


$kelasQuery = mysqli_query($conn, "SELECT DISTINCT kelas FROM murid ORDER BY kelas ASC");


$selectedKelas = isset($_GET['kelas']) ? $_GET['kelas'] : "";
$tglMulai = isset($_GET['mulai']) ? $_GET['mulai'] : date("Y-m-01");
$tglAkhir = isset($_GET['akhir']) ? $_GET['akhir'] : date("Y-m-d");


$sql = "
    SELECT m.kelas, m.nama,
        SUM(a.status='Hadir') as hadir,
        SUM(a.status='Izin') as izin,
        SUM(a.status='Sakit') as sakit,
        SUM(a.status='Alfa') as alfa
    FROM murid m
    LEFT JOIN absensi a 
        ON m.id = a.murid_id 
        AND a.tanggal BETWEEN '$tglMulai' AND '$tglAkhir'
        " . ($guru_id ? " AND a.guru_id='$guru_id'" : "") . "
    WHERE 1=1
    " . ($selectedKelas ? " AND m.kelas='$selectedKelas'" : "") . "
    GROUP BY m.id
    ORDER BY m.kelas, m.nama
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Absensi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="css/laporan.css">

</head>
<body>


<div class="sidebar">
  <h4><i class="fas fa-school"></i> SIAKAD</h4>
  <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="absensi.php"><i class="fas fa-clipboard-check"></i> Absensi</a>
  <a href="siswa.php"><i class="fas fa-users"></i> Data Murid</a>
  <a href="guru.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Data User </a>
  <a href="laporan.php" class="bg-white text-dark"><i class="fas fa-file-alt"></i> Laporan</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>


<nav class="navbar navbar-expand navbar-light px-4">
  <span class="navbar-brand mb-0 h4">📊 Laporan Absensi</span>
  <div class="ms-auto">
    <span class="me-3 text-muted">Halo, <?= $isAdmin ? $_SESSION['admin'] : $_SESSION['guru'] ?></span>
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($isAdmin ? $_SESSION['admin'] : $_SESSION['guru']) ?>&background=4e73df&color=fff" 
         alt="user" class="rounded-circle" width="35" height="35">
  </div>
</nav>

<div class="content">
  <div class="card p-4">
  
    <form method="get" class="row g-3 mb-4">
      <div class="col-md-3">
        <label class="form-label">Kelas</label>
        <select name="kelas" class="form-select">
          <option value="">-- Semua Kelas --</option>
          <?php while ($k = mysqli_fetch_assoc($kelasQuery)): ?>
            <option value="<?= $k['kelas'] ?>" <?= $selectedKelas == $k['kelas'] ? 'selected' : '' ?>>
              <?= $k['kelas'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Dari</label>
        <input type="date" name="mulai" class="form-control" value="<?= $tglMulai ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Sampai</label>
        <input type="date" name="akhir" class="form-control" value="<?= $tglAkhir ?>">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tampilkan</button>
      </div>
    </form>

    
    <?php if ($isAdmin): ?>
    <form method="post" action="export_excel.php?kelas=<?= $selectedKelas ?>&mulai=<?= $tglMulai ?>&akhir=<?= $tglAkhir ?>">
      <button type="submit" class="btn btn-success mb-3"><i class="fas fa-file-excel"></i> Export Excel</button>
    </form>
    <?php endif; ?>

  
    <div class="table-responsive">
  <table class="table table-bordered table-hover text-center" id="laporanTable">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Hadir</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>Alfa</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no=1;
          if (mysqli_num_rows($result)>0):
            while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td class="text-start"><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= $row['kelas'] ?></td>
              <td><?= $row['hadir'] ?></td>
              <td><?= $row['izin'] ?></td>
              <td><?= $row['sakit'] ?></td>
              <td><?= $row['alfa'] ?></td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    new DataTable('#laporanTable');
  });
</script>
</html>
