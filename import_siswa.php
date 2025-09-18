<?php
session_start();
require "koneksi.php";

// pastikan hanya admin
if (!isset($_SESSION['admin'])) {
    header("Location: siswa.php");
    exit;
}

// load library PhpSpreadsheet
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data Siswa - SIAKAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        .import-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .steps {
            margin: 20px 0;
            padding: 0;
            list-style-position: inside;
        }
        .steps li {
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .file-upload {
            border: 2px dashed #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="import-container">
            <h2 class="text-center mb-4">Import Data Siswa</h2>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success']; ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    Panduan Import
                </div>
                <div class="card-body">
                    <ol class="steps">
                        <li>Download template Excel terlebih dahulu</li>
                        <li>Isi data sesuai format yang tersedia</li>
                        <li>Pastikan kolom NIS tidak duplikat</li>
                        <li>Upload file yang sudah diisi</li>
                    </ol>
                </div>
            </div>

            <div class="text-center mb-4">
                <a href="create_template.php" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download Template Excel
                </a>
            </div>

            <form action="import_siswa.php" method="post" enctype="multipart/form-data">
                <div class="file-upload">
                    <div class="mb-3">
                        <label for="file_excel" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" id="file_excel" name="file_excel" accept=".xlsx,.xls" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-import"></i> Import Data
                    </button>
                    <a href="siswa.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
</body>
</html>

<?php
if (isset($_FILES['file_excel']['name'])) {
    $fileName = $_FILES['file_excel']['name'];
    $fileTmp  = $_FILES['file_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($fileTmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $imported = 0;
        $skipped = 0;

        // Lewati baris pertama (header excel)
        for ($i = 1; $i < count($rows); $i++) {
            $nama  = mysqli_real_escape_string($conn, $rows[$i][0]); // kolom A
            $kelas = mysqli_real_escape_string($conn, $rows[$i][1]); // kolom B
            $nis   = mysqli_real_escape_string($conn, $rows[$i][2]); // kolom C

            if (!empty($nama) && !empty($kelas) && !empty($nis)) {
                // Cek kalau nis sudah ada, jangan duplikat
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

    header("Location: import_siswa.php");
    exit;
}
?>
