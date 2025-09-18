<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];


if ($id == 1) {
    echo "<script>alert('Admin utama tidak bisa dihapus!');window.location='guru.php';</script>";
    exit;
}

mysqli_query($conn, "DELETE FROM guru WHERE id=$id");
header("Location: guru.php");
exit;
