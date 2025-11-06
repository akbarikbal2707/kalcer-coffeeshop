<?php
session_start();
include '../login/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header("Location: ../login/index.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Cek apakah user sudah mengisi absensi hari ini
$query = "SELECT status, check_in, check_out FROM attendance WHERE user_id = '$user_id' AND tanggal = '$today'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $data_absensi = mysqli_fetch_assoc($result);
    $status_hari_ini = $data_absensi['status'];
    $check_in = $data_absensi['check_in'];
    $check_out = $data_absensi['check_out'];
} else {
    $status_hari_ini = 'Belum Absen';
    $check_in = '-';
    $check_out = '-';
}

// Pendapatan
$result = $conn->query("SELECT * FROM orders WHERE user_id = $user_id AND DATE(created_at) = '$today' ORDER BY created_at DESC");

// ✅ Pengeluaran
$expenseResult = $conn->query("SELECT * FROM expenses WHERE user_id = $user_id AND DATE(created_at) = '$today' ORDER BY created_at DESC");
$totalExpenseResult = $conn->query("SELECT SUM(nominal) AS total FROM expenses WHERE user_id = $user_id AND DATE(created_at) = '$today'");
$totalExpense = $totalExpenseResult->fetch_assoc()['total'] ?? 0;

$today = date('Y-m-d');
$expenseResult = $conn->query("SELECT * FROM expenses WHERE user_id = $user_id AND DATE(created_at) = '$today' ORDER BY created_at DESC");
$totalExpenseResult = $conn->query("SELECT SUM(nominal) AS total FROM expenses WHERE user_id = $user_id AND DATE(created_at) = '$today'");
$totalExpense = $totalExpenseResult->fetch_assoc()['total'] ?? 0;

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Karyawan</title>

    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="navbar" style="padding: 20px; background-color: #906241; color: white">
      <h2>Karyawan</h2>
      <p id="tanggalHariIni">Tanggal Hari Ini: Loading...</p>
      <div class="log">
        <button id="logoutBtn" class="logout-header">
          <p>Logout</p>
          <i class="fa-solid fa-power-off"></i>
        </button>
      </div>
    </div>

    <div class="dashboard-container">
      <div class="widget" style="max-width: 300px">
        <div class="absen">
        <p>Status: 
          <strong id="absensiStatus" style="color: orange">
            <?= ucfirst($status_hari_ini); ?>
           </strong>
        </p>
        <p>Check-In:</p>
        <p class="faruq">
          <span id="checkInTime">
            <?= ($check_in && $check_in != '0000-00-00 00:00:00' && $check_in != '-') ? date('H:i:s', strtotime($check_in)) : '-' ?>
          </span>
        </p>
        <p>Check-Out:</p>
        <p class="faruq">
          <span id="checkOutTime">
            <?= ($check_out && $check_out != '0000-00-00 00:00:00' && $check_out != '-') ? date('H:i:s', strtotime($check_out)) : '-' ?>
          </span>
        </p>
        </div>

    <div style="margin-top:5px;   justify-content: center;  display: flex;">
    <button id="btnHadir" class="btn btn-green">Hadir</button>
    <button id="btnIzin" class="btn btn-yellow">Izin</button>
    <button id="btnCuti" class="btn btn-orange">Cuti</button>
    </div>

    <div style="margin-top:5px; justify-content: center; display: flex;">
    <button id="btnAbsenMasuk" class="btn btn-blue" disabled>Absen Masuk</button>
    <button id="btnAbsenPulang" class="btn btn-red" disabled>Absen Pulang</button>
    </div>

    <div class="absen-actions" style="margin-top: 5px;  justify-content: center; display: flex;">
    <button id="btnReset" class="btn btn-gray" disabled>Reset</button>
   <button id="btnConfirm" class="btn btn-green" disabled>Confirm</button>
    </div>


</div>

<div class="widget" style="flex: 2">
  <h3>Pencatatan Pendapatan (Order)</h3>
  <!-- ✅ Form dikirim ke worker_process.php -->
<form id="orderForm" action="worker_process.php" method="POST">
  <input
    type="text"
    id="orderDescription"
    name="orderDescription" 
    placeholder="Cth: 1 Latte, 2 Croissant"
    style="width: 100%; padding: 5px"
    required
  />

  <input
    type="number"
    id="orderNominal"
    name="orderNominal"
    placeholder="Total Uang Diterima"
    style="width: 100%; padding: 5px"
    required
    min="1"
  />

  <button type="submit" class="btn btn-blue" style="margin: 10px">
    Tambah Order
  </button>
</form>
  <!-- ✅ TOTAL PENDAPATAN -->
  <?php
$today = date('Y-m-d');
$totalIncomeResult = $conn->query("SELECT SUM(nominal) AS total FROM orders WHERE user_id = $user_id AND DATE(created_at) = '$today'");
$totalIncome = $totalIncomeResult->fetch_assoc()['total'] ?? 0;
?>
<p>Total Pendapatan Hari Ini: <span id="totalIncomeDisplay">Rp <?= number_format($totalIncome, 0, ',', '.') ?></span></p>


  <h4>Histori Order Hari Ini</h4>
  <table class="history-table">
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Deskripsi</th>
        <th>Nominal</th>
      </tr>
    </thead>
    <tbody id="orderHistoryBody">
  <?php
  while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
      echo "<td>" . htmlspecialchars($row['description']) . "</td>";
      echo "<td>Rp " . number_format($row['nominal'], 0, ',', '.') . "</td>";
      echo "</tr>";
  }
?>
</tbody>

  </table>
    </div>
<div class="widget widget-expense" style="padding: 20px">
  <h3>Pencatatan Pengeluaran</h3>

  <form id="expenseForm" action="expense_process.php" method="POST">
    <div class="form-row">
      <div class="form-group" style="flex: 1">
        <label>Nominal:</label>
        <input type="text" id="expenseNominal" placeholder="Jumlah Uang Keluar" style="width: 100%; padding: 5px" required />
      </div>
      <div class="form-group" style="flex: 2">
        <label>Notes/Deskripsi:</label>
        <input type="text" id="expenseDescription" placeholder="Uang dipakai untuk beli stock susu" style="width: 100%; padding: 5px" required />
      </div>
      <button type="submit" class="btn btn-red" style="align-self: flex-end; margin: 10px">Input Pengeluaran</button>
    </div>
  </form>

  <!-- ✅ TOTAL PENGELUARAN -->
  <div class="total-display">
    Total Pengeluaran Hari Ini:
    <span class="total-expense-value" id="totalExpenseDisplay">
      Rp <?= number_format($totalExpense, 0, ',', '.') ?>
    </span>
  </div>

  <!-- ✅ HISTORI PENGELUARAN -->
  <h4>Histori Pengeluaran Hari Ini</h4>
  <table class="history-table">
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Nominal</th>
        <th>Deskripsi</th>
      </tr>
    </thead>
    <tbody id="expenseHistoryBody">
      <?php while ($row = $expenseResult->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['description']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
      </table>
    </div>

    <!-- Pop Up Konfirmasi -->
    <div id="logoutPopup" class="popup-overlay">
      <div class="popup-content">
        <h3>Konfirmasi Logout</h3>
        <p>Apakah kamu yakin ingin logout dari Dashboard Kalcer Coffee?</p>
        <div class="popup-actions">
          <button id="confirmLogout" class="btn-confirm">Ya, Logout</button>
          <button id="cancelLogout" class="btn-cancel">Batal</button>
        </div>
      </div>
    </div>

    <script src="script.js"></script>
  </body>
</html>
