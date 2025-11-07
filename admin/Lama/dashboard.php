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
            <img src="../image/pendopoawal.png" alt="Profile Picture">
            
            <div class="user-info">
                <span><?php echo htmlspecialchars($username); ?></span>
                <p><?php echo htmlspecialchars($role); ?></p>
            </div>
        </div>

        <ul class="nav-links">
            <li class="active">
                <img src="../image/produk.svg" alt="Ikon Produk" class="nav-icon">
                <span>Produk</span>
            </li>
            <li>
                <img src="../image/kalender.svg" alt="Ikon Jadwal Penyewaan" class="nav-icon">
                <span>Jadwal Penyewaan</span>
            </li>
            <li>
                <img src="../image/berita.svg" alt="Ikon Berita" class="nav-icon">
                <span>Berita</span>
            </li>
            </ul>
    </nav>

    <main class="main-content">
        <h1>Produk Anda</h1>

        <button class="fab" id="openModalBtn">+</button>
    </main>

    <div id="addProductModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-btn" id="closeModalBtn">&times;</button>
            <h2>Tambah Produk Baru</h2>

            <form action="#" method="POST" enctype="multipart/form-data">
                
                <div class="form-modal-group">
                    <label for="judul_buah">Judul Buah</label>
                    <input type="text" id="judul_buah" name="judul_buah" placeholder="Contoh: Melon Hidroponik Ceria" required>
                </div>

                <div class="form-modal-group">
                    <label for="penjelasan_buah">Penjelasan Buah</label>
                    <textarea id="penjelasan_buah" name="penjelasan_buah" rows="4" placeholder="Jelaskan tentang produk buah ini..." required></textarea>
                </div>

                <div class="form-modal-group">
                    <label for="gambar_buah">Gambar Buah</label>
                    <input type="file" id="gambar_buah" name="gambar_buah" class="custom-file-input" required>
                    <label for="gambar_buah" class="custom-file-label">Pilih file...</label>
                </div>

                <button type="submit" class="submit-modal-btn">Simpan Produk</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>