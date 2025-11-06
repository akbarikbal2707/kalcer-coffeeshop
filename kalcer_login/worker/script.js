document.addEventListener("DOMContentLoaded", function () {
  // =========================================================
  // 1️⃣ TANGGAL HARI INI
  // =========================================================
  const tanggalDisplay = document.getElementById("tanggalHariIni");
  const now = new Date();
  tanggalDisplay.textContent = "Tanggal Hari Ini: " + now.toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" });

  // =========================================================
  // 2️⃣ PENCATATAN PENDAPATAN (ORDER)
  // =========================================================
  const orderForm = document.getElementById("orderForm");
  const orderDescriptionInput = document.getElementById("orderDescription");
  const orderNominalInput = document.getElementById("orderNominal");
  const orderHistoryBody = document.getElementById("orderHistoryBody");
  const totalIncomeDisplay = document.getElementById("totalIncomeDisplay");

  let totalIncome = 0;

  function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    }).format(number);
  }

  function updateTotalPrice() {
    totalIncomeDisplay.textContent = formatRupiah(totalIncome);
  }

  // Event untuk form pendapatan
  orderForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const description = orderDescriptionInput.value.trim();
    const nominal = parseInt(orderNominalInput.value.replaceAll(".", ""));

    if (!description || isNaN(nominal) || nominal <= 0) {
      alert("Mohon isi Deskripsi dan Nominal dengan benar.");
      return;
    }

    fetch("worker_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `orderDescription=${encodeURIComponent(description)}&` + `orderNominal=${encodeURIComponent(nominal)}`,
    })
      .then((res) => res.text())
      .then((data) => {
        console.log("Response dari PHP:", data);
        if (data.trim() === "success") {
          alert("✅ Order berhasil ditambahkan ke database!");
        } else {
          alert("❌ Gagal menambahkan order: " + data);
        }

        const newRow = orderHistoryBody.insertRow();
        newRow.insertCell(0).textContent = new Date().toLocaleTimeString("id-ID", {
          hour: "2-digit",
          minute: "2-digit",
        });
        newRow.insertCell(1).textContent = description;
        newRow.insertCell(2).textContent = formatRupiah(nominal);

        totalIncome += nominal;
        updateTotalPrice();

        orderDescriptionInput.value = "";
        orderNominalInput.value = "";
      })
      .catch((err) => {
        console.error("Error:", err);
        alert("❌ Gagal terhubung ke server.");
      });
  });

  // =========================================================
  // 3️⃣ PENCATATAN PENGELUARAN (EXPENSE)
  // =========================================================
  const expenseForm = document.getElementById("expenseForm");
  const expenseDescriptionInput = document.getElementById("expenseDescription");
  const expenseNominalInput = document.getElementById("expenseNominal");
  const expenseHistoryBody = document.getElementById("expenseHistoryBody");
  const totalExpenseDisplay = document.getElementById("totalExpenseDisplay");

  let totalExpense = 0;

  function updateTotalExpense() {
    totalExpenseDisplay.textContent = formatRupiah(totalExpense);
  }

  expenseForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const description = expenseDescriptionInput.value.trim();
    const nominal = parseInt(expenseNominalInput.value.replaceAll(".", ""));

    if (!description || isNaN(nominal) || nominal <= 0) {
      alert("Mohon isi Deskripsi dan Nominal pengeluaran dengan benar.");
      return;
    }

    // Kirim ke PHP
    fetch("expense_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `expenseDescription=${encodeURIComponent(description)}&` + `expenseNominal=${encodeURIComponent(nominal)}`,
    })
      .then((res) => res.text())
      .then((data) => {
        console.log("Response pengeluaran:", data);
        if (data.trim() === "success") {
          alert("✅ Pengeluaran berhasil disimpan!");
        } else {
          alert("❌ Gagal menyimpan pengeluaran: " + data);
        }

        const newRow = expenseHistoryBody.insertRow();
        newRow.insertCell(0).textContent = new Date().toLocaleTimeString("id-ID", {
          hour: "2-digit",
          minute: "2-digit",
        });
        newRow.insertCell(1).textContent = formatRupiah(nominal);
        newRow.insertCell(2).textContent = description;

        totalExpense += nominal;
        updateTotalExpense();

        expenseDescriptionInput.value = "";
        expenseNominalInput.value = "";
      })
      .catch((err) => {
        console.error("Error:", err);
        alert("❌ Gagal terhubung ke server.");
      });
  });

  // =========================================================
  // 4️⃣ ABSENSI
  // =========================================================
  const btnHadir = document.getElementById("btnHadir");
  const btnIzin = document.getElementById("btnIzin");
  const btnCuti = document.getElementById("btnCuti");

  function kirimAbsensi(action, data = {}) {
    return fetch("absensi_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ action, ...data }),
    }).then((res) => res.text());
  }

  btnHadir.addEventListener("click", () => {
    kirimAbsensi("status", { status: "Hadir" }).then(() => {
      absensiStatus.textContent = "Hadir";
      absensiStatus.style.color = "green";
      btnAbsenMasuk.disabled = false;
      btnIzin.disabled = true;
      btnCuti.disabled = true;
    });
  });

  btnIzin.addEventListener("click", () => {
    kirimAbsensi("status", { status: "Izin" }).then(() => {
      absensiStatus.textContent = "Izin";
      absensiStatus.style.color = "orange";
      btnHadir.disabled = true;
      btnCuti.disabled = true;
    });
  });

  btnCuti.addEventListener("click", () => {
    kirimAbsensi("status", { status: "Cuti" }).then(() => {
      absensiStatus.textContent = "Cuti";
      absensiStatus.style.color = "red";
      btnHadir.disabled = true;
      btnIzin.disabled = true;
    });
  });

  const btnAbsenMasuk = document.getElementById("btnAbsenMasuk");
  const btnAbsenPulang = document.getElementById("btnAbsenPulang");
  const absensiStatus = document.getElementById("absensiStatus");
  const checkInTimeDisplay = document.getElementById("checkInTime");
  const checkOutTimeDisplay = document.getElementById("checkOutTime");
  let hasCheckedIn = false;

  function formatDateTime(date) {
    const time = date.toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" });
    const datePart = date.toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" });
    return `${time}, ${datePart}`;
  }

  btnAbsenMasuk.addEventListener("click", function () {
    if (hasCheckedIn) return alert("Anda sudah Absen Masuk hari ini.");

    fetch("absensi_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ action: "checkin" }),
    })
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "success") {
          const now = new Date();
          const formattedTime = formatDateTime(now);
          absensiStatus.textContent = "Masuk";
          absensiStatus.style.color = "green";
          checkInTimeDisplay.textContent = formattedTime;
          hasCheckedIn = true;
          btnAbsenMasuk.disabled = true;
          btnAbsenPulang.disabled = false;
        } else {
          alert("❌ Gagal menyimpan waktu absen masuk: " + data);
        }
      })
      .catch((err) => alert("❌ Gagal terhubung ke server: " + err));
  });

  btnAbsenPulang.addEventListener("click", () => {
    fetch("absensi_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ action: "checkout" }),
    })
      .then((res) => res.text())
      .then((data) => {
        data = data.trim();
        if (data === "success") {
          const now = new Date();
          const formattedTime = formatDateTime(now);
          absensiStatus.textContent = "Pulang";
          absensiStatus.style.color = "red";
          checkOutTimeDisplay.textContent = formattedTime;
          btnAbsenPulang.disabled = true;
        } else if (data === "already_checked_out") {
          alert("Anda sudah Absen Pulang hari ini.");
        } else if (data === "no_checkin") {
          alert("Anda belum Absen Masuk hari ini.");
        } else {
          alert("❌ Gagal menyimpan waktu absen pulang: " + data);
        }
      })
      .catch((err) => alert("❌ Gagal terhubung ke server: " + err));
  });

  // =========================================================
  // 🔄 SISTEM RESET & CONFIRM UNTUK ABSENSI
  // =========================================================
  const btnReset = document.getElementById("btnReset");
  const btnConfirm = document.getElementById("btnConfirm");
  let selectedStatus = null;

  // fungsi pilih status (override klik lama)
  function pilihStatus(status, color) {
    selectedStatus = status;
    absensiStatus.textContent = status;
    absensiStatus.style.color = color;

    // aktifkan tombol confirm & reset
    btnConfirm.disabled = false;
    btnReset.disabled = false;

    // nonaktifkan tombol status lain
    [btnHadir, btnIzin, btnCuti].forEach((b) => (b.disabled = true));

    // kalau statusnya Hadir, aktifkan Absen Masuk
    if (status === "Hadir") {
      btnAbsenMasuk.disabled = false;
    } else {
      btnAbsenMasuk.disabled = true;
      btnAbsenPulang.disabled = true;
    }
  }

  // override event lama
  btnHadir.onclick = () => pilihStatus("Hadir", "green");
  btnIzin.onclick = () => pilihStatus("Izin", "orange");
  btnCuti.onclick = () => pilihStatus("Cuti", "red");

  // tombol reset
  btnReset.addEventListener("click", () => {
    selectedStatus = null;
    absensiStatus.textContent = "Belum Absen";
    absensiStatus.style.color = "black";
    btnAbsenMasuk.disabled = true;
    btnAbsenPulang.disabled = true;

    [btnHadir, btnIzin, btnCuti].forEach((b) => (b.disabled = false));
    btnConfirm.disabled = true;
    btnReset.disabled = true;
  });

  // tombol confirm
  btnConfirm.addEventListener("click", () => {
    if (!selectedStatus) return alert("Pilih status dulu!");

    fetch("absensi_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ action: "status", status: selectedStatus }),
    })
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "success") {
          alert("✅ Status berhasil dikonfirmasi!");
          btnConfirm.disabled = true;
          btnReset.disabled = true;
        } else {
          alert("❌ Gagal menyimpan status: " + data);
        }
      })
      .catch((err) => alert("❌ Gagal terhubung ke server: " + err));
  });

  // =========================================================
  // 5️⃣ LOGOUT POPUP
  // =========================================================
  const logoutBtn = document.getElementById("logoutBtn");
  const logoutPopup = document.getElementById("logoutPopup");
  const confirmLogout = document.getElementById("confirmLogout");
  const cancelLogout = document.getElementById("cancelLogout");

  logoutBtn.addEventListener("click", () => (logoutPopup.style.display = "flex"));
  cancelLogout.addEventListener("click", () => (logoutPopup.style.display = "none"));
  confirmLogout.addEventListener("click", () => (window.location.href = "../login/logout.php"));
});

fetch("get_absensi_today.php")
  .then((res) => res.json())
  .then((data) => {
    if (data && data.status) {
      absensiStatus.textContent = data.status;
      absensiStatus.style.color = data.status === "Hadir" ? "green" : data.status === "Izin" ? "orange" : data.status === "Cuti" ? "red" : "black";

      if (data.check_in) {
        const checkIn = new Date(data.check_in);
        checkInTimeDisplay.textContent = formatDateTime(checkIn);
        hasCheckedIn = true;
        btnAbsenMasuk.disabled = true;
        btnAbsenPulang.disabled = false;
      }

      if (data.check_out) {
        const checkOut = new Date(data.check_out);
        checkOutTimeDisplay.textContent = formatDateTime(checkOut);
        btnAbsenPulang.disabled = true;
      }
    }
  });
