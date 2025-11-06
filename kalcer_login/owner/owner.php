<?php
include 'auth_owner.php';
include_once '../login/db_connect.php';

// Ambil tanggal hari ini
$today = date('Y-m-d');
// Query pemasukan dari tabel orders
$result_pemasukan = mysqli_query($conn, "SELECT SUM(nominal) AS total_pemasukan FROM orders WHERE DATE(created_at) = CURDATE()");
$data_pemasukan = mysqli_fetch_assoc($result_pemasukan);
$total_pemasukan = $data_pemasukan['total_pemasukan'] ?? 0;

// Query pengeluaran dari tabel expenses
$result_pengeluaran = mysqli_query($conn, "SELECT SUM(nominal) AS total_pengeluaran FROM expenses WHERE DATE(created_at) = CURDATE()");
$data_pengeluaran = mysqli_fetch_assoc($result_pengeluaran);
$total_pengeluaran = $data_pengeluaran['total_pengeluaran'] ?? 0;

// Query jumlah pegawai dengan role 'worker'
$result_pegawai = mysqli_query($conn, "SELECT COUNT(*) AS total_pegawai FROM users WHERE role = 'worker'");
$data_pegawai = mysqli_fetch_assoc($result_pegawai);
$total_pegawai = $data_pegawai['total_pegawai'] ?? 0;

// Query kehadiran hari ini
$query_kehadiran = "
    SELECT 
        SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) AS total_hadir,
        SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) AS total_izin,
        SUM(CASE WHEN status = 'cuti' THEN 1 ELSE 0 END) AS total_cuti,
        SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) AS total_alpha
    FROM attendance 
    WHERE tanggal = CURDATE()
";
$result_kehadiran = mysqli_query($conn, $query_kehadiran);
$data_kehadiran = mysqli_fetch_assoc($result_kehadiran);

$total_hadir = $data_kehadiran['total_hadir'] ?? 0;
$total_izin = $data_kehadiran['total_izin'] ?? 0;
$total_cuti = $data_kehadiran['total_cuti'] ?? 0;
$total_alpha = $data_kehadiran['total_alpha'] ?? 0;



// Format ke Rupiah
function formatRupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KalcerCoffee-Owner</title>
    <link rel="stylesheet" href="style.css" />
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
        <a href="#"><i class="fa-solid fa-chart-simple"></i> Dashboard Utama</a>
        <a href="Kehadiran Pegawai/kehadiran.php"><i class="fa-solid fa-semibold fa-clock"></i>Kehadiran Pegawai</a>

      </ul>
    </nav>

    <!-- Isi Content -->
    <main class="main-content">
      <div class="title">
        <h1>Ringkasan Cepat</h1>
      </div>
      <div class="chart-container">
        <canvas id="incomeChart"></canvas>
      </div>
      <div class="content">
        <section class="container">
          <div class="card">
            <h3>Pemasukan Hari Ini</h3>
            <p><?= formatRupiah($total_pemasukan) ?></p>
          </div>
          <div class="card">
            <h3>Pengeluaran Hari Ini</h3>
            <p><?= formatRupiah($total_pengeluaran) ?></p>
          </div>
          <div class="card">
            <h3>Jumlah Pegawai</h3>
            <p><?= $total_pegawai ?> Orang</p>
          </div>
          <div class="card">
            <h3>Kehadiran Hari Ini</h3>
            <p>
              <?= $total_hadir ?> Orang Hadir<br>
              <?= $total_izin ?> Orang Izin<br>
              <?= $total_cuti ?> Orang Cuti<br>
            </p>
</div>


        </section>
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
