<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
require "../config/auth_check.php";


$query = mysqli_query($conn, "
    SELECT p.*, s.nama_prasarana 
    FROM pengaduan p
    JOIN prasarana s ON p.prasarana_id = s.id
    ORDER BY p.tanggal_pengaduan ASC
");

$laporan = [];

while ($data_pengaduan = mysqli_fetch_assoc($query)) {
    $tanggal = new DateTime($data_pengaduan['tanggal_pengaduan']);

    $format = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN,
        'MMMM yyyy'
    );
    $bulan = $format->format($tanggal);

    // Short month name for chart labels
    $formatShort = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN,
        'MMM'
    );
    $bulanShort = $formatShort->format($tanggal);

    if (!isset($laporan[$bulan])) {
        $laporan[$bulan] = [
            'bulan' => $bulan,
            'bulan_short' => $bulanShort,
            'total_pengaduan' => 0,
            'total_prasarana' => [],
            'diajukan' => 0,
            'diproses' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];
    }

    $status = strtolower($data_pengaduan['status'] ?? '');

    $laporan[$bulan]['total_pengaduan']++;
    $laporan[$bulan]['total_prasarana'][$data_pengaduan['prasarana_id']] = true;

    if ($status === 'diajukan') $laporan[$bulan]['diajukan']++;
    if ($status === 'diproses') $laporan[$bulan]['diproses']++;
    if ($status === 'selesai') $laporan[$bulan]['selesai']++;
    if ($status === 'ditolak') $laporan[$bulan]['ditolak']++;
}

// Hitung jumlah prasarana unik dan persen selesai
$laporan_final = [];
foreach ($laporan as $data_bulan) {
    $total_pengaduan = $data_bulan['total_pengaduan'];
    $selesai_count = $data_bulan['selesai'];
    $laporan_final[] = [
        'bulan' => $data_bulan['bulan'],
        'bulan_short' => $data_bulan['bulan_short'],
        'total_pengaduan' => $total_pengaduan,
        'total_prasarana' => count($data_bulan['total_prasarana']),
        'diajukan' => $data_bulan['diajukan'],
        'diproses' => $data_bulan['diproses'],
        'selesai' => $selesai_count,
        'ditolak' => $data_bulan['ditolak'],
        'persen_selesai' => $total_pengaduan > 0 ? round(($selesai_count / $total_pengaduan) * 100) : 0,
    ];
}

// Data bulan ini untuk ringkasan
$format_now = new IntlDateFormatter(
    'id_ID',
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'Asia/Jakarta',
    IntlDateFormatter::GREGORIAN,
    'MMMM yyyy'
);
$bulan_ini = $format_now->format(new DateTime());
$ringkasan_bulan_ini = $laporan[$bulan_ini] ?? [
    'diajukan' => 0, 'diproses' => 0, 'selesai' => 0, 'ditolak' => 0
];

// Statistik keseluruhan
$query2 = mysqli_query($conn, "SELECT * FROM pengaduan");

$total = 0;
$diajukan = 0;
$diproses = 0;
$selesai = 0;
$ditolak = 0;

while ($data_pengaduan = mysqli_fetch_assoc($query2)) {
    $total++;
    switch (strtolower($data_pengaduan['status'])) {
        case 'diajukan': $diajukan++; break;
        case 'diproses': $diproses++; break;
        case 'selesai':  $selesai++;  break;
        case 'ditolak':  $ditolak++;  break;
    }
}

// Daftar bulan untuk dropdown
$daftar_bulan = array_map(fn($lap_item) => $lap_item['bulan'], $laporan_final);

ob_start();
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
    <p class="text-sm text-slate-400 mt-1">Overview sistem pengaduan sarana sekolah</p>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <!-- Total Pengaduan -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow duration-300">
        <div>
            <p class="text-sm text-slate-400 font-medium mb-1">Total Pengaduan</p>
            <p class="text-3xl font-bold text-blue-600"><?= $total ?></p>
        </div>
        <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
    </div>

    <!-- Diproses -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow duration-300">
        <div>
            <p class="text-sm text-slate-400 font-medium mb-1">Diproses</p>
            <p class="text-3xl font-bold text-amber-500"><?= $diproses ?></p>
        </div>
        <div class="w-11 h-11 rounded-full bg-amber-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <!-- Selesai -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow duration-300">
        <div>
            <p class="text-sm text-slate-400 font-medium mb-1">Selesai</p>
            <p class="text-3xl font-bold text-emerald-500"><?= $selesai ?></p>
        </div>
        <div class="w-11 h-11 rounded-full bg-emerald-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <!-- Ditolak -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow duration-300">
        <div>
            <p class="text-sm text-slate-400 font-medium mb-1">Ditolak</p>
            <p class="text-3xl font-bold text-red-500"><?= $ditolak ?></p>
        </div>
        <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

</div>

<!-- Laporan Pengaduan Bulanan -->
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <h2 class="text-lg font-semibold text-slate-800">Laporan Pengaduan Bulanan</h2>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="table table-zebra text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500">
                    <th class="font-semibold">Bulan</th>
                    <th class="text-center font-semibold">Total</th>
                    <th class="text-center font-semibold">Prasarana</th>
                    <th class="text-center font-semibold">Diajukan</th>
                    <th class="text-center font-semibold">Diproses</th>
                    <th class="text-center font-semibold">Selesai</th>
                    <th class="text-center font-semibold">Ditolak</th>
                    <th class="text-center font-semibold">Progress</th>
                    <th class="text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($laporan_final) : ?>
                    <?php foreach ($laporan_final as $data_bulan) : ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="font-medium text-slate-700"><?= $data_bulan['bulan']; ?></td>
                            <td class="text-center font-semibold text-slate-700"><?= $data_bulan['total_pengaduan']; ?></td>
                            <td class="text-center"><span class="badge badge-sm bg-blue-50 text-blue-600 border-blue-100"><?= $data_bulan['total_prasarana']; ?></span></td>
                            <td class="text-center"><span class="badge badge-sm bg-blue-50 text-blue-600 border-blue-100"><?= $data_bulan['diajukan']; ?></span></td>
                            <td class="text-center"><span class="badge badge-sm bg-amber-50 text-amber-600 border-amber-100"><?= $data_bulan['diproses']; ?></span></td>
                            <td class="text-center"><span class="badge badge-sm bg-emerald-50 text-emerald-600 border-emerald-100"><?= $data_bulan['selesai']; ?></span></td>
                            <td class="text-center"><span class="badge badge-sm bg-red-50 text-red-600 border-red-100"><?= $data_bulan['ditolak']; ?></span></td>
                            <td class="text-center">
                                <?php
                                $persen = $data_bulan['persen_selesai'];
                                if ($persen >= 80) { $badgeCls = 'bg-emerald-50 text-emerald-600 border-emerald-100'; }
                                elseif ($persen >= 50) { $badgeCls = 'bg-amber-50 text-amber-600 border-amber-100'; }
                                else { $badgeCls = 'bg-red-50 text-red-600 border-red-100'; }
                                ?>
                                <span class="badge badge-sm <?= $badgeCls ?>"><?= $persen ?>%</span>
                            </td>
                            <td class="text-center">
                                <a href="../dompdf/export_pdf.php?bulan=<?= urlencode($data_bulan['bulan']); ?>"
                                   class="btn btn-xs bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded-lg">
                                    Export
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="text-center text-slate-400 py-8">Data laporan belum tersedia</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php
$content = ob_get_clean();
include "../layouts/main.php";
?>