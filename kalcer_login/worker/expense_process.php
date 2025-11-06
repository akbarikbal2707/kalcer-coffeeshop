<?php

session_start();
include '../login/db_connect.php';
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['expenseDescription']);
    $nominal = intval($_POST['expenseNominal']);

    if ($description !== '' && $nominal > 0) {
        $stmt = $conn->prepare("INSERT INTO expenses (user_id, description, nominal, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("isi", $user_id, $description, $nominal);
        echo $stmt->execute() ? "success" : $stmt->error;
    } else {
        echo "invalid";
    }
}
