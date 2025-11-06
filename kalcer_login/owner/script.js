// Ambil canvas chart
const ctx = document.getElementById("incomeChart").getContext("2d");

// Label hari dalam seminggu
const days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"];

// Data pemasukan (simulasi dalam rupiah, bisa diganti dari database nanti)
const incomeData = [750000, 1200000, 950000, 1100000, 1750000, 2000000, 1300000];

// Buat grafik pakai Chart.js
new Chart(ctx, {
  type: "line",
  data: {
    labels: days,
    datasets: [
      {
        label: "Pemasukan (Rp)",
        data: incomeData,
        borderColor: "#8B4513",
        backgroundColor: "rgba(139,69,19,0.1)",
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointBackgroundColor: "#6f4e37",
        pointHoverRadius: 7,
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        display: true,
        labels: { color: "#333" },
      },
      title: {
        display: true,
        text: "Statistik Pemasukan Minggu Ini",
        color: "#333",
        font: {
          size: 16,
          weight: "bold",
        },
      },
    },
    scales: {
      x: {
        title: {
          display: true,
          text: "Hari",
          color: "#333",
        },
        ticks: {
          color: "#333",
        },
      },
      y: {
        title: {
          display: true,
          text: "Nominal (Rp)",
          color: "#333",
        },
        ticks: {
          color: "#333",
          // Format angka biar keliatan kayak uang
          callback: function (value) {
            return "Rp " + value.toLocaleString("id-ID");
          },
          stepSize: 250000, // jarak antar nilai di sumbu Y
        },
        suggestedMin: 0,
        suggestedMax: 2000000, // batas maksimal sumbu Y
      },
    },
  },
});

document.addEventListener("DOMContentLoaded", () => {
  const logoutBtn = document.getElementById("logoutBtn");
  const logoutPopup = document.getElementById("logoutPopup");
  const confirmLogout = document.getElementById("confirmLogout");
  const cancelLogout = document.getElementById("cancelLogout");

  if (!logoutBtn || !logoutPopup) {
    console.error("Elemen popup logout tidak ditemukan!");
    return;
  }

  logoutBtn.addEventListener("click", () => {
    logoutPopup.style.display = "flex";
  });

  cancelLogout.addEventListener("click", () => {
    logoutPopup.style.display = "none";
  });

  confirmLogout.addEventListener("click", () => {
    console.log("Logout diklik!");
    window.location.href = "../login/logout.php";
  });
});
