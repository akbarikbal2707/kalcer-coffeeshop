<?php
include 'auth_worker.php'; // jika path sama folder worker
include '../login/db_connect.php'; // sekali saja
// sekarang $_SESSION['user_id'] pasti ada
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<?php
// worker/auth_worker.php
session_start();

// Pastikan user login dan role worker
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: ../login/index.php");
    exit;
}
?>
