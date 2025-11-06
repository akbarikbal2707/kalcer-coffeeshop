<?php

include 'db_connect.php'; // pastikan nama file koneksi lo sama ya

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah username sudah ada
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>
            alert('Username sudah digunakan!');
            window.location.href = 'index.php';
        </script>";
        exit;
    }

    // Hash password biar aman
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Role default (misalnya worker)
    $role = 'worker';

    // Masukkan data ke database
    $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', 'worker')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>
            alert('Registrasi berhasil! Silakan login.');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Registrasi gagal!');
            window.location.href = 'index.php';
        </script>";
    }
}
