<?php
// --- LOGIKA PHP UNTUK KALENDER ---

// 1. Tentukan Bulan dan Tahun
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// 2. Dapatkan Tanggal Hari Ini (Untuk Highlight)
$today_date = date('Y-m-d'); // Format: 2025-11-02

// 3. Data Booking (SIMULASI DATABASE)
$booked_dates = [
    '2025-11-10',
    '2025-11-15',
    '2025-11-16',
    '2025-12-05'
];

// 4. Perhitungan Kalender
$first_day_of_month_timestamp = mktime(0, 0, 0, $month, 1, $year);
$total_days_in_month = date('t', $first_day_of_month_timestamp);
$first_day_of_week = date('N', $first_day_of_month_timestamp); 
$month_name = date('F', $first_day_of_month_timestamp);

// 5. Logika Navigasi
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year = $year - 1;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month == 13) {
    $next_month = 1;
    $next_year = $year + 1;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pendopo - House of Redland</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Nunito+Sans:wght@400&family=Plus+Jakarta+Sans:wght@400;700&family=Poppins:wght@500;700&family=Red+Hat+Display:wght@700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="jadwal.css">

</head>
<body>

    <div class="page-blur-overlay"></div>

    <header>
        <nav>
            <ul>
                <li class="nav-item-dropdown">
                    <a href="#">PRODUK</a>
                    <div class="product-dropdown">
                        <a href="#" class="dropdown-item">
                            <img src="image/image6.png" alt="Madu">
                            <h3>Madu</h3>
                        </a>
                        <a href="melon.html" class="dropdown-item">
                            <img src="image/image7.png" alt="Melon">
                            <h3>Melon</h3>
                        </a>
                        <a href="#" class="dropdown-item">
                            <img src="image/image8.png" alt="Sayur">
                            <h3>Sayur</h3>
                        </a>
                    </div>
                </li>
                <li><a href="tentang.html">TENTANG</a></li>
            </ul>
        </nav>
        <h1 class="main-title"><a href="index.html">HOUSE OF REDLAND</a></h1>
        <nav>
            <ul>
                <li><a href="kontak.html">KONTAK</a></li>
                <li><a href="jadwal.php">JADWAL</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-container">

        <div class="page-header">
            <h2>Jadwal Peminjaman</h2>
        </div>

        <div class="calendar-month-header">
            <h3><?php echo $month_name . ' ' . $year; ?></h3>
        </div>

        <div class="calendar-wrapper">
            
            <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="calendar-arrow-nav">
                &lt;
            </a>

            <table class="calendar">
                <thead>
                    <tr>
                        <th>Senin</th>
                        <th>Selasa</th>
                        <th>Rabu</th>
                        <th>Kamis</th>
                        <th>Jumat</th>
                        <th>Sabtu</th>
                        <th>Minggu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        $day_count = 1;
                        $empty_cells = $first_day_of_week - 1; 

                        for ($i = 0; $i < $empty_cells; $i++) {
                            echo "<td class='other-month'></td>";
                        }

                        while ($day_count <= $total_days_in_month) {
                            
                            if (($day_count + $empty_cells - 1) % 7 == 0 && $day_count != 1) {
                                echo "</tr><tr>";
                            }

                            $current_date = $year . '-' . $month . '-' . str_pad($day_count, 2, '0', STR_PAD_LEFT);
                            
                            $css_classes = [];

                            if ($current_date == $today_date) {
                                $css_classes[] = 'today';
                            }
                            if (in_array($current_date, $booked_dates)) {
                                $css_classes[] = 'booked';
                            }
                            
                            $class_string = empty($css_classes) ? '' : 'class="' . implode(' ', $css_classes) . '"';

                            echo "<td $class_string>";
                            echo "<span class='day-number'>$day_count</span>";
                            echo "</td>";

                            $day_count++;
                        }

                        $remaining_cells = 7 - (($total_days_in_month + $empty_cells) % 7);
                        if ($remaining_cells < 7) {
                            for ($i = 0; $i < $remaining_cells; $i++) {
                                echo "<td class='other-month'></td>";
                            }
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
            
            <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="calendar-arrow-nav">
                &gt;
            </a>

        </div> </div>
    
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

    <script>
        const productNav = document.querySelector('.nav-item-dropdown');
        const productDropdown = document.querySelector('.product-dropdown');
        const blurOverlay = document.querySelector('.page-blur-overlay');
        let menuTimer; 

        if (productNav && productDropdown && blurOverlay) {
            
            const showMenu = () => {
                clearTimeout(menuTimer); 
                blurOverlay.style.display = 'block';
                productDropdown.style.opacity = '1';
                productDropdown.style.visibility = 'visible';
                productDropdown.style.pointerEvents = 'auto';
            };

            const hideMenu = () => {
                menuTimer = setTimeout(() => {
                    blurOverlay.style.display = 'none';
                    productDropdown.style.opacity = '0';
                    productDropdown.style.visibility = 'hidden';
                    productDropdown.style.pointerEvents = 'none';
                }, 200);
            };

            productNav.addEventListener('mouseenter', showMenu);
            productNav.addEventListener('mouseleave', hideMenu);
            productDropdown.addEventListener('mouseenter', showMenu);
            productDropdown.addEventListener('mouseleave', hideMenu);
        }
    </script>

</body>
</html>
