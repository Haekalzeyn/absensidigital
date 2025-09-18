<?php
require "koneksi.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

   
    mysqli_query($conn, "DELETE FROM absensi WHERE murid_id=$id");

    
    mysqli_query($conn, "DELETE FROM murid WHERE id=$id");

    header("Location: siswa.php");
    exit;
}
?>
