<?php
define('APP_RUNNING', true);

include "../config/koneksi.php";
require "../config/auth_check.php";

// ==========================
// AMBIL DATA USER
// ==========================
$user_id = $_SESSION['user_id'];

$user_query = mysqli_query($conn, "SELECT nama FROM users WHERE id='$user_id'");
$data_user  = mysqli_fetch_assoc($user_query);

// ==========================
// AMBIL DATA RIWAYAT
// ==========================
$data_riwayat = mysqli_query($conn, "
    SELECT p.*, s.nama_prasarana
    FROM pengaduan p
    JOIN prasarana s ON p.prasarana_id = s.id
    WHERE p.user_id='$user_id' 
    AND p.status IN ('selesai', 'ditolak')
    ORDER BY p.id DESC
");


// ==========================
// VIEW START
// ==========================
ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengaduan</title>
    <link rel="stylesheet" href="../assets/css/output.css">
</head>
<body>

<div class="min-h-screen bg-slate-50 flex">

    <main class="flex-1 overflow-y-auto p-5 md:p-8">

        <!-- ===== HEADER ===== -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">
                Riwayat Pengaduan
            </h1>
            <p class="text-sm text-slate-500 mt-1">Lihat semua pengaduan yang telah selesai atau ditolak</p>
        </div>

        <!-- ===== EMPTY STATE ===== -->
        <?php if (mysqli_num_rows($data_riwayat) == 0) : ?>
            <div class="bg-white rounded-2xl border border-dashed border-slate-300 shadow-sm py-16 flex flex-col items-center justify-center text-slate-400">
                <p class="text-sm font-medium">Belum ada pengaduan selesai</p>
            </div>
        <?php else : ?>

            <!-- ===== GRID CARDS ===== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <?php while ($riwayat = mysqli_fetch_assoc($data_riwayat)) : ?>  

                    <?php
                    $isSelesai = (strtolower($riwayat['status']) === 'selesai');
                    
                    // Colors
                    $statusBg = $isSelesai ? 'bg-emerald-50' : 'bg-red-50';
                    $statusText = $isSelesai ? 'text-emerald-700' : 'text-red-700';
                    $borderColor = $isSelesai ? 'border-emerald-100' : 'border-red-100';
                    $badgeClass = $isSelesai ? 'bg-white text-emerald-600 shadow-sm' : 'bg-red-100 text-red-600';
                    $iconCircleClass = $isSelesai ? 'bg-emerald-100 text-emerald-500' : 'bg-red-100 text-red-500';

                    // Formats
                    $aduId = str_pad($riwayat['id'], 3, '0', STR_PAD_LEFT);
                    $tglFormat = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                    $tanggal = $tglFormat->format(new DateTime($riwayat['tanggal_pengaduan']));
                    ?>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col relative group transition-all hover:shadow-md">
                        
                        <!-- Top Half: Image or Placeholder -->
                        <div class="relative h-48 w-full shrink-0 <?= empty($riwayat['foto']) ? 'bg-emerald-50/50 flex items-center justify-center border-b border-emerald-50' : 'bg-slate-100' ?>">
                            <?php if (!empty($riwayat['foto'])) : ?>
                                <img src="../upload/<?= htmlspecialchars($riwayat['foto']) ?>"
                                     alt="Bukti Pengaduan"
                                     class="w-full h-full object-cover" />
                            <?php else : ?>
                                <!-- Placeholder icon (like image example) -->
                                <svg class="w-16 h-16 text-emerald-400 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            <?php endif; ?>

                            <!-- Badge Status Absolute -->
                            <div class="absolute top-4 right-4 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider <?= $badgeClass ?>">
                                <?= htmlspecialchars($riwayat['status']) ?>
                            </div>
                        </div>

                        <!-- Content Half -->
                        <div class="p-6 flex flex-col flex-1">
                            
                            <!-- Header Info -->
                            <div class="flex items-start gap-4">
                                <!-- Status Icon -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 <?= $iconCircleClass ?>">
                                    <?php if ($isSelesai): ?>
                                        <img src="../assets/icons/check.svg" class="w-5 h-5">
                                    <?php else: ?>
                                        <img src="../assets/icons/cross.svg" class="w-5 h-5">
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Judul & ID -->
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800 leading-tight">
                                        <?= htmlspecialchars($riwayat['judul']) ?>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1 font-medium tracking-wide flex items-center gap-1.5">
                                        <?= $aduId ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Location & Date -->
                            <div class="mt-5 space-y-2 text-sm text-slate-500">
                                <div class="flex items-center gap-2">
                                    <img src="../assets/icons/loc.svg" class="w-4 h-4">
                                    <?= htmlspecialchars($riwayat['nama_prasarana']) ?>
                                </div>
                                <div class="flex items-center gap-2">
                                    <img src="../assets/icons/kalender.svg" class="w-4 h-4">
                                    <?= $tanggal ?>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-4 text-[13px] text-slate-600 leading-relaxed line-clamp-2">
                                <?= htmlspecialchars($riwayat['deskripsi']) ?>
                            </div>
                            
                            <!-- Spacer pushes Catatan to bottom -->
                            <div class="flex-1"></div>
                            
                            <!-- Catatan Admin Box -->
                            <?php if (!empty($riwayat['catatan'])) : ?>
                                <div class="mt-5 p-3.5 rounded-xl text-sm border <?= $statusBg ?> <?= $statusText ?> <?= $borderColor ?>">
                                    <span class="font-bold">Catatan:</span> <?= htmlspecialchars($riwayat['catatan']) ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </main>
</div>

<?php
$content = ob_get_clean();
include "../layouts/main.php";
?>
</body>
</html>