<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'db_connect.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Handle Aksi Admin (Accept / Decline / Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    if ($action == 'accept') {
        // 1. Ubah status jadi 'accepted'
        $query_accept = "UPDATE jadwal_penyewaan SET status = 'accepted' WHERE id = ?";
        $stmt_accept = mysqli_prepare($conn, $query_accept);
        mysqli_stmt_bind_param($stmt_accept, "i", $id);
        
        if (mysqli_stmt_execute($stmt_accept)) {
            $_SESSION['success_message'] = "Booking berhasil di-Accept!";
            
            // 2. Ambil tanggal yang di-accept
            $query_date = "SELECT tanggal_booked FROM jadwal_penyewaan WHERE id = ?";
            $stmt_date = mysqli_prepare($conn, $query_date);
            mysqli_stmt_bind_param($stmt_date, "i", $id);
            mysqli_stmt_execute($stmt_date);
            $result_date = mysqli_stmt_get_result($stmt_date);
            $row_date = mysqli_fetch_assoc($result_date);
            $tanggal_accepted = $row_date['tanggal_booked'];

            // 3. Hapus semua permintaan 'pending' LAINNYA di tanggal yang sama
            $query_decline_others = "DELETE FROM jadwal_penyewaan WHERE tanggal_booked = ? AND status = 'pending'";
            $stmt_decline = mysqli_prepare($conn, $query_decline_others);
            mysqli_stmt_bind_param($stmt_decline, "s", $tanggal_accepted);
            mysqli_stmt_execute($stmt_decline);
            mysqli_stmt_close($stmt_decline);
        } else {
            $_SESSION['error_message'] = "Gagal meng-accept booking.";
        }
        mysqli_stmt_close($stmt_accept);

    } elseif ($action == 'decline' || $action == 'delete') {
        // Hapus data dari database (baik itu 'pending' atau 'accepted')
        $query_delete = "DELETE FROM jadwal_penyewaan WHERE id = ?";
        $stmt_delete = mysqli_prepare($conn, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            $_SESSION['success_message'] = "Booking berhasil dihapus/ditolak.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus booking.";
        }
        mysqli_stmt_close($stmt_delete);
    }
    
    header("Location: kelola_jadwal.php");
    exit;
}


// Ambil semua data booking (Pending)
$query_pending = "SELECT * FROM jadwal_penyewaan WHERE status = 'pending' ORDER BY tanggal_booked ASC, created_at ASC";
$result_pending = mysqli_query($conn, $query_pending);
$bookings_pending = mysqli_fetch_all($result_pending, MYSQLI_ASSOC);

// Ambil semua data booking (Accepted)
$query_accepted = "SELECT * FROM jadwal_penyewaan WHERE status = 'accepted' ORDER BY tanggal_booked DESC";
$result_accepted = mysqli_query($conn, $query_accepted);
$bookings_accepted = mysqli_fetch_all($result_accepted, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Penyewaan</title>
    <link rel="stylesheet" href="cms-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .btn-table-accept {
            display: inline-flex; align-items: center; gap: 4px; padding: 8px 14px;
            background-color: #28a745; color: white; text-decoration: none;
            border-radius: 6px; font-size: 12px; font-weight: 500;
        }
        .btn-table-decline {
            display: inline-flex; align-items: center; gap: 4px; padding: 8px 14px;
            background-color: #dc3545; color: white; text-decoration: none;
            border-radius: 6px; font-size: 12px; font-weight: 500;
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
                <a href="dashboard.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                    <img src="../image/produk.svg" alt="Ikon Produk" class="nav-icon">
                    <span>Produk</span>
                </a>
            </li>
            <li class="active">
                <a href="kelola_jadwal.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                    <img src="../image/kalender.svg" alt="Ikon Jadwal" class="nav-icon">
                    <span>Jadwal Penyewaan</span>
                </a>
            </li>
            <li>
                <a href="#" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                    <img src="../image/berita.svg" alt="Ikon Berita" class="nav-icon">
                    <span>Berita</span>
                </a>
            </li>
        </ul>
    </nav>

    <main class="main-content">
        <h1>Kelola Jadwal Penyewaan</h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <h2 style="margin-top: 30px;">Permintaan Booking Baru (Pending)</h2>
        <div class="kategori-table">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Pemesan</th>
                        <th>No. HP</th>
                        <th>Keperluan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings_pending)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">Tidak ada permintaan booking baru</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings_pending as $booking): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars(date('d F Y', strtotime($booking['tanggal_booked']))); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['nama_pemesan']); ?></td>
                                <td><?php echo htmlspecialchars($booking['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars($booking['keperluan']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="kelola_jadwal.php?action=accept&id=<?php echo $booking['id']; ?>" class="btn-table-accept" onclick="return confirm('Anda yakin ingin MENYETUJUI booking ini? Semua permintaan lain di tanggal yang sama akan ditolak.')">
                                            <span class="material-icons" style="font-size: 16px;">check</span> Accept
                                        </a>
                                        <a href="kelola_jadwal.php?action=decline&id=<?php echo $booking['id']; ?>" class="btn-table-decline" onclick="return confirm('Yakin ingin MENOLAK booking ini?')">
                                            <span class="material-icons" style="font-size: 16px;">close</span> Decline
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <h2 style="margin-top: 40px;">Booking Dikonfirmasi (Accepted)</h2>
        <div class="kategori-table">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Pemesan</th>
                        <th>No. HP</th>
                        <th>Keperluan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings_accepted)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">Belum ada booking yang dikonfirmasi</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings_accepted as $booking): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars(date('d F Y', strtotime($booking['tanggal_booked']))); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['nama_pemesan']); ?></td>
                                <td><?php echo htmlspecialchars($booking['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars($booking['keperluan']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="kelola_jadwal.php?action=delete&id=<?php echo $booking['id']; ?>" class="btn-table-delete" onclick="return confirm('Yakin ingin MENGHAPUS booking ini? Tanggal akan menjadi kosong kembali.')">
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

    </main>
    <script>
        document.getElementById('toggleBtn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>