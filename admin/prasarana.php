<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
require "../config/auth_check.php";

$detail = $_GET['detail'] ?? null;
$delete = $_GET['delete'] ?? null;

$search = $_GET['search'] ?? '';
$search = trim($search);
$safe_search = mysqli_real_escape_string($conn, $search);

if ($search !== '') {
    $data_prasarana = mysqli_query($conn, "
        SELECT * FROM prasarana
        WHERE nama_prasarana LIKE '%$safe_search%'
        OR lokasi LIKE '%$safe_search%'
        ORDER BY nama_prasarana ASC
    ");
} else {
    $data_prasarana = mysqli_query($conn, "
        SELECT * FROM prasarana
        ORDER BY nama_prasarana ASC
    ");
}

// Hitung total prasarana
$total_prasarana = mysqli_num_rows($data_prasarana);

ob_start()
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Prasarana</h1>
    <p class="text-sm text-slate-400 mt-1">Kelola data prasarana sekolah</p>
</div>

<!-- Stat Card -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <p class="text-xs font-semibold text-emerald-500 mb-1">Total Prasarana</p>
        <p class="text-2xl font-bold text-slate-800"><?= $total_prasarana ?></p>
    </div>
</div>

<!-- Search + Tambah -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <form method="GET" class="relative max-w-md w-full">
        <input
            type="text"
            name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Cari prasarana atau lokasi..."
            class="input input-bordered w-full pl-4 rounded-xl bg-white border-slate-200 text-sm focus:border-emerald-400 focus:outline-none" />
    </form>
    <a href="?detail=new" class="btn bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded-lg flex items-center gap-1.5 shadow-sm shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Prasarana
    </a>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table w-full text-sm">
            <thead>
                <tr class="text-emerald-600 border-b border-slate-100">
                    <th class="font-semibold bg-transparent">ID</th>
                    <th class="font-semibold bg-transparent">Nama Prasarana</th>
                    <th class="font-semibold bg-transparent">Lokasi</th>
                    <th class="text-center font-semibold bg-transparent">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $has_data = false;
                if (mysqli_num_rows($data_prasarana) > 0):
                    while ($prasarana = mysqli_fetch_assoc($data_prasarana)):
                        $has_data = true;
                        $id_display = '' . str_pad($prasarana['id'], 3, '0', STR_PAD_LEFT);
                ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="font-semibold text-slate-700"><?= $id_display ?></td>
                            <td class="font-medium text-slate-700"><?= htmlspecialchars($prasarana['nama_prasarana']) ?></td>
                            <td class="text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <img src="../assets/icons/loc.svg" class="w-4 h-4">
                                    <?= htmlspecialchars($prasarana['lokasi']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Edit -->
                                    <a href="?detail=<?= $prasarana['id']; ?>"
                                       class="btn btn-xs bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 rounded-lg gap-1.5 font-medium"
                                       title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>

                                     <!-- Delete -->
                                    <button type="button"
                                            onclick="openDeleteModal(<?= $prasarana['id'] ?>, '<?= htmlspecialchars($prasarana['nama_prasarana'], ENT_QUOTES) ?>')"
                                            class="btn btn-xs bg-white border border-red-200 text-red-500 hover:bg-red-50 rounded-lg gap-1.5 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-slate-400 py-8">
                            Data prasarana belum tersedia
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/main.php";
if ($detail) {
    include "../components/modal/modal_prasarana.php";
}
?>

<!-- Modal Konfirmasi Hapus Prasarana -->
<dialog id="modalHapusPrasarana" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-lg">Hapus Prasarana</h3>
                <p class="text-sm text-base-content/60">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        <p class="text-sm text-base-content/80 mb-1">Apakah kamu yakin ingin menghapus prasarana:</p>
        <p class="font-semibold text-slate-800 mb-4" id="namaPrasaranaHapus"></p>
        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalHapusPrasarana').close()">
                Batal
            </button>
            <form method="POST" action="proses_prasarana.php" class="inline">
                <input type="hidden" name="delete" id="idPrasaranaHapus">
                <button type="submit" class="btn btn-error">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
function openDeleteModal(id, nama) {
    document.getElementById('idPrasaranaHapus').value = id;
    document.getElementById('namaPrasaranaHapus').textContent = nama;
    document.getElementById('modalHapusPrasarana').showModal();
}
</script>

</body>

</html>