<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'produk_functions.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Ambil ID produk dari URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$produk_id = intval($_GET['id']);
$produk = getProdukById($produk_id, $user_id);

// Jika produk tidak ditemukan atau bukan milik user
if (!$produk) {
    $_SESSION['error_message'] = "Produk tidak ditemukan.";
    header("Location: dashboard.php");
    exit;
}

// Handle form submission untuk update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul_buah'];
    $penjelasan = $_POST['penjelasan_buah'];
    $gambar_baru = null;
    
    // Cek apakah ada gambar baru diupload
    if (isset($_FILES['gambar_buah']) && $_FILES['gambar_buah']['error'] == 0) {
        $upload = uploadGambar($_FILES['gambar_buah']);
        
        if ($upload['success']) {
            $gambar_baru = $upload['filename'];
            // Hapus gambar lama
            if (file_exists("../uploads/" . $produk['gambar_buah'])) {
                unlink("../uploads/" . $produk['gambar_buah']);
            }
        } else {
            $_SESSION['error_message'] = $upload['message'];
            header("Location: edit_produk.php?id=" . $produk_id);
            exit;
        }
    }
    
    if (updateProduk($produk_id, $user_id, $judul, $penjelasan, $gambar_baru)) {
        $_SESSION['success_message'] = "Produk berhasil diupdate!";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal mengupdate produk.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
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
        <div class="edit-header">
            <h1>Edit Produk</h1>
            <a href="dashboard.php" class="btn-back">
                <span class="material-icons">arrow_back</span> Kembali
            </a>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error_message']; 
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="edit-form-container">
            <form action="edit_produk.php?id=<?php echo $produk_id; ?>" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="judul_buah">Judul Buah</label>
                    <input type="text" id="judul_buah" name="judul_buah" value="<?php echo htmlspecialchars($produk['judul_buah']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="penjelasan_buah">Penjelasan Buah</label>
                    <textarea id="penjelasan_buah" name="penjelasan_buah" rows="6" required><?php echo htmlspecialchars($produk['penjelasan_buah']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Gambar Saat Ini</label>
                    <div class="current-image">
                        <img src="../uploads/<?php echo htmlspecialchars($produk['gambar_buah']); ?>" alt="Current Image">
                    </div>
                </div>

                <div class="form-group">
                    <label for="gambar_buah">Ganti Gambar (Opsional)</label>
                    <input type="file" id="gambar_buah" name="gambar_buah" class="custom-file-input" accept="image/*">
                    <label for="gambar_buah" class="custom-file-label">Pilih file baru...</label>
                    <small>Kosongkan jika tidak ingin mengganti gambar</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <span class="material-icons">save</span> Simpan Perubahan
                    </button>
                    <a href="dashboard.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>