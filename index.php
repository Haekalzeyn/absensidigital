<?php
session_start();
require "koneksi.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];


    $cekAdmin = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' LIMIT 1");
    if ($cekAdmin && mysqli_num_rows($cekAdmin) > 0) {
        $data = mysqli_fetch_assoc($cekAdmin);
        if (password_verify($password, $data['password'])) {
            $_SESSION['admin'] = $data['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Password admin salah!');</script>";
        }
    }


    $cekGuru = mysqli_query($conn, "SELECT * FROM guru WHERE username='$username' LIMIT 1");
    if ($cekGuru && mysqli_num_rows($cekGuru) > 0) {
        $data = mysqli_fetch_assoc($cekGuru);
        if (password_verify($password, $data['password'])) {
            $_SESSION['guru'] = $data['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Password guru salah!');</script>";
        }
    }

    echo "<script>alert('Login gagal! Username tidak ditemukan');</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - Absensi Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/index.css">
 
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <!-- Logo sekolah, bisa ganti dengan logo asli -->
      <img src="logo_sekolah.png" alt="Logo Sekolah">
      <h4>Sistem Absensi Digital</h4>
      <p>SMK Taruna Bangsa</p>
    </div>
    <div class="login-body">
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Nama Panggilan</label>
          <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100 btn-login">Login</button>
      </form>
      <hr>
     <p class="text-center register-link">
  Belum punya akun? 
</p>
<a href="https://wa.me/6281399694203" target="_blank">
    <img src="https://img.icons8.com/ios-filled/20/25D366/whatsapp.png" alt="WhatsApp"> Hubungi Admin
  </a>
    </div>
  </div>
</body>
</html>
