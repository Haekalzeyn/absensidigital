<?php
require "koneksi.php";
session_start();

// Hanya admin yang bisa akses
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// Ambil filter kelas dari URL (opsional)
$kelas = isset($_GET['kelas']) ? mysqli_real_escape_string($conn, $_GET['kelas']) : "";

// Nama file export
$filename = "absensi_" . date("Y-m-d") . ($kelas ? "_kelas_$kelas" : "") . ".xls";

// Header agar otomatis download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");


echo "
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 12pt;
    }
    th {
        background-color: #4e73df;
        color: white;
        text-align: center;
        padding: 8px;
        border: 1px solid #000;
    }
    td {
        padding: 6px;
        border: 1px solid #000;
    }
    h2 {
        text-align: center;
        font-family: Arial, sans-serif;
    }
    .info {
        margin-bottom: 15px;
        font-weight: bold;
    }
</style>
";

// Judul laporan
echo "<h2>Laporan Absensi Murid</h2>";
echo "<div class='info'>Tanggal: " . date("d-m-Y") . "</div>";
if ($kelas) {
    echo "<div class='info'>Kelas: $kelas</div>";
}


echo "<table>";
echo "<tr>
        <th>No</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Status</th>
        <th>Tanggal</th>
      </tr>";


$sql = "
    SELECT a.*, m.nama, m.kelas 
    FROM absensi a 
    JOIN murid m ON a.murid_id = m.id
    WHERE a.tanggal = CURDATE()
";
if ($kelas) {
    $sql .= " AND m.kelas='$kelas'";
}
$sql .= " ORDER BY m.nama ASC";

$q = mysqli_query($conn, $sql);
$no = 1;
while($row = mysqli_fetch_assoc($q)){
    echo "<tr>
            <td align='center'>{$no}</td>
            <td>{$row['nama']}</td>
            <td align='center'>{$row['kelas']}</td>
            <td align='center'>{$row['status']}</td>
            <td align='center'>{$row['tanggal']}</td>
          </tr>";
    $no++;
}

echo "</table>";
exit;
?>
