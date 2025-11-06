document.addEventListener("DOMContentLoaded", function () {
  // === BAGIAN 1: TAMPIL DATA ABSENSI ===
  const attendanceDate = document.getElementById("attendanceDate");
  const today = new Date();
  const todayStr = today.toISOString().split("T")[0];
  attendanceDate.value = todayStr; // ⬅️ otomatis set ke tanggal hari ini
  const attendanceBody = document.getElementById("attendanceBody");
  const totalPegawai = document.getElementById("totalPegawai");
  const hadirCount = document.getElementById("hadirCount");
  const izinCount = document.getElementById("izinCount");

  // lalu panggil loadAttendance setelah value diset
  loadAttendance(todayStr);

  async function loadAttendance(date) {
    const response = await fetch(`get_kehadiran.php?date=${date}`);
    const data = await response.json();

    // Isi tabel
    attendanceBody.innerHTML = "";
    if (data.attendance.length === 0) {
      attendanceBody.innerHTML = `<tr><td colspan="5">Belum ada data absensi pada tanggal ini.</td></tr>`;
    } else {
      data.attendance.forEach((item, index) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${item.nama_pegawai}</td>
          <td>${item.status ?? "-"}</td>
          <td>${item.check_in ?? "-"}</td>
          <td>${item.check_out ?? "-"}</td>
        `;
        attendanceBody.appendChild(tr);
      });
    }

    // Ringkasan
    totalPegawai.textContent = `${data.summary.total} Orang`;
    hadirCount.textContent = `${data.summary.hadir} Orang`;
    izinCount.textContent = `${data.summary.izin} Orang`;
  }

  // Load pertama kali
  loadAttendance(attendanceDate.value);

  // Ganti tanggal → reload data
  attendanceDate.addEventListener("change", () => {
    loadAttendance(attendanceDate.value);
  });

  // Tombol next & prev tanggal
  document.getElementById("prevDate").addEventListener("click", () => {
    const d = new Date(attendanceDate.value);
    d.setDate(d.getDate() - 1);
    attendanceDate.value = d.toISOString().split("T")[0];
    loadAttendance(attendanceDate.value);
  });

  document.getElementById("nextDate").addEventListener("click", () => {
    const d = new Date(attendanceDate.value);
    d.setDate(d.getDate() + 1);
    attendanceDate.value = d.toISOString().split("T")[0];
    loadAttendance(attendanceDate.value);
  });

  // === BAGIAN 2: POPUP LOGOUT ===
  const logoutBtn = document.getElementById("logoutBtn");
  const logoutPopup = document.getElementById("logoutPopup");
  const confirmLogout = document.getElementById("confirmLogout");
  const cancelLogout = document.getElementById("cancelLogout");

  if (logoutBtn && logoutPopup) {
    logoutBtn.addEventListener("click", () => {
      logoutPopup.style.display = "flex";
    });

    cancelLogout.addEventListener("click", () => {
      logoutPopup.style.display = "none";
    });

    confirmLogout.addEventListener("click", () => {
      console.log("Logout diklik!");
      window.location.href = "../../login/logout.php";
    });
  } else {
    console.error("Elemen popup logout tidak ditemukan!");
  }
});
