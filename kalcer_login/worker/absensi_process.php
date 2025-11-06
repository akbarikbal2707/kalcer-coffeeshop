<?php

session_start();
date_default_timezone_set('Asia/Jakarta');
include '../login/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

$user_id = $_SESSION['user_id'];
$date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    $row = null;
    $check = $conn->prepare("SELECT * FROM attendance WHERE user_id=? AND tanggal=? LIMIT 1");
    $check->bind_param("is", $user_id, $date);
    $check->execute();
    $result = $check->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }

    // ==========================
    // 1️⃣ ACTION: STATUS
    // ==========================
    if ($action === 'status') {
        $status = $_POST['status'] ?? '';

        if ($row) {
            // sudah ada data hari ini → update status
            $update = $conn->prepare("UPDATE attendance SET status=? WHERE id=?");
            $update->bind_param("si", $status, $row['id']);
            $update->execute();
        } else {
            // belum ada → insert baru
            $insert = $conn->prepare("INSERT INTO attendance (user_id, status, tanggal) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $user_id, $status, $date);
            $insert->execute();
        }

        echo "success";
        exit;
    }

    // ==========================
    // 2️⃣ ACTION: CHECK-IN
    // ==========================
    if ($action === 'checkin') {
        $time = date('Y-m-d H:i:s');

        // Cek apakah user udah punya data absensi hari ini
        if ($row) {
            // Kalau udah ada waktu check_in valid → tolak
            if (!empty($row['check_in']) && $row['check_in'] !== '0000-00-00 00:00:00') {
                echo "already_checked_in";
                exit;
            }

            // Update waktu check-in
            $update = $conn->prepare("UPDATE attendance SET check_in=? WHERE id=?");
            $update->bind_param("si", $time, $row['id']);
            $update->execute();
            echo "success";
            exit;
        } else {
            // Kalau belum ada record, insert baru
            $insert = $conn->prepare("INSERT INTO attendance (user_id, status, check_in, tanggal) VALUES (?, 'hadir', ?, ?)");
            $insert->bind_param("iss", $user_id, $time, $date);
            $insert->execute();
            echo "success";
            exit;
        }
    }

    // ==========================
    // 3️⃣ ACTION: CHECK-OUT
    // ==========================
    if ($action === 'checkout') {
        $time = date('Y-m-d H:i:s');

        // Pastikan ambil ulang data fresh (hindari cache)
        $check = $conn->prepare("SELECT * FROM attendance WHERE user_id=? AND tanggal=? LIMIT 1");
        $check->bind_param("is", $user_id, $date);
        $check->execute();
        $result = $check->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            echo "no_checkin";
            exit;
        }

        if (!empty($row['check_out']) && $row['check_out'] !== '0000-00-00 00:00:00') {
            echo "already_checked_out";
            exit;
        }

        $update = $conn->prepare("UPDATE attendance SET check_out=? WHERE id=?");
        $update->bind_param("si", $time, $row['id']);
        if ($update->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        exit;
    }
}
