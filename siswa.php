<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin']) && !isset($_SESSION['guru'])) {
    header("Location: index.php");
    exit;
}

$isAdmin = isset($_SESSION['admin']);

// Handle Excel Import
if ($isAdmin && isset($_FILES['file_excel'])) {
    require 'vendor/autoload.php';

    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['file_excel']['tmp_name']);
        $spreadsheet = IOFactory::load($_FILES['file_excel']['tmp_name']);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $imported = 0;
        $skipped = 0;

        // Skip header row
        for ($i = 1; $i < count($rows); $i++) {
            $nama  = mysqli_real_escape_string($conn, $rows[$i][0]); 
            $kelas = mysqli_real_escape_string($conn, $rows[$i][1]); 
            $nis   = mysqli_real_escape_string($conn, $rows[$i][2]); 

            if (!empty($nama) && !empty($kelas) && !empty($nis)) {
                // Check for duplicate NIS
                $cek = mysqli_query($conn, "SELECT id FROM murid WHERE nis='$nis' LIMIT 1");
                if (mysqli_num_rows($cek) == 0) {
                    mysqli_query($conn, "INSERT INTO murid (nama, kelas, nis) VALUES ('$nama','$kelas','$nis')");
                    $imported++;
                } else {
                    $skipped++;
                }
            }
        }

        $_SESSION['success'] = "Import berhasil! $imported data ditambahkan, $skipped data dilewati.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal import: " . $e->getMessage();
    }
}

$kelasQuery = mysqli_query($conn, "SELECT DISTINCT kelas FROM murid ORDER BY kelas ASC");

$selectedKelas = isset($_GET['kelas']) ? $_GET['kelas'] : "";

if ($selectedKelas) {
    $result = mysqli_query($conn, "SELECT * FROM murid WHERE kelas='$selectedKelas' ORDER BY nama ASC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM murid ORDER BY nama ASC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Siswa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="css/siswa.css">
</head>
<body>

<div class="sidebar">
  <h4><i class="fas fa-school"></i> SIAKAD</h4>
  <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="absensi.php"><i class="fas fa-clipboard-check"></i> Absensi</a>
  <a href="siswa.php" class="bg-white text-dark"><i class="fas fa-users"></i> Data Siswa</a>
  <a href="guru.php"><i class="fas fa-chalkboard-teacher"></i> Data User</a>
  <a href="laporan.php"><i class="fas fa-file-excel"></i> Laporan</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<nav class="navbar navbar-expand navbar-light px-4">
  <span class="navbar-brand mb-0 h4">👨‍🎓 Data Siswa</span>
  <div class="ms-auto">
    <span class="me-3 text-muted">Halo, <?= $isAdmin ? $_SESSION['admin'] : $_SESSION['guru'] ?></span>
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($isAdmin ? $_SESSION['admin'] : $_SESSION['guru']) ?>&background=4e73df&color=fff" 
         alt="user" class="rounded-circle" width="35" height="35">
  </div>
</nav>

<div class="content">
  <div class="card p-4">

    <?php if(isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold mb-0"><i class="fas fa-users"></i> Data Siswa</h4>
      <?php if ($isAdmin): ?>
        <div class="d-flex gap-2">
          <a href="tambah_siswa.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Siswa
          </a>
          <!-- Import Excel Section -->
          <div class="d-flex gap-2">
            <a href="import_siswa.php" class="btn btn-info">
              <i class="fas fa-download"></i> Import Data
            </a>
          </div>

          <!-- Modal Import -->
          <div class="modal fade" id="importModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><i class="fas fa-file-excel"></i> Import Data Siswa</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="alert alert-info">
                    <ol class="mb-0">
                      <li>Download template Excel terlebih dahulu</li>
                      <li>Isi data sesuai format yang tersedia</li>
                      <li>Pastikan kolom NIS tidak duplikat</li>
                      <li>Upload file yang sudah diisi</li>
                    </ol>
                  </div>
                  <form action="siswa.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                      <label class="form-label">Pilih File Excel</label>
                      <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="text-end">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- filter kelas -->
    <form method="get" class="mb-3">
      <div class="input-group">
        <label class="input-group-text"><i class="fas fa-school"></i> Pilih Kelas</label>
        <select name="kelas" class="form-select" onchange="this.form.submit()">
          <option value="">-- Semua Kelas --</option>
          <?php while ($k = mysqli_fetch_assoc($kelasQuery)): ?>
            <option value="<?= $k['kelas'] ?>" <?= $selectedKelas == $k['kelas'] ? 'selected' : '' ?>>
              <?= $k['kelas'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
    </form>

    <!-- search -->
    <div class="input-group mb-3">
      <span class="input-group-text"><i class="fas fa-search"></i></span>
      <input type="text" id="searchInput" class="form-control" placeholder="Cari siswa...">
    </div>

    <!-- tabel siswa -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center" id="siswaTable">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>NIS</th>
            <?php if ($isAdmin): ?><th>Aksi</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['kelas']) ?></td>
            <td><?= htmlspecialchars($row['nis']) ?></td>
            <?php if ($isAdmin): ?>
            <td>
              <a href="edit_siswa.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i>
              </a>
              <a href="hapus_siswa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Yakin ingin hapus siswa ini?')">
                 <i class="fas fa-trash"></i>
              </a>
            </td>
            <?php endif; ?>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script>
  document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#siswaTable tbody tr');
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
    new DataTable('#siswaTable');
  });
</script>

</body>
</html>
