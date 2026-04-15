<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
require "../config/auth_check.php";

$search = $_GET['search'] ?? '';
$search = trim($search);
$detail_id = $_GET['detail'] ?? null;

$where = "WHERE p.status IN ('diajukan','diproses')";

if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where .= " AND (
        u.nama LIKE '%$safe_search%' OR
        p.judul LIKE '%$safe_search%' OR
        p.deskripsi LIKE '%$safe_search%' OR
        s.nama_prasarana LIKE '%$safe_search%' OR
        p.id LIKE '%$safe_search%'
    )";
}

$data_pengaduan = mysqli_query($conn, "
    SELECT p.*, u.nama, s.nama_prasarana, k.nama_kategori
    FROM pengaduan p
    JOIN users u ON p.user_id = u.id
    JOIN prasarana s ON p.prasarana_id = s.id
    JOIN kategori k ON p.kategori_id = k.id
    $where
    ORDER BY p.tanggal_pengaduan DESC
");

$total_pengaduan = mysqli_num_rows($data_pengaduan);
ob_start()
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Pengaduan</h1>
    <p class="text-sm text-slate-400 mt-1">Kelola semua pengaduan yang masuk</p>
</div>

<!-- Stat Card -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <p class="text-xs font-semibold text-emerald-500 mb-1">Total Pengaduan</p>
        <p class="text-2xl font-bold text-slate-800"><?= $total_pengaduan ?></p>
    </div>
</div>

<!-- Search -->
<form method="GET" class="mb-5">
    <div class="relative max-w-md">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
            placeholder="Cari pengaduan, pelapor, atau ID..."
            class="input input-bordered w-full pl-4 rounded-xl bg-white border-slate-200 text-sm focus:border-emerald-400 focus:outline-none" />
    </div>
</form>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table w-full text-sm">
            <thead>
                <tr class="text-emerald-600 border-b border-slate-100">
                    <th class="font-semibold bg-transparent">ID</th>
                    <th class="font-semibold bg-transparent">Judul</th>
                    <th class="font-semibold bg-transparent">Pelapor</th>
                    <th class="font-semibold bg-transparent">Prasarana</th>
                    <th class="font-semibold bg-transparent">Kategori</th>
                    <th class="font-semibold bg-transparent">Tanggal</th>
                    <th class="font-semibold bg-transparent">Status</th>
                    <th class="font-semibold bg-transparent">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $has_data = false;
                while ($pengaduan = mysqli_fetch_assoc($data_pengaduan)) {
                    $has_data = true;

                    // Format ID
                    $id_display = '' . str_pad($pengaduan['id'], 3, '0', STR_PAD_LEFT);

                    // Status badge styling
                    $status = strtolower($pengaduan['status']);
                    $status_badge = match ($status) {
                        'diajukan' => 'bg-blue-50 text-blue-600 border-blue-100',
                        'diproses' => 'bg-amber-50 text-amber-600 border-amber-100',
                        'selesai'  => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                        'ditolak'  => 'bg-red-50 text-red-600 border-red-100',
                        default    => 'bg-slate-50 text-slate-500 border-slate-100'
                    };

                    // Format tanggal
                    $tanggal = date('d M Y', strtotime($pengaduan['tanggal_pengaduan']));

                    $format_tanggal = new IntlDateFormatter(
                        'id_ID',
                        IntlDateFormatter::MEDIUM,
                        IntlDateFormatter::NONE,
                        'Asia/Jakarta'
                    );
                    $tanggal = $format_tanggal->format(new DateTime($pengaduan['tanggal_pengaduan']));
                ?>
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="font-semibold text-slate-700"><?= $id_display ?></td>
                    <td>
                        <div class="flex flex-col">
                            <span
                                class="font-semibold text-slate-800"><?= htmlspecialchars($pengaduan['judul']) ?></span>
                        </div>
                    </td>
                    <td class="text-slate-600">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <?= htmlspecialchars($pengaduan['nama']) ?>
                        </span>
                    </td>
                    <td class="text-slate-600">
                        <span class="flex items-center gap-1.5">
                            <img src="../assets/icons/loc.svg" class="w-4 h-4">
                            <?= htmlspecialchars($pengaduan['nama_prasarana']) ?>
                        </span>
                    </td>
                    <td class="text-slate-600"><?= htmlspecialchars($pengaduan['nama_kategori']) ?></td>
                    <td class="text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <img src="../assets/icons/kalender.svg" class="w-4 h-4">
                            <?= $tanggal ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-sm border <?= $status_badge ?>">
                            <?= ucfirst($pengaduan['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="?detail=<?= $pengaduan['id'] ?>"
                            class="btn btn-xs bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 rounded-lg gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Detail
                        </a>
                    </td>
                </tr>
                <?php } ?>

                <?php if (!$has_data): ?>
                <tr>
                    <td colspan="7" class="text-center py-8 text-slate-400">
                        Data pengaduan belum tersedia
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$modalAction = "/proses/proses_pengaduan.php";
include "../layouts/main.php";
if ($detail_id) {
    include "../components/modal/modal_pengaduan.php";
}
?>
</body>

</html>