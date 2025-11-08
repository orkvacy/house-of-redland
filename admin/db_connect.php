<?php
// File koneksi database untuk hosting InfinityFree
    
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Informasi ini bisa kamu dapat dari menu "MySQL Databases" di cPanel InfinityFree
define('DB_HOST', 'sql201.infinityfree.com'); // ganti XXX sesuai host yang tertera
define('DB_USER', 'if0_40355573'); // username database kamu
define('DB_PASS', 'IniPassword1234'); // password yang kamu buat di InfinityFree
define('DB_NAME', 'if0_40355573_houseofredland'); // nama database kamu

// Membuat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset UTF-8
$conn->set_charset("utf8mb4");

// Jika berhasil
// echo "Koneksi berhasil!";
?>
