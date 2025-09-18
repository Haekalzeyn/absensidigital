<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $insert = mysqli_query($conn, "INSERT INTO guru (username, password) VALUES ('$username','$password')");
    if ($insert) {
        header("Location: guru.php");
        exit;
    } else {
        echo "<script>alert('Gagal tambah guru');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Guru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="card shadow">
      <div class="card-header">
        <h4>➕ Tambah Guru</h4>
      </div>
      <div class="card-body">
        <form method="post">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
          <a href="guru.php" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
