<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
require "../config/auth_check.php";

$user_id = $_SESSION['user_id'];
$user = mysqli_query($conn, "SELECT nama FROM users WHERE id='$user_id'");
$data_user = mysqli_fetch_assoc($user);

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$search = mysqli_real_escape_string($conn, $search);


// HITUNG TOTAL DATA 
if ($search) {
    $count_query = mysqli_query($conn, "
        SELECT COUNT(*) as total 
        FROM prasarana 
        WHERE is_active = 1
        AND (nama_prasarana LIKE '%$search%' 
             OR lokasi LIKE '%$search%')
    ");
} else {
    $count_query = mysqli_query($conn, "
        SELECT COUNT(*) as total 
        FROM prasarana 
        WHERE is_active = 1
    ");
}

$count = mysqli_fetch_assoc($count_query);
$total_data = $count['total'];
$total_page = max(ceil($total_data / $limit), 1);

if ($page > $total_page) {
    $page = 1;
}

$start = ($page - 1) * $limit;

// AMBIL DATA prasarana
if ($search) {
    $data_prasarana = mysqli_query($conn, "
        SELECT * FROM prasarana
        WHERE is_active = 1
        AND (nama_prasarana LIKE '%$search%' 
             OR lokasi LIKE '%$search%')
        ORDER BY nama_prasarana ASC
        LIMIT $start, $limit
    ");
} else {
    $data_prasarana = mysqli_query($conn, "
        SELECT * FROM prasarana
        WHERE is_active = 1
        ORDER BY nama_prasarana ASC
        LIMIT $start, $limit
    ");
}

ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="../assets/css/output.css">
</head>
<body>

<div class="min-h-screen bg-slate-50 flex">
    <main class="flex-1 overflow-y-auto p-5 md:p-8">

        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Prasarana Sekolah</h1>
            <p class="text-sm text-slate-500 mt-1">Pilih prasarana untuk membuat pengaduan</p>
        </div>

        <!-- Search Bar -->
        <form method="GET" class="mb-6">
            <div class="relative max-w-md w-full">
                <img src="../assets/icons/search.svg" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2">
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Cari prasarana atau lokasi..."
                    class="input border-none w-full pl-4 rounded-xl bg-slate-100/80 text-sm focus:bg-white focus:ring-1 focus:ring-emerald-400 transition-all font-medium text-slate-600 placeholder:text-slate-400" />
            </div>
        </form>

        <!-- Grid Prasarana -->
        <?php if ($total_data == 0): ?>
            <div class="bg-white rounded-2xl border border-dashed border-slate-300 shadow-sm py-16 flex flex-col items-center justify-center text-slate-400">
                <img src="../assets/icons/prasarana.svg" class="w-12 h-12" style="filter: invert(48%) sepia(79%) saturate(247%) hue-rotate(86deg);">
                <p class="text-sm font-medium">Prasarana tidak ditemukan</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php while ($prasarana = mysqli_fetch_assoc($data_prasarana)): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col">
                        
                        <!-- Card Content -->
                        <div class="flex-1 space-y-3">
                            <!-- Judul & Kategori (Kategori dihilangkan sesuai request) -->
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-slate-800 text-[15px]"><?= htmlspecialchars($prasarana['nama_prasarana']) ?></h3>
                                <!-- Tempat untuk kategori jika mau ditambah badge -->
                            </div>
                            
                            <!-- Lokasi -->
                            <div class="flex items-center gap-1.5 text-slate-500 text-sm pb-2">
                                <img src="../assets/icons/loc.svg" class="w-4 h-4">
                                <span class="truncate"><?= htmlspecialchars($prasarana['lokasi']) ?></span>
                            </div>
                        </div>

                        <!-- Button Action -->
                        <a href="?prasarana=<?= $prasarana['id'] ?>" class="btn w-full bg-emerald-500 hover:bg-emerald-600 border-none text-white rounded-xl shadow-sm mt-4 gap-1.5 font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Buat Pengaduan
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_page > 1): ?>
                <div class="flex justify-center mt-8">
                    <div class="join shadow-sm rounded-xl bg-white border border-slate-200">
                        <!-- Prev -->
                        <a href="?page=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>" class="join-item btn bg-white hover:bg-slate-50 text-slate-600 border-none <?= ($page == 1) ? 'btn-disabled opacity-50' : '' ?>">
                            <img src="../assets/icons/map.svg" class="w-4 h-4">
                            <span class="ml-1 sr-only sm:not-sr-only">Previous</span>
                        </a>

                        <!-- Numbers -->
                        <?php for ($i = 1; $i <= $total_page; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="join-item btn border-none <?= ($page == $i) ? 'bg-emerald-50 text-emerald-600 font-bold hover:bg-emerald-100' : 'bg-white hover:bg-slate-50 text-slate-600' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Next -->
                        <a href="?page=<?= min($total_page, $page + 1) ?>&search=<?= urlencode($search) ?>" class="join-item btn bg-white hover:bg-slate-50 text-slate-600 border-none <?= ($page == $total_page) ? 'btn-disabled opacity-50' : '' ?>">
                            <span class="mr-1 sr-only sm:not-sr-only">Next</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Peta Prasarana (Tengah/Bawah) -->
        <div class="mt-12 mb-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col max-w-4xl mx-auto">
            <div class="p-4 border-b border-slate-100 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-[15px] leading-tight">Denah Lokasi Sekolah</h3>
                    <p class="text-xs text-slate-500">Klik gambar untuk memperbesar secara penuh</p>
                </div>
            </div>
            
            <div class="p-4 sm:p-6 bg-slate-50 flex justify-center">
                <!-- Thumbnail Peta (Lebih Lebar) -->
                <button type="button" class="relative group focus:outline-none w-full rounded-xl overflow-hidden border border-slate-200/60 shadow-sm bg-white" onclick="if(typeof zoomPeta === 'function') zoomPeta('reset'); document.getElementById('modalZoomMap').showModal()">
                    <img src="../assets/icons/Map.png" alt="Denah Lokasi" class="w-full h-auto object-cover transition-transform duration-500 group-hover:scale-[1.02] cursor-zoom-in" />
                    
                    <!-- Overlay saat di-hover -->
                    <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity cursor-zoom-in flex items-center justify-center">
                        <div class="bg-white/95 backdrop-blur-md text-emerald-700 px-4 py-2 rounded-xl shadow-md border border-slate-100 flex items-center gap-2 text-sm font-semibold transform scale-95 group-hover:scale-100 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            Klik untuk Perbesar Penuh
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Modal Peta Full/Zoom -->
        <dialog id="modalZoomMap" class="modal modal-bottom sm:modal-middle">
            <!-- Modal Box yang tingginya dibatasi max 90vh, dan overflow-hidden agar seluruh box tidak ikut ter-scroll -->
            <div class="modal-box w-11/12 max-w-6xl max-h-[90vh] bg-white p-0 flex flex-col shadow-2xl rounded-2xl overflow-hidden">
                
                <!-- Bagian Header (Tombol Tutup Modal) - Tetap (Fixed) -->
                <div class="flex justify-between items-center p-4 border-b border-slate-100 bg-white z-20 shrink-0">
                    <h3 class="font-bold text-lg text-slate-800 pl-2">Informasi Denah</h3>
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost text-slate-400 hover:bg-red-50 hover:text-red-500 focus:outline-none transition-colors border-none shadow-none" title="Tutup Modal">✕</button>
                    </form>
                </div>
                
                <!-- Area Konten Utama (Mengisi Sisa Ruang) -->
                <div class="relative w-full flex-1 flex flex-col overflow-hidden bg-slate-100">
                    
                    <!-- Area Scroll Gambar - Di sini Scroll Bar muncul -->
                    <div id="scrollZoomContainer" class="w-full h-full flex overflow-auto relative">
                        <img id="petaZoomDisplay" src="../assets/icons/Map.png" alt="Denah Lokasi Full" class="transition-all duration-300 origin-top-left object-contain" style="min-width: 1200px; width: 1200px; max-width: none; margin: auto;" />
                    </div>

                    <!-- Floating Zoom Controls - Tetap Mengambang di Pojok Kanan Bawah -->
                    <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-md p-1.5 rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.15)] border border-slate-200 flex gap-1 z-10">
                        <button type="button" onclick="zoomPeta('out')" class="btn btn-sm btn-square bg-slate-50 hover:bg-slate-200 border-none text-slate-700" title="Perkecil">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <button type="button" onclick="zoomPeta('reset')" class="btn btn-sm px-3 bg-slate-50 hover:bg-slate-200 border-none text-slate-700 font-semibold" title="Reset Ukuran">
                            Reset
                        </button>
                        <button type="button" onclick="zoomPeta('in')" class="btn btn-sm btn-square bg-emerald-100 hover:bg-emerald-200 border-none text-emerald-700" title="Perbesar">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                </div>
            </div>
            
            <form method="dialog" class="modal-backdrop bg-slate-900/70 backdrop-blur-sm transition-all">
                <button class="cursor-zoom-out">close</button>
            </form>
        </dialog>

    </main>
</div>

<?php
$content = ob_get_clean();
include "../layouts/main.php";
// Gunakan parameter ?prasarana= untuk memicu modal dari file asli jika dimodifikasi
if (isset($_GET['prasarana'])) {
    include "../components/modal/modal_pengaduan_siswa.php";
}
?>
<script>
    let petaDefaultWidth = window.innerWidth < 768 ? 1000 : 1200;
    let currentPetaWidth = petaDefaultWidth;
    let maxPetaWidth = 3600; // Maksimalkan mentok sampe 3x lipat
    let minPetaWidth = 600;

    function zoomPeta(action) {
        let img = document.getElementById('petaZoomDisplay');
        if (!img) return;

        let zoomStep = 400; // setiap klik nambah 400px

        if (action === 'in' && currentPetaWidth < maxPetaWidth) {
            currentPetaWidth += zoomStep;
        } else if (action === 'out' && currentPetaWidth > minPetaWidth) {
            currentPetaWidth -= zoomStep;
        } else if (action === 'reset') {
            currentPetaWidth = petaDefaultWidth;
        }

        // Terapkan ke style inline
        img.style.minWidth = currentPetaWidth + 'px';
        img.style.width = currentPetaWidth + 'px';
    }
</script>

</body>
</html>