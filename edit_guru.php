<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM guru WHERE id=$id"));

if (isset($_POST['update'])) {
    $username = $_POST['username'];
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = mysqli_query($conn, "UPDATE guru SET username='$username', password='$password' WHERE id=$id");
    } else {
        $update = mysqli_query($conn, "UPDATE guru SET username='$username' WHERE id=$id");
    }
    if ($update) {
        header("Location: guru.php");
        exit;
    } else {
        echo "<script>alert('Gagal update guru');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Guru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="card shadow">
      <div class="card-header">
        <h4>✏ Edit Guru</h4>
      </div>
      <div class="card-body">
        <form method="post">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?= $guru['username'] ?>" required>
          </div>
          <div class="mb-3">
            <label>Password Baru (opsional)</label>
            <input type="password" name="password" class="form-control">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti password</small>
          </div>
          <button type="submit" name="update" class="btn btn-warning">Update</button>
          <a href="guru.php" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
