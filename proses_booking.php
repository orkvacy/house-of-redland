<?php
require_once 'config.php'; // Panggil koneksi database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $tanggal = $_POST['tanggal_booked'];
    $nama = $_POST['nama_pemesan'];
    $no_hp = $_POST['no_hp'];
    $keperluan = $_POST['keperluan'];
    
    // Validasi sederhana
    if (empty($tanggal) || empty($nama) || empty($no_hp)) {
        die("Data tidak lengkap. Silakan kembali dan isi semua field.");
    }
    
    // Cek apakah tanggal ini sudah ada yang 'pending' atau 'accepted'
    $query_cek = "SELECT id FROM jadwal_penyewaan WHERE tanggal_booked = ? AND (status = 'pending' OR status = 'accepted')";
    $stmt_cek = mysqli_prepare($conn, $query_cek);
    mysqli_stmt_bind_param($stmt_cek, "s", $tanggal);
    mysqli_stmt_execute($stmt_cek);
    $result_cek = mysqli_stmt_get_result($stmt_cek);
    
    if (mysqli_num_rows($result_cek) > 0) {
        // Jika sudah ada, tampilkan pesan error
        echo "<!DOCTYPE html><html><head><title>Gagal</title><link rel='stylesheet' href='style.css'></head><body style='padding: 20px; text-align: center;'>";
        echo "<h2>Booking Gagal</h2>";
        echo "<p>Maaf, tanggal <strong>".date('d F Y', strtotime($tanggal))."</strong> sudah ada yang membooking atau sedang dalam proses review.</p>";
        echo "<a href='jadwal.php' style='text-decoration: none; color: #000; font-weight: bold;'>Kembali ke Kalender</a>";
        echo "</body></html>";
    } else {
        // Jika kosong, masukkan data sebagai 'pending'
        $query_insert = "INSERT INTO jadwal_penyewaan (tanggal_booked, nama_pemesan, no_hp, keperluan, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt_insert = mysqli_prepare($conn, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "ssss", $tanggal, $nama, $no_hp, $keperluan);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            // Jika berhasil, tampilkan pesan sukses
            echo "<!DOCTYPE html><html><head><title>Sukses</title><link rel='stylesheet' href='style.css'></head><body style='padding: 20px; text-align: center;'>";
            echo "<h2>Booking Terkirim!</h2>";
            echo "<p>Permintaan booking Anda untuk tanggal <strong>".date('d F Y', strtotime($tanggal))."</strong> telah terkirim.</p>";
            echo "<p>Admin kami akan segera menghubungi Anda melalui WhatsApp untuk konfirmasi.</p>";
            echo "<a href='jadwal.php' style='text-decoration: none; color: #000; font-weight: bold;'>Kembali ke Kalender</a>";
            echo "</body></html>";
        } else {
            echo "Terjadi kesalahan. Silakan coba lagi. Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_insert);
    }
    mysqli_stmt_close($stmt_cek);
    
} else {
    // Jika diakses langsung, redirect
    header('Location: jadwal.php');
}
?>