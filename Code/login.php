<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; 

    // Ambil username saja
    $sql = "SELECT * FROM pengguna WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Verifikasi password
    if ($data && md5($password) === $data['password']) {
      $_SESSION['username'] = $data['username'];
      $_SESSION['role'] = $data['role'];

      $_SESSION['nama_user'] = $data['nama_user'];
      header("Location: dashboard.php");
      exit();
    } else {
      $error = "Login gagal";
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>

<body class="login-page">
  <div class="login-blur-image"></div>
  <div class="login-container">
    <img src="img/MTN.webp" alt="Logo" class="login-logo">
    <h2>Welcome, User!</h2>
    <?php if (isset($error)) echo "<div class='login-error'>" . htmlspecialchars($error) . "</div>"; ?>
    <form method="POST" autocomplete="off">
      <div class="login-form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>
      </div>
      <div class="login-form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
      </div>
      <div class="login-btn-group">
        <button type="submit" class="login-btn">Login</button>
      </div>
    </form>
  </div>

  <footer class="login-footer">
    <div class="login-footer-left">
      <img src="img/MTN.webp" alt="Logo 1">
      <img src="img/logojasamarga.jpg" alt="Logo 2">
      <img src="img/unpam.png" alt="Logo 3">
      <img src="img/logo-prodi.png" alt="Logo 4">
      <img src="img/logo-bumn.png" alt="Logo 5">
    </div>
    <div class="login-footer-right">
      Kuliah Kerja Praktek 2025
    </div>
  </footer>
</body>
</html>