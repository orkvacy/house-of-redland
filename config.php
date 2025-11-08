<?php
// config.php - Koneksi Database

// Konfigurasi database
define('DB_HOST', 'sql201.infinityfree.com'); // ganti XXX sesuai host yang tertera
define('DB_USER', 'if0_40355573'); // username database kamu
define('DB_PASS', 'IniPassword1234'); // password yang kamu buat di InfinityFree
define('DB_NAME', 'if0_40355573_houseofredland'); // nama database kamu

// Membuat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke utf8
mysqli_set_charset($conn, "utf8");
?>