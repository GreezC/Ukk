<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
require "../config/auth_check.php";

$user_id = $_SESSION['user_id'];

// Mengambil data pengaduan milik user 
$data_pengaduan = mysqli_query($conn, "
    SELECT p.*, s.nama_prasarana, k.nama_kategori
    FROM pengaduan p
    JOIN prasarana s ON p.prasarana_id = s.id
    JOIN kategori k ON p.kategori_id = k.id
    WHERE p.user_id = '$user_id'
    AND p.status IN ('diajukan', 'diproses')
    ORDER BY CASE 
        WHEN p.status = 'diajukan' THEN 1
        WHEN p.status = 'diproses' THEN 2
        ELSE 3
    END, p.id DESC
");

ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengaduan</title>
    <link rel="stylesheet" href="../assets/css/output.css">
</head>
<body>

<div class="min-h-screen bg-slate-50 flex">
    <main class="flex-1 overflow-y-auto p-5 md:p-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-800">Status Pengaduan</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar pengaduan yang sedang berjalan</p>
            </div>
        </div>

        <!-- Empty State -->
        <?php if (mysqli_num_rows($data_pengaduan) == 0): ?>
            <div class="bg-white rounded-2xl border border-dashed border-slate-300 shadow-sm">
                <div class="flex flex-col items-center justify-center py-16 px-4 gap-3 text-slate-400">
                    <p class="text-sm font-medium">Belum ada pengaduan aktif</p>
                    <a href="dashboard.php" class="btn bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded-lg btn-sm mt-2 shadow-sm">+ Buat Pengaduan</a>
                </div>
            </div>

        <?php else: ?>

            <!-- List Pengaduan -->
            <div class="flex flex-col gap-6">
                <?php while ($pengaduan = mysqli_fetch_assoc($data_pengaduan)):
                    $status = strtolower($pengaduan['status']);
                    // Badge status styles matching the screenshot and previous pages
                    $badge = match($status) {
                        'diajukan' => 'bg-blue-50 text-blue-600',
                        'diproses' => 'bg-amber-50 text-amber-600',
                        default    => 'bg-slate-50 text-slate-600',
                    };
                    $label = match($status) {
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        default    => ucfirst($status),
                    };

                    // Format date to "25 Maret 2026"
                    $fmt = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                    $tanggal = $fmt->format(new DateTime($pengaduan['tanggal_pengaduan']));
                    $id_display = str_pad($pengaduan['id'], 3, '0', STR_PAD_LEFT);
                ?>

                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                        
                        <!-- Card Header -->
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800 tracking-wide"><?= htmlspecialchars($pengaduan['judul']) ?></h3>
                                    <div class="flex flex-wrap items-center gap-4 mt-2.5 text-sm text-slate-500">
                                        <!-- ID -->
                                        <span class="flex items-center gap-1.5 font-medium">
                                            <img src="../assets/icons/id.svg" class="w-4 h-4">
                                            <?= $id_display ?>
                                        </span>
                                        <!-- Tanggal -->
                                        <span class="flex items-center gap-1.5 font-medium">
                                            <img src="../assets/icons/kalender.svg" class="w-4 h-4">
                                            <?= $tanggal ?>
                                        </span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-lg text-xs font-semibold <?= $badge ?>">
                                    <?= $label ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Divider -->
                        <div class="border-t border-slate-100"></div>

                        <!-- Card Body -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Info Kiri -->
                                <div class="space-y-6">
                                    <!-- Prasarana -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-500 mb-1.5">Prasarana</h4>
                                        <p class="text-slate-700 flex items-center gap-1.5 font-medium">
                                            <img src="../assets/icons/loc.svg" class="w-4 h-4">
                                            <?= htmlspecialchars($pengaduan['nama_prasarana']) ?>
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-500 mb-1.5">Kategori</h4>
                                        <p class="text-slate-700 flex items-center gap-1.5 font-medium">
                                            <img src="../assets/icons/kat.svg" class="w-4 h-4">
                                            <?= htmlspecialchars($pengaduan['nama_kategori']) ?>
                                        </p>
                                    </div>
                                    
                                    <!-- Deskripsi -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-500 mb-1.5">Deskripsi</h4>
                                        <p class="text-slate-700 leading-relaxed text-sm">
                                            <?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Bukti Foto -->
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-500 mb-2 flex items-center gap-1.5">
                                        <img src="../assets/icons/img.svg" class="w-4 h-4">
                                        Bukti Foto
                                    </h4>
                                    <?php if ($pengaduan['foto']): ?>
                                        <div class="rounded-xl border border-slate-100 shadow-sm overflow-hidden bg-slate-50/50 flex justify-center p-2">
                                            <img src="../upload/<?= htmlspecialchars($pengaduan['foto']) ?>" alt="Bukti Foto" class="max-w-full h-auto max-h-[400px] object-contain rounded-lg" />
                                        </div>
                                    <?php else: ?>
                                        <div class="w-full h-32 bg-slate-50 rounded-xl border border-dashed border-slate-200 flex items-center justify-center text-slate-400 text-sm">
                                            Tidak ada bukti foto
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Catatan Admin (Jika Diperlukan) -->
                            <?php if (!empty($pengaduan['catatan'])): ?>
                                <div class="mt-6 bg-amber-50/50 border border-amber-200/60 rounded-xl p-4 flex gap-3">
                                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-amber-800 mb-0.5">Catatan Admin</p>
                                        <p class="text-sm text-amber-700/80"><?= nl2br(htmlspecialchars($pengaduan['catatan'])) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Card Footer (Edit Pengaduan) -->
                        <?php if ($status === 'diajukan'): ?>
                            <div class="border-t border-slate-100"></div>
                            <div class="p-5">
                                <a href="?detail=<?= $pengaduan['id'] ?>" class="inline-flex items-center gap-1.5 px-4 py-2 border border-emerald-400 text-emerald-600 bg-emerald-50/30 hover:bg-emerald-50 hover:border-emerald-500 rounded-lg text-sm font-semibold transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Pengaduan
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php endif; ?>

    </main>
</div>

<?php
$content = ob_get_clean();
include "../layouts/main.php";

// Gunakan parameter ?detail= untuk memicu modal
if (isset($_GET['detail'])) {
    include "../components/modal/modal_pengaduan_siswa.php";
}
?>
</body>
</html>