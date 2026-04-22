<?php

$host = "127.0.0.1"; // lebih aman daripada localhost
$user = "root";
$pass = "";
$dbname = "kalcer_db";

// Buat koneksi
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset (biar aman buat text, emoji, dll)
mysqli_set_charset($conn, "utf8mb4");

?>