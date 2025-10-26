// Tunggu hingga seluruh konten halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    
    // Ambil elemen tombol dan sidebar
    const toggleBtn = document.getElementById('toggleBtn');
    const sidebar = document.querySelector('.sidebar');

    // Tambahkan event listener 'click' pada tombol
    toggleBtn.addEventListener('click', function() {
        
        // Toggle class 'collapsed' pada sidebar
        sidebar.classList.toggle('collapsed');

        // Ganti ikon/teks tombol berdasarkan status sidebar
        if (sidebar.classList.contains('collapsed')) {
            // Jika tertutup, tampilkan panah kanan (buka)
            toggleBtn.innerHTML = '&rarr;'; 
        } else {
            // Jika terbuka, tampilkan panah kiri (tutup)
            toggleBtn.innerHTML = '&larr;'; 
        }
    });
});