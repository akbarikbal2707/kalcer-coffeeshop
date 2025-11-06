<?php include '../auth_owner.php'; ?>
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../../login/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KalcerCoffee-Owner</title>
    <link rel="stylesheet" href="kehadiran.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet" />
  </head>
  <body>
    <div class="head">
      <h1>Dashboard</h1>
      <div class="content">
        <h1>Kalcer<span>Coffee.</span></h1>
      </div>
      <div class="log">
        <button id="logoutBtn" class="logout-header">
          <p>Logout</p>
          <i class="fa-solid fa-power-off"></i>
        </button>
      </div>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar">
      <ul class="sidebar-links">
        <a href="../owner.php"><i class="fa-solid fa-chart-simple"></i> Dashboard Utama</a>
        <a href="#"><i class="fa-solid fa-semibold fa-clock"></i>Kehadiran Pegawai</a>
      </ul>
    </nav>

    <!-- Isi Content -->
    <main class="main-content">
      <div class="title">
        <h1>Kehadiran Pegawai:</h1>
      </div>

      <div class="content container">
        <!-- Pilihan Tanggal -->
        <div class="date-selector">
          <button id="prevDate">←</button>
          <input type="date" id="attendanceDate"/>
          <button id="nextDate">→</button>
        </div>

        <!-- Tabel Data Pegawai -->
        <div class="table-container">
          <h3 id="attendanceTitle">Daftar Kehadiran Pegawai Kalcer Coffee</h3>
          <table class="pegawai-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Status</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
              </tr>
            </thead>
            <tbody id="attendanceBody">
              <!-- Data akan diisi otomatis lewat JS -->
            </tbody>
          </table>
        </div>

        <!-- Ringkasan -->
        <div class="summary">
          <div class="card">
            <h3>Total Pegawai</h3>
            <p id="totalPegawai">5 Orang</p>
          </div>
          <div class="card">
            <h3>Hadir Hari Ini</h3>
            <p id="hadirCount">6 Orang</p>
          </div>
          <div class="card">
            <h3>Izin / Cuti</h3>
            <p id="izinCount">2 Orang</p>
          </div>
        </div>
      </div>
    </main>
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
