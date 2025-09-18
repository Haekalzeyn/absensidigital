<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin']) && !isset($_SESSION['guru'])) {
    header("Location: index.php");
    exit;
}

$isAdmin = isset($_SESSION['admin']);
$result = mysqli_query($conn, "SELECT id, username, created_at FROM guru ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Guru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
  <link rel="stylesheet" href="css/guru.css">
</head>
<body>


<div class="sidebar">
  <h4><i class="fas fa-school"></i> SIAKAD</h4>
  <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="absensi.php"><i class="fas fa-clipboard-check"></i> Absensi</a>
  <a href="siswa.php"><i class="fas fa-users"></i> Data Murid</a>
  <a href="guru.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Data User </a>
  <a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>


<nav class="navbar navbar-expand navbar-light px-4">
  <span class="navbar-brand mb-0 h4"><i class="fas fa-chalkboard-teacher"></i> Data User</span>
  <div class="ms-auto d-flex align-items-center">
    <span class="me-3 text-muted">Halo, <?= $isAdmin ? $_SESSION['admin'] : $_SESSION['guru'] ?></span>
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($isAdmin ? $_SESSION['admin'] : $_SESSION['guru']) ?>&background=4e73df&color=fff" 
         alt="user" class="rounded-circle" width="35" height="35">
  </div>
</nav>


<div class="content">
  <div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold mb-0"><i class="fas fa-chalkboard-teacher"></i> Daftar User</h4>
      <?php if ($isAdmin): ?>
        <a href="tambah_guru.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Guru</a>
      <?php endif; ?>
    </div>

   
    <div class="input-group mb-3" style="max-width:300px;">
      <span class="input-group-text"><i class="fas fa-search"></i></span>
      <input type="text" id="searchInput" class="form-control" placeholder="Cari guru...">
    </div>


    <div class="table-responsive">
  <table class="table table-hover align-middle" id="guruTable">
        <thead>
          <tr>
            <th>No</th>
            <th>Username</th>
            <th>Tanggal Daftar</th>
            <?php if ($isAdmin): ?><th>Aksi</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td class="text-center"><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td class="text-center"><?= $row['created_at'] ?></td>
              <?php if ($isAdmin): ?>
              <td class="text-center">
                <a href="edit_guru.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                  <i class="fas fa-edit"></i> Edit
                </a>
                <a href="hapus_guru.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin ingin hapus guru ini?')">
                   <i class="fas fa-trash"></i> Hapus
                </a>
              </td>
              <?php endif; ?>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?= $isAdmin ? 4 : 3 ?>" class="text-center text-muted">Belum ada data guru</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>

document.getElementById('searchInput').addEventListener('keyup', function() {
  let filter = this.value.toLowerCase();
  let rows = document.querySelectorAll('#guruTable tbody tr');
  rows.forEach(row => {
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
});
</script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    new DataTable('#guruTable');
  });
</script>
</body>
</html>
