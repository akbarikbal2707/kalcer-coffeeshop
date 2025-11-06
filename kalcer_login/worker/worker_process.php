<?php

session_start();
include '../login/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak: Anda belum login.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $description = trim($_POST['orderDescription']);
    $nominal = intval($_POST['orderNominal']);

    if ($description !== '' && $nominal > 0) {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, description, nominal, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("isi", $user_id, $description, $nominal);

        if ($stmt->execute()) {
            // ✅ kirim response sederhana biar cocok sama fetch()
            echo "success";
        } else {
            // ❌ kirim pesan error biasa
            echo "error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "invalid input";
    }

    $conn->close();
} else {
    echo "invalid request";
}
