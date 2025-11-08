<?php
require_once 'config.php';

// Ambil kategori_id dari URL
$kategori_id = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

// Ambil semua kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori_produk ORDER BY id ASC";
$result_kategori = mysqli_query($conn, $query_kategori);
$kategori_list = [];
if ($result_kategori) {
    while ($row = mysqli_fetch_assoc($result_kategori)) {
        $kategori_list[] = $row;
    }
}

// Ambil nama kategori yang dipilih
$nama_kategori = '';
$query_nama = "SELECT nama_kategori FROM kategori_produk WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_nama);
mysqli_stmt_bind_param($stmt, "i", $kategori_id);
mysqli_stmt_execute($stmt);
$result_nama = mysqli_stmt_get_result($stmt);
if ($row_nama = mysqli_fetch_assoc($result_nama)) {
    $nama_kategori = $row_nama['nama_kategori'];
}

// Ambil semua produk berdasarkan kategori
$query_products = "
    SELECT pb.*, kp.nama_kategori 
    FROM produk_buah pb
    LEFT JOIN kategori_produk kp ON pb.kategori_id = kp.id
    WHERE pb.kategori_id = ?
    ORDER BY pb.created_at DESC
";
$stmt_products = mysqli_prepare($conn, $query_products);
mysqli_stmt_bind_param($stmt_products, "i", $kategori_id);
mysqli_stmt_execute($stmt_products);
$result_products = mysqli_stmt_get_result($stmt_products);
$products = [];
if ($result_products) {
    while ($row = mysqli_fetch_assoc($result_products)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk <?php echo htmlspecialchars($nama_kategori); ?> - House of Redland</title> 
    
    <link rel="stylesheet" href="style.css"> 
    
    <link rel="stylesheet" href="produk-melon.css"> 

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Nunito+Sans:wght@400&family=Plus+Jakarta+Sans:wght@400;700&family=Poppins:wght@500;700&family=Red+Hat+Display:wght@700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
    <div class="page-blur-overlay"></div>

    <header>
        <nav>
            <ul>
                <li class="nav-item-dropdown">
                    <a href="#">PRODUK</a>
                    <div class="product-dropdown">
                        <?php foreach ($kategori_list as $kategori): ?>
                        <a href="produk.php?kategori=<?php echo $kategori['id']; ?>" class="dropdown-item">
                            <img src="<?php echo htmlspecialchars($kategori['gambar_kategori']); ?>" alt="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>">
                            <h3><?php echo htmlspecialchars($kategori['nama_kategori']); ?></h3>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </li>
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
        
        <section class="product-page-header">
            <h2>PRODUK KAMI</h2>
        </section>

        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $index => $product): ?>
                <section class="product-item-container <?php echo ($index % 2 == 1) ? 'product-item-reversed' : ''; ?>">
                    <div class="product-item-image">
                        <img src="uploads/<?php echo htmlspecialchars($product['gambar_buah']); ?>" alt="<?php echo htmlspecialchars($product['judul_buah']); ?>">
                    </div>
                    <div class="product-item-text">
                        <h3><?php echo htmlspecialchars($product['judul_buah']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($product['penjelasan_buah'])); ?></p>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php else: ?>
            <section class="product-item-container">
                <div class="product-item-text" style="flex-basis: 100%; text-align: center;">
                    <h3>Belum Ada Produk</h3>
                    <p>Produk untuk kategori <?php echo htmlspecialchars($nama_kategori); ?> belum tersedia. Silakan cek kembali nanti.</p>
                </div>
            </section>
        <?php endif; ?>

    </div>
    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-column">
                <h4>Kontak Kami</h4>
                <p><i class="fas fa-envelope"></i> houseofredland@gmail.com</p>
                <p><i class="fas fa-phone"></i> +62 858 4903 4541</p>
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