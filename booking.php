<?php
// Ambil tanggal dari URL
$tanggal = $_GET['tanggal'] ?? '';

// Jika tidak ada tanggal, kembalikan ke jadwal
if (empty($tanggal) || strtotime($tanggal) < time()) {
    header('Location: jadwal.php');
    exit;
}

$tanggal_formatted = date('l, d F Y', strtotime($tanggal));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Pendopo - House of Redland</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Nunito+Sans:wght@400&family=Plus+Jakarta+Sans:wght@400;700&family=Poppins:wght@500;700&family=Red+Hat+Display:wght@700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="jadwal.css"> <link rel="stylesheet" href="kontak.css"> <style>
        /* Style untuk form booking */
        .booking-form-container {
            max-width: 600px;
            margin: 2em auto;
            padding: 2.5rem;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .booking-form-container h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5em;
            margin-top: 0;
        }
        .booking-form-container .form-group {
            margin-bottom: 20px;
        }
        .booking-form-container label {
            display: block;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }
        .booking-form-container input[type="text"],
        .booking-form-container textarea {
            width: 100%;
            border: 1px solid #ccc;
            padding: 12px 15px;
            font-size: 15px;
            border-radius: 6px;
            background-color: #F4F2F1;
        }
        .booking-form-container textarea {
            min-height: 100px;
            resize: vertical;
        }
        .booking-form-container .btn-submit-booking {
            width: 100%;
            padding: 15px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <header>
        <nav>
            <ul>
                <li><a href="produk.php">PRODUK</a></li>
                <li><a href="tentang.html">TENTANG</a></li>
            </ul>
        </nav>
        <h1 class="main-title"><a href="index.php">HOUSE OF REDLAND</a></h1>
        <nav>
            <ul>
                <li><a href="kontak.html">KONTAK</a></li>
                <li><a href="jadwal.php">JADWAL</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-container">

        <div class="page-header">
            <h2>Formulir Booking Pendopo</h2>
        </div>

        <div class="booking-form-container">
            <h3>Booking untuk tanggal: <?php echo htmlspecialchars($tanggal_formatted); ?></h3>
            <p>Silakan isi data di bawah. Admin kami akan menghubungi Anda untuk konfirmasi.</p>
            
            <form action="proses_booking.php" method="POST">
                <input type="hidden" name="tanggal_booked" value="<?php echo htmlspecialchars($tanggal); ?>">
                
                <div class="form-group">
                    <label for="nama_pemesan">Nama Lengkap</label>
                    <input type="text" id="nama_pemesan" name="nama_pemesan" required>
                </div>
                
                <div class="form-group">
                    <label for="no_hp">Nomor Handphone (WhatsApp)</label>
                    <input type="text" id="no_hp" name="no_hp" required>
                </div>
                
                <div class="form-group">
                    <label for="keperluan">Keperluan Acara</label>
                    <textarea id="keperluan" name="keperluan" rows="4" placeholder="Contoh: Rapat Komunitas, Acara Ulang Tahun, dll..."></textarea>
                </div>
                
                <button type="submit" class="btn-submit-booking">Kirim Permintaan Booking</button>
            </form>
        </div>

    </div>
    
    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-column">
                <h4>Kontak Kami</h4>
                <p>houseofredland@gmail.com</p>
                <p>+62 858 4903 4541</p>
            </div>
            <div class="footer-column">
                <h4>Lokasi</h4>
                <p>Tanah Merah</p>
            </div>
        </div>
        <hr class="footer-divider">
        <p class="footer-copyright">House Of Redland, All Right Reserved</p>
    </footer>

</body>
</html>