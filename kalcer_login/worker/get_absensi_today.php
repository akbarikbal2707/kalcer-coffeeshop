<?php

session_start();
include '../login/db_connect.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$date = date('Y-m-d');

$query = $conn->prepare("SELECT * FROM attendance WHERE user_id=? AND tanggal=?");
$query->bind_param("is", $user_id, $date);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

echo json_encode($data ?: []);
