<?php

    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'db_connect.php';
require_once 'produk_functions.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Handle tambah kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = $_POST['nama_kategori'];
    $gambar_path = 'image/default-category.png'; // default
    
    // Handle upload gambar
    if (isset($_FILES['gambar_kategori']) && $_FILES['gambar_kategori']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar_kategori']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($filetype, $allowed)) {
            $newname = 'kategori_' . time() . '.' . $filetype;
            $upload_path = 'uploads/' . $newname;
            
            if (move_uploaded_file($_FILES['gambar_kategori']['tmp_name'], $upload_path)) {
                $gambar_path = $upload_path;
            }
        }
    }
    
    // Insert ke database
    $query = "INSERT INTO kategori_produk (nama_kategori, gambar_kategori) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $nama, $gambar_path);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Kategori berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan kategori: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    header("Location: kelola_kategori.php");
    exit;
}

// Handle edit kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = intval($_POST['kategori_id']);
    $nama = $_POST['nama_kategori'];
    
    // Ambil gambar lama
    $query_old = "SELECT gambar_kategori FROM kategori_produk WHERE id = ?";
    $stmt_old = mysqli_prepare($conn, $query_old);
    mysqli_stmt_bind_param($stmt_old, "i", $id);
    mysqli_stmt_execute($stmt_old);
    $result_old = mysqli_stmt_get_result($stmt_old);
    $old_data = mysqli_fetch_assoc($result_old);
    $gambar_path = $old_data['gambar_kategori'];
    
    // Handle upload gambar baru
    if (isset($_FILES['gambar_kategori']) && $_FILES['gambar_kategori']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar_kategori']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($filetype, $allowed)) {
            $newname = 'kategori_' . time() . '.' . $filetype;
            $upload_path = 'uploads/' . $newname;
            
            if (move_uploaded_file($_FILES['gambar_kategori']['tmp_name'], $upload_path)) {
                // Hapus gambar lama jika bukan default dan ada di folder uploads
                if ($old_data['gambar_kategori'] != 'image/default-category.png' && 
                    strpos($old_data['gambar_kategori'], 'uploads/') === 0 && 
                    file_exists($old_data['gambar_kategori'])) {
                    unlink($old_data['gambar_kategori']);
                }
                $gambar_path = $upload_path;
            }
        }
    }
    
    // Update database
    $query = "UPDATE kategori_produk SET nama_kategori = ?, gambar_kategori = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $nama, $gambar_path, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Kategori berhasil diupdate!";
    } else {
        $_SESSION['error_message'] = "Gagal mengupdate kategori: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    header("Location: kelola_kategori.php");
    exit;
}

// Handle hapus kategori
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    // Ambil data gambar untuk dihapus
    $query_img = "SELECT gambar_kategori FROM kategori_produk WHERE id = ?";
    $stmt_img = mysqli_prepare($conn, $query_img);
    mysqli_stmt_bind_param($stmt_img, "i", $id);
    mysqli_stmt_execute($stmt_img);
    $result_img = mysqli_stmt_get_result($stmt_img);
    $img_data = mysqli_fetch_assoc($result_img);
    
    // Hapus kategori
    if (hapusKategori($id)) {
        // Hapus file gambar jika ada dan bukan default
        if ($img_data && $img_data['gambar_kategori'] != 'image/default-category.png' && 
            strpos($img_data['gambar_kategori'], 'uploads/') === 0 && 
            file_exists($img_data['gambar_kategori'])) {
            unlink($img_data['gambar_kategori']);
        }
        $_SESSION['success_message'] = "Kategori berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus kategori.";
    }
    header("Location: kelola_kategori.php");
    exit;
}

// Ambil data kategori dengan jumlah produk
$kategori_stats = countProdukByKategori();

// Ambil data kategori untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $query_edit = "SELECT * FROM kategori_produk WHERE id = ?";
    $stmt_edit = mysqli_prepare($conn, $query_edit);
    mysqli_stmt_bind_param($stmt_edit, "i", $edit_id);
    mysqli_stmt_execute($stmt_edit);
    $result_edit = mysqli_stmt_get_result($stmt_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}
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
    <style>
        .preview-image {
            max-width: 200px;
            max-height: 150px;
            margin-top: 10px;
            border-radius: 8px;
            display: none;
        }
        .preview-image.show {
            display: block;
        }
        .kategori-table img {
            max-width: 80px;
            max-height: 60px;
            border-radius: 4px;
            object-fit: cover;
        }
    </style>
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
                        <th>Gambar</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategori_stats)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">Belum ada kategori</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($kategori_stats as $kategori): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($kategori['gambar_kategori'] ?? 'image/default-category.png'); ?>" 
     alt="<?php echo htmlspecialchars($kategori['nama_kategori'] ?? ''); ?>">
                                </td>
                                <td><strong><?php echo htmlspecialchars($kategori['nama_kategori']); ?></strong></td>
                                <td>
                                    <span class="badge-count"><?php echo $kategori['total_produk']; ?> produk</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="kelola_kategori.php?edit=<?php echo $kategori['id']; ?>" 
                                           class="btn-table-edit">
                                            <span class="material-icons" style="font-size: 16px;">edit</span> Edit
                                        </a>
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

    <!-- Modal Tambah/Edit Kategori -->
    <div id="addKategoriModal" class="modal-overlay <?php echo $edit_data ? 'show' : ''; ?>">
        <div class="modal-content">
            <button class="close-btn" id="closeModalBtn">&times;</button>
            <h2><?php echo $edit_data ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?></h2>

            <form action="kelola_kategori.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'tambah'; ?>">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="kategori_id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="form-modal-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" 
                           id="nama_kategori" 
                           name="nama_kategori" 
                           placeholder="Contoh: Madu, Melon, Sayur" 
                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama_kategori']) : ''; ?>"
                           required>
                </div>

                <div class="form-modal-group">
                    <label for="gambar_kategori">Gambar Kategori</label>
                    <?php if ($edit_data && $edit_data['gambar_kategori']): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?php echo htmlspecialchars($edit_data['gambar_kategori']); ?>" 
                                 alt="Current" 
                                 style="max-width: 150px; border-radius: 8px;">
                            <p style="font-size: 12px; color: #666; margin-top: 5px;">Gambar saat ini</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" 
                           id="gambar_kategori" 
                           name="gambar_kategori" 
                           accept="image/*"
                           onchange="previewImage(event)"
                           <?php echo !$edit_data ? 'required' : ''; ?>>
                    <small style="color: #666;">Format: JPG, JPEG, PNG, GIF (Max 2MB)</small>
                    <img id="preview" class="preview-image" src="#" alt="Preview">
                </div>

                <button type="submit" class="submit-modal-btn">
                    <?php echo $edit_data ? 'Update Kategori' : 'Simpan Kategori'; ?>
                </button>
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
            // Redirect untuk clear edit mode
            window.location.href = 'kelola_kategori.php';
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
                window.location.href = 'kelola_kategori.php';
            }
        });

        function previewImage(event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>