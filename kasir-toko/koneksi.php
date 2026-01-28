<?php
$host = "localhost";
$user = "root";     // User default Laragon
$pass = "";         // Password default Laragon (kosong)
$db   = "db_kasir"; // Nama database yang tadi dibuat

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>