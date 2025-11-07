<?php
session_start();

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'house_of_redland');

// Buat koneksi database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset UTF-8
$conn->set_charset("utf8mb4");

// Ambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Query ke database untuk cek user
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verifikasi password
    // Jika password di database sudah di-hash, gunakan password_verify()
    // Jika masih plain text, gunakan perbandingan biasa dulu
    if (password_verify($password, $user['password'])) {
        // Login berhasil dengan password hash
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id']; // PENTING untuk CMS
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: dashboard.php");
        exit;
    } elseif ($password === $user['password']) {
        // Untuk backward compatibility jika password masih plain text
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id']; // PENTING untuk CMS
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: dashboard.php");
        exit;
    } else {
        // Password salah
        header("Location: login.php?error=1");
        exit;
    }
} else {
    // Username tidak ditemukan
    header("Location: login.php?error=1");
    exit;
}

$stmt->close();
$conn->close();
?>