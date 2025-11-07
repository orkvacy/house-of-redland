<?php
session_start();

// --- INI HANYA CONTOH LOGIN ---
// Di aplikasi nyata, Anda harus cek ke database
$valid_username = "admin";
$valid_password = "password123"; // Ganti ini dengan password Anda

// Ambil data yang dikirim dari form login.php
$username = $_POST['username'];
$password = $_POST['password'];

// Cek apakah username dan password cocok
// (Di aplikasi nyata, gunakan password_verify() untuk password yang di-hash)
if ($username === $valid_username && $password === $valid_password) {
    
    // Jika login berhasil:
    // 1. Simpan data user ke dalam session
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = "Sifwah Fatin Sofwani"; // Ambil dari database
    $_SESSION['role'] = "Admin"; // Ambil dari database
    
    // 2. Arahkan user ke halaman dashboard
    header("Location: dashboard.php");
    exit;

} else {
    // Jika login gagal:
    // 1. Kembalikan user ke halaman login
    // (Anda bisa tambahkan ?error=1 untuk menampilkan pesan error di login.php)
    header("Location: login.php?error=1");
    exit;
}
?>