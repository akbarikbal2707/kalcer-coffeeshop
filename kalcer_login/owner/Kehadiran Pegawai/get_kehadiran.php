<?php

include '../../login/db_connect.php';

// Ambil tanggal dari request, default ke hari ini
$date = $_GET['date'] ?? date('Y-m-d');

// 🔹 Ambil data kehadiran + nama pegawai
$query = "
  SELECT 
    a.*, 
    u.username AS nama_pegawai
  FROM attendance a
  JOIN users u ON a.user_id = u.id_user
  WHERE DATE(a.tanggal) = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $date);
$stmt->execute();
$result = $stmt->get_result();

$attendanceData = [];
while ($row = $result->fetch_assoc()) {
    $attendanceData[] = $row;
}

// 🔹 Total pegawai (hanya worker, owner tidak dihitung)
$totalPegawai = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='worker'")->fetch_assoc()['total'];

// 🔹 Hadir hari ini (status = 'hadir')
$hadirHariIni = $conn->prepare("
  SELECT COUNT(DISTINCT user_id) AS hadir 
  FROM attendance 
  WHERE DATE(tanggal)=? AND status='hadir'
");
$hadirHariIni->bind_param('s', $date);
$hadirHariIni->execute();
$hadirHariIniResult = $hadirHariIni->get_result()->fetch_assoc()['hadir'];

// 🔹 Izin / Cuti (status = 'izin' atau 'cuti')
$izinCuti = $conn->prepare("
  SELECT COUNT(DISTINCT user_id) AS izin 
  FROM attendance 
  WHERE DATE(tanggal)=? AND (status='izin' OR status='cuti')
");
$izinCuti->bind_param('s', $date);
$izinCuti->execute();
$izinCutiResult = $izinCuti->get_result()->fetch_assoc()['izin'];

// 🔹 Kirim data JSON
header('Content-Type: application/json');
echo json_encode([
  'attendance' => $attendanceData,
  'summary' => [
    'total' => $totalPegawai,
    'hadir' => $hadirHariIniResult,
    'izin' => $izinCutiResult
  ]
]);
