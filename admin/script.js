// Tunggu hingga seluruh konten halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    
    // --- LOGIKA UNTUK SIDEBAR (Kode Anda sebelumnya) ---
    const toggleBtn = document.getElementById('toggleBtn');
    const sidebar = document.querySelector('.sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            if (sidebar.classList.contains('collapsed')) {
                toggleBtn.innerHTML = '&rarr;'; 
            } else {
                toggleBtn.innerHTML = '&larr;'; 
            }
        });
    }

    // --- LOGIKA BARU UNTUK MODAL FORM ---
    
    // Ambil elemen-elemen modal
    const modalOverlay = document.getElementById('addProductModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Pastikan semua elemen ada sebelum menambahkan listener
    if (modalOverlay && openModalBtn && closeModalBtn) {
        
        // Fungsi untuk MEMBUKA modal
        function openModal() {
            modalOverlay.style.display = 'flex'; // Gunakan flex untuk menengahkan
        }

        // Fungsi untuk MENUTUP modal
        function closeModal() {
            modalOverlay.style.display = 'none';
        }

        // Event listener untuk tombol FAB (+)
        openModalBtn.addEventListener('click', openModal);

        // Event listener untuk tombol Close (X)
        closeModalBtn.addEventListener('click', closeModal);

        // Event listener untuk menutup modal saat klik di luar area modal (di overlay)
        modalOverlay.addEventListener('click', function(event) {
            // Jika target klik adalah overlay itu sendiri, bukan konten di dalamnya
            if (event.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // (Opsional) Menampilkan nama file yang dipilih di input file
    const fileInput = document.getElementById('gambar_buah');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const label = document.querySelector('.custom-file-label');
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
            label.textContent = fileName;
        });
    }
});