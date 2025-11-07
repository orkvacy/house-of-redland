<?php
session_start();

// Cek apakah user sudah login atau belum
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Include fungsi produk
require_once 'produk_functions.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Ambil semua kategori
$kategori_list = getAllKategori();

// Ambil semua produk milik user
$produk_list = getProdukByUser($user_id);

// Handle form submission untuk tambah produk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $judul = $_POST['judul_buah'];
    $penjelasan = $_POST['penjelasan_buah'];
    $kategori_id = !empty($_POST['kategori_id']) ? intval($_POST['kategori_id']) : null;
    
    if (isset($_FILES['gambar_buah']) && $_FILES['gambar_buah']['error'] == 0) {
        $upload = uploadGambar($_FILES['gambar_buah']);
        
        if ($upload['success']) {
            if (tambahProduk($user_id, $judul, $penjelasan, $upload['filename'], $kategori_id)) {
                $_SESSION['success_message'] = "Produk berhasil ditambahkan!";
                header("Location: dashboard.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan produk ke database.";
            }
        } else {
            $_SESSION['error_message'] = $upload['message'];
        }
    } else {
        $_SESSION['error_message'] = "Gambar harus diupload.";
    }
}

// Handle hapus produk
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if (hapusProduk($id, $user_id)) {
        $_SESSION['success_message'] = "Produk berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus produk.";
    }
    header("Location: dashboard.php");
    exit;
}

// Filter produk berdasarkan kategori jika ada parameter
$filter_kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : null;
if ($filter_kategori) {
    $produk_list = array_filter($produk_list, function($produk) use ($filter_kategori) {
        return $produk['kategori_id'] == $filter_kategori;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="cms-style.css">
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
        <div class="page-header">
            <h1>Produk Anda</h1>
            
            <!-- Filter Kategori -->
            <div class="filter-section">
                <select id="filterKategori" class="filter-select" onchange="filterByKategori(this.value)">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori_list as $kategori): ?>
                        <option value="<?php echo $kategori['id']; ?>" <?php echo ($filter_kategori == $kategori['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <a href="kelola_kategori.php" class="btn-manage-kategori">
                    <span class="material-icons">category</span>
                    Kelola Kategori
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error_message']; 
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Grid produk -->
        <div class="produk-grid">
            <?php if (empty($produk_list)): ?>
                <div class="empty-state">
                    <p>Belum ada produk. Klik tombol + untuk menambah produk baru.</p>
                </div>
            <?php else: ?>
                <?php foreach ($produk_list as $produk): ?>
                    <div class="produk-card">
                        <?php if ($produk['nama_kategori']): ?>
                            <div class="produk-kategori-badge">
                                <?php echo htmlspecialchars($produk['nama_kategori']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="produk-image">
                            <img src="../uploads/<?php echo htmlspecialchars($produk['gambar_buah']); ?>" alt="<?php echo htmlspecialchars($produk['judul_buah']); ?>">
                        </div>
                        <div class="produk-content">
                            <h3><?php echo htmlspecialchars($produk['judul_buah']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($produk['penjelasan_buah'], 0, 100)); ?>...</p>
                            <div class="produk-actions">
                                <a href="edit_produk.php?id=<?php echo $produk['id']; ?>" class="btn-edit">
                                    <span class="material-icons">edit</span> Edit
                                </a>
                                <a href="dashboard.php?hapus=<?php echo $produk['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    <span class="material-icons">delete</span> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button class="fab" id="openModalBtn">+</button>
    </main>

    <!-- Modal Tambah Produk -->
    <div id="addProductModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-btn" id="closeModalBtn">&times;</button>
            <h2>Tambah Produk Baru</h2>

            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="tambah">
                
                <div class="form-modal-group">
                    <label for="kategori_id">Kategori Produk</label>
                    <select id="kategori_id" name="kategori_id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori_list as $kategori): ?>
                            <option value="<?php echo $kategori['id']; ?>">
                                <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-modal-group">
                    <label for="judul_buah">Judul Produk</label>
                    <input type="text" id="judul_buah" name="judul_buah" placeholder="Contoh: Melon Hidroponik Ceria" required>
                </div>

                <div class="form-modal-group">
                    <label for="penjelasan_buah">Penjelasan Produk</label>
                    <textarea id="penjelasan_buah" name="penjelasan_buah" rows="4" placeholder="Jelaskan tentang produk ini..." required></textarea>
                </div>

                <div class="form-modal-group">
                    <label for="gambar_buah">Gambar Produk</label>
                    <input type="file" id="gambar_buah" name="gambar_buah" class="custom-file-input" accept="image/*" required>
                    <label for="gambar_buah" class="custom-file-label">Pilih file...</label>
                </div>

                <button type="submit" class="submit-modal-btn">Simpan Produk</button>
            </form>
        </div>
    </div>
    
    <script>
        // Toggle sidebar
        document.getElementById('toggleBtn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });

        // Modal functions
        const modal = document.getElementById('addProductModal');
        const openBtn = document.getElementById('openModalBtn');
        const closeBtn = document.getElementById('closeModalBtn');

        openBtn.addEventListener('click', () => {
            modal.classList.add('show');
        });

        closeBtn.addEventListener('click', () => {
            modal.classList.remove('show');
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });

        // File input label update
        document.getElementById('gambar_buah').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Pilih file...';
            document.querySelector('.custom-file-label').textContent = fileName;
        });

        // Filter kategori function
        function filterByKategori(kategoriId) {
            if (kategoriId) {
                window.location.href = 'dashboard.php?kategori=' + kategoriId;
            } else {
                window.location.href = 'dashboard.php';
            }
        }
    </script>
</body>
</html>