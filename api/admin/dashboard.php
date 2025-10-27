<?php
session_start();

// Cek apakah user sudah login atau belum
// Jika belum, tendang kembali ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Jika sudah login, ambil data user dari session
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="dashboard-body">

    <nav class="sidebar">
        <button class="toggle-btn" id="toggleBtn">&larr;</button>

        <div class="user-profile">
            <img src="https://i.pravatar.cc/40?img=1" alt="Profile Picture">
            <div class="user-info">
                <span><?php echo htmlspecialchars($username); ?></span>
                <p><?php echo htmlspecialchars($role); ?></p>
            </div>
        </div>

        <ul class="nav-links">
            <li class="active">
                <i class="material-icons">home</i>
                <span>Home</span>
            </li>
            <li>
                <i class="material-icons">bar_chart</i>
                <span>Statistic</span>
            </li>
        </ul>
    </nav>

    <main class="main-content">
        <h1>Produk Anda</h1>

        <button class="fab">+</button>
    </main>

    <script src="script.js"></script>
</body>
</html>