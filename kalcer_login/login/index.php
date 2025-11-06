<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil user dari database (gunakan nama kolom id_user sesuai DB)
    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    // Cek password
    if ($user && password_verify($password, $user['password'])) {
        // simpan id_user supaya worker.php bisa baca
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect sesuai role
        if ($user['role'] === 'owner') {
            header("Location: ../owner/owner.php");
        } else {
            header("Location: ../worker/worker.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet" />
  </head>

  <body>
    <div class="container">
<!-- Login -->
<div class="form-box login">
  <form method="POST" action="">
    <h1>Login</h1>
    <div class="input-box">
      <input type="text" name="username" placeholder="Username" required />
      <i class="fa-solid fa-user"></i>
    </div>
    <div class="input-box">
      <input type="password" name="password" placeholder="Password" required />
      <i class="fa-solid fa-lock"></i>
    </div>
    <div class="forgot-link">
      
      <a href="#">Forgot Password?</a>
    </div>
    <button type="submit" class="btn">Login</button>
    <p>or login with social platforms</p>
    <div class="social-icons">
      <a href="#"><i class="fa-brands fa-google"></i></a>
      <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#"><i class="fa-brands fa-instagram"></i></a>
      <a href="#"><i class="fa-brands fa-apple"></i></a>
    </div>

    <!-- Tambahkan ini untuk menampilkan error -->
    <?php if (!empty($error)): ?>
      <p style="color:red; text-align:center; margin-top:10px;">
        <?= $error ?>
      </p>
    <?php endif; ?>
  </form>
</div>


      <!-- Registrasi -->
         <div class="form-box register">
        <form method="POST" action="register_process.php">
    <h1>Registraction</h1>
        <div class="input-box">
      <input type="text" name="username" placeholder="Username" required />
      <i class="fa-solid fa-user"></i>
    </div>
    <div class="input-box">
      <input type="email" name="email" placeholder="Email" required />
      <i class="fa-solid fa-envelope"></i>
    </div>
    <div class="input-box">
      <input type="password" name="password" placeholder="Password" required />
      <i class="fa-solid fa-lock"></i>
    </div>
          <button type="submit" class="btn">Register</button>
          <p>or register with social platforms</p>
          <div class="social-icons">
            <a href="#"><i class="fa-brands fa-google"></i></a>
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-apple"></i></a>
          </div>
        </form>
      </div>
      <!-- Toggle Pindah-Pindahnya -->
      <div class="toggle-box">
        <div class="toggle-panel toggle-left">
          <h1>Hello, Welcome!</h1>
          <p>Dont have an account?</p>
          <button class="btn register-btn">Register</button>
        </div>
        <div class="toggle-panel toggle-right">
          <h1>Welcome Back!</h1>
          <p>Already have an account?</p>
          <button class="btn login-btn">Login</button>
        </div>
      </div>
    </div>

    <script src="script.js"></script>
  </body>
</html>
