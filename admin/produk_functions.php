<?php
require_once 'db_connect.php';

// ========== FUNGSI KATEGORI ==========

// Fungsi untuk mengambil semua kategori
function getAllKategori() {
    global $conn;
    
    $result = $conn->query("SELECT * FROM kategori_produk ORDER BY nama_kategori ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi untuk menambah kategori
function tambahKategori($nama) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO kategori_produk (nama_kategori) VALUES (?)");
    $stmt->bind_param("s", $nama);
    
    return $stmt->execute();
}

// Fungsi untuk update kategori
function updateKategori($id, $nama) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE kategori_produk SET nama_kategori = ? WHERE id = ?");
    $stmt->bind_param("si", $nama, $id);
    
    return $stmt->execute();
}

// Fungsi untuk hapus kategori
function hapusKategori($id) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM kategori_produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

// ========== FUNGSI PRODUK ==========

// Fungsi untuk menambah produk (dengan kategori)
function tambahProduk($user_id, $judul, $penjelasan, $gambar, $kategori_id = null) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO produk_buah (user_id, judul_buah, penjelasan_buah, gambar_buah, kategori_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $user_id, $judul, $penjelasan, $gambar, $kategori_id);
    
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

// Fungsi untuk mengambil semua produk berdasarkan user (dengan info kategori)
function getProdukByUser($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT p.*, k.nama_kategori 
        FROM produk_buah p 
        LEFT JOIN kategori_produk k ON p.kategori_id = k.id 
        WHERE p.user_id = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi untuk mengambil produk berdasarkan kategori
function getProdukByKategori($kategori_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT p.*, k.nama_kategori 
        FROM produk_buah p 
        LEFT JOIN kategori_produk k ON p.kategori_id = k.id 
        WHERE p.kategori_id = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->bind_param("i", $kategori_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi untuk mengambil semua produk (untuk halaman publik)
function getAllProduk() {
    global $conn;
    
    $result = $conn->query("
        SELECT p.*, k.nama_kategori 
        FROM produk_buah p 
        LEFT JOIN kategori_produk k ON p.kategori_id = k.id 
        ORDER BY p.created_at DESC
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi untuk mengambil satu produk berdasarkan ID
function getProdukById($id, $user_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT p.*, k.nama_kategori 
        FROM produk_buah p 
        LEFT JOIN kategori_produk k ON p.kategori_id = k.id 
        WHERE p.id = ? AND p.user_id = ?
    ");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fungsi untuk update produk (dengan kategori)
function updateProduk($id, $user_id, $judul, $penjelasan, $kategori_id = null, $gambar = null) {
    global $conn;
    
    if ($gambar) {
        $stmt = $conn->prepare("UPDATE produk_buah SET judul_buah = ?, penjelasan_buah = ?, gambar_buah = ?, kategori_id = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssiii", $judul, $penjelasan, $gambar, $kategori_id, $id, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE produk_buah SET judul_buah = ?, penjelasan_buah = ?, kategori_id = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssiii", $judul, $penjelasan, $kategori_id, $id, $user_id);
    }
    
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

// Fungsi untuk hapus produk
function hapusProduk($id, $user_id) {
    global $conn;
    
    // Ambil nama file gambar terlebih dahulu
    $produk = getProdukById($id, $user_id);
    if ($produk && file_exists("../uploads/" . $produk['gambar_buah'])) {
        unlink("../uploads/" . $produk['gambar_buah']);
    }
    
    $stmt = $conn->prepare("DELETE FROM produk_buah WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

// Fungsi untuk upload gambar
function uploadGambar($file) {
    $target_dir = "../uploads/";
    
    // Buat folder uploads jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Validasi file
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    if (!in_array($file_extension, $allowed_types)) {
        return array('success' => false, 'message' => 'Format file tidak diizinkan. Hanya JPG, JPEG, PNG, GIF, dan WEBP.');
    }
    
    // Validasi ukuran file (max 5MB)
    if ($file["size"] > 5000000) {
        return array('success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB.');
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('success' => true, 'filename' => $new_filename);
    } else {
        return array('success' => false, 'message' => 'Gagal mengupload file.');
    }
}

// Fungsi untuk menghitung jumlah produk per kategori
function countProdukByKategori() {
    global $conn;
    
    $result = $conn->query("
        SELECT k.id, k.nama_kategori, COUNT(p.id) as total_produk 
        FROM kategori_produk k 
        LEFT JOIN produk_buah p ON k.id = p.kategori_id 
        GROUP BY k.id 
        ORDER BY k.nama_kategori ASC
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>