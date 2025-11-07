<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'produk_functions.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Handle tambah kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = $_POST['nama_kategori'];
    
    if (tambahKategori($nama)) {
        $_SESSION['success_message'] = "Kategori berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan kategori.";
    }
    header("Location: kelola_kategori.php");
    exit;
}

// Handle hapus kategori
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if (hapusKategori($id)) {
        $_SESSION['success_message'] = "Kategori berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus kategori.";
    }
    header("Location: kelola_kategori.php");
    exit;
}

// Ambil data kategori dengan jumlah produk
$kategori_stats = countProdukByKategori();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori</title>
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
            <li>
                <img src="../image/produk.svg" alt="Ikon Produk" class="nav-icon">
                <span>Produk</span>
            </li>
            <li>
                <img src="../image/kalender.svg" alt="Ikon Jadwal" class="nav-icon">
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
            <h1>Kelola Kategori Produk</h1>
            <a href="dashboard.php" class="btn-back">
                <span class="material-icons">arrow_back</span> Kembali
            </a>
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

        <div class="kategori-table">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategori_stats)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #999;">Belum ada kategori</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($kategori_stats as $kategori): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($kategori['nama_kategori']); ?></strong></td>
                                <td>
                                    <span class="badge-count"><?php echo $kategori['total_produk']; ?> produk</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="kelola_kategori.php?hapus=<?php echo $kategori['id']; ?>" 
                                           class="btn-table-delete" 
                                           onclick="return confirm('Yakin ingin menghapus kategori ini? Produk dengan kategori ini akan menjadi tanpa kategori.')">
                                            <span class="material-icons" style="font-size: 16px;">delete</span> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button class="fab" id="openModalBtn">+</button>
    </main>

    <!-- Modal Tambah Kategori -->
    <div id="addKategoriModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-btn" id="closeModalBtn">&times;</button>
            <h2>Tambah Kategori Baru</h2>

            <form action="kelola_kategori.php" method="POST">
                <input type="hidden" name="action" value="tambah">
                
                <div class="form-modal-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" id="nama_kategori" name="nama_kategori" placeholder="Contoh: Madu, Melon, Sayur" required>
                </div>

                <button type="submit" class="submit-modal-btn">Simpan Kategori</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('toggleBtn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });

        const modal = document.getElementById('addKategoriModal');
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
    </script>
</body>
</html>