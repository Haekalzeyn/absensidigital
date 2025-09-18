<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin']) && !isset($_SESSION['guru'])) {
    header("Location: index.php");
    exit;
}

$guru_id = null;
$isAdmin = isset($_SESSION['admin']);
if (isset($_SESSION['guru'])) {
    $usernameGuru = $_SESSION['guru']; 
    $qGuru = mysqli_query($conn, "SELECT id FROM guru WHERE username='$usernameGuru' LIMIT 1");
    if ($qGuru && mysqli_num_rows($qGuru) > 0) {
        $guru_id = mysqli_fetch_assoc($qGuru)['id'];
    }
}

/* === Hapus absensi jika admin klik tombol hapus === */
if ($isAdmin && isset($_GET['hapus'])) {
    $murid_id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM absensi WHERE murid_id='$murid_id' AND tanggal=CURDATE()");
    header("Location: absensi.php?kelas=" . urlencode($_GET['kelas'] ?? ""));
    exit;
}

/* === Input absensi (guru) === */
if (isset($_POST['submit_absensi']) && $guru_id) {
    $murid_id = $_POST['murid_id'];
    $status   = $_POST['status'];

    $cek = mysqli_query($conn, "
        SELECT id FROM absensi 
        WHERE murid_id='$murid_id' 
          AND guru_id='$guru_id' 
          AND tanggal=CURDATE()
    ");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "
            INSERT INTO absensi (murid_id, guru_id, status, tanggal) 
            VALUES ('$murid_id','$guru_id','$status',CURDATE())
        ");
    }
    exit; 
}

/* === Data kelas & murid === */
$kelasQuery = mysqli_query($conn, "SELECT DISTINCT kelas FROM murid ORDER BY kelas ASC");

$selectedKelas = isset($_GET['kelas']) ? $_GET['kelas'] : "";

if ($selectedKelas) {
    $result = mysqli_query($conn, "SELECT * FROM murid WHERE kelas='$selectedKelas' ORDER BY nama ASC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM murid ORDER BY nama ASC");
}

/* === Ambil absensi hari ini === */
$absensiHariIni = [];
$whereGuru = $guru_id ? "AND guru_id='$guru_id'" : "";
$q = mysqli_query($conn, "SELECT * FROM absensi WHERE tanggal=CURDATE() $whereGuru");
while ($row = mysqli_fetch_assoc($q)) {
    $absensiHariIni[$row['murid_id']] = $row['status'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Absensi Murid</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="css/absensi.css">
</head>
<body>

<div class="sidebar">
  <h4><i class="fas fa-school"></i> SIAKAD</h4>
  <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="absensi.php" class="bg-white text-dark"><i class="fas fa-clipboard-check"></i> Absensi</a>
  <a href="siswa.php"><i class="fas fa-users"></i> Data Murid</a>
  <a href="guru.php"><i class="fas fa-chalkboard-teacher"></i> Data User </a>
  <a href="laporan.php"><i class="fas fa-file-excel"></i> Laporan</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<nav class="navbar navbar-expand navbar-light px-4">
  <span class="navbar-brand mb-0 h4">📋 Absensi Murid Hari Ini (<?= date("d-m-Y") ?>)</span>
  <div class="ms-auto">
    <span class="me-3 text-muted">Halo, <?= $isAdmin ? $_SESSION['admin'] : $_SESSION['guru'] ?></span>
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($isAdmin ? $_SESSION['admin'] : $_SESSION['guru']) ?>&background=4e73df&color=fff" 
         alt="user" class="rounded-circle" width="35" height="35">
  </div>
</nav>

<div class="content">
  <div class="card p-4">
   
    <form method="get" class="mb-4">
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

    <?php if($isAdmin): ?>
    <form method="post" action="export_excel.php?kelas=<?= $selectedKelas ?>" class="mb-3">
      <button type="submit" class="btn btn-success"><i class="fas fa-download"></i> Simpan / Download Excel</button>
    </form>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center" id="absensiTable">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <?php if(!$isAdmin): ?><th>Absensi</th><?php else: ?><th>Status Hari Ini</th><th>Aksi</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
        <?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['kelas']) ?></td>
            <td>
              <?php if(!$isAdmin): ?>
                <?php if(isset($absensiHariIni[$row['id']])): ?>
                  <span class="btn btn-success btn-sm disabled">✓</span>
                <?php else: ?>
                  <form method="post" class="d-flex align-items-center justify-content-center" onsubmit="markDone(event,this)">
                    <input type="hidden" name="murid_id" value="<?= $row['id'] ?>">
                    <select name="status" class="form-select form-select-sm me-2 w-auto">
                      <option value="Hadir">Hadir</option>
                      <option value="Izin">Izin</option>
                      <option value="Sakit">Sakit</option>
                      <option value="Alfa">Alfa</option>
                    </select>
                    <button type="submit" name="submit_absensi" class="btn btn-primary btn-sm">OK</button>
                  </form>
                <?php endif; ?>
              <?php else: ?>
                <?= isset($absensiHariIni[$row['id']]) ? $absensiHariIni[$row['id']] : '<span class="text-muted">Belum Absen</span>' ?>
              </td>
              <td>
                <?php if(isset($absensiHariIni[$row['id']])): ?>
                  <a href="?hapus=<?= $row['id'] ?>&kelas=<?= $selectedKelas ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Yakin ingin hapus absensi murid ini?')">
                     <i class="fas fa-trash"></i>
                  </a>
                <?php endif; ?>
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
function markDone(e, form){
    e.preventDefault();
    const murid_id = form.querySelector('input[name="murid_id"]').value;
    const status = form.querySelector('select[name="status"]').value;

    fetch('absensi.php?kelas=<?= $selectedKelas ?>', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`submit_absensi=1&murid_id=${murid_id}&status=${status}`
    })
    .then(()=> {
        const btn = form.querySelector('button');
        btn.innerHTML = '✓';
        btn.className = 'btn btn-success btn-sm';
        form.querySelector('select').disabled = true;
        btn.disabled = true;
    })
    .catch(err => console.error(err));
}
</script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    new DataTable('#absensiTable');
  });
</script>

</body>
</html>
