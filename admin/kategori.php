<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
require "../config/auth_check.php";

$detail = $_GET['detail'] ?? null;
$search = $_GET['search'] ?? '';

$where = "WHERE 1=1";

if ($search) {
    $safe = mysqli_real_escape_string($conn, $search);
    $where .= " AND (nama_kategori LIKE '%$safe%')";
}

$data_kategori = mysqli_query($conn, "SELECT * FROM kategori $where ORDER BY id DESC");

// Hitung stats
$count_all = mysqli_query($conn, "SELECT COUNT(*) as c FROM kategori");
$total_kategori = mysqli_fetch_assoc($count_all)['c'];

ob_start()
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Kategori</h1>
    <p class="text-sm text-slate-400 mt-1">Kelola data kategori pengaduan</p>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-100 p-5 col-span-1">
        <p class="text-xs font-semibold text-emerald-500 mb-1">Total Kategori</p>
        <p class="text-2xl font-bold text-slate-800"><?= $total_kategori ?></p>
    </div>
</div>

<!-- Search + Tambah -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto flex-1">
        <!-- Search -->
        <div class="relative w-full max-w-md">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Cari kategori..."
                class="input input-bordered w-full pl-4 rounded-xl bg-white border-slate-200 text-sm focus:border-emerald-400 focus:outline-none" />
        </div>
    </form>

    <!-- Tambah Kategori -->
    <a href="?detail=new" class="btn bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded-lg flex items-center gap-1.5 shadow-sm shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kategori
    </a>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table w-full text-sm">
            <thead>
                <tr class="text-emerald-600 border-b border-slate-100">
                    <th class="font-semibold bg-transparent w-24">ID</th>
                    <th class="font-semibold bg-transparent">Nama Kategori</th>
                    <th class="text-center font-semibold bg-transparent w-48">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($data_kategori && mysqli_num_rows($data_kategori) > 0):
                    while ($kategori = mysqli_fetch_assoc($data_kategori)):
                        $id_display = '' . str_pad($kategori['id'], 3, '0', STR_PAD_LEFT);
                ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="font-semibold text-slate-700"><?= $id_display ?></td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                        <img src="../assets/icons/kategori.svg" class="w-4 h-4">
                                    </div>
                                    <span class="font-medium text-slate-700"><?= htmlspecialchars($kategori['nama_kategori']) ?></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Edit -->
                                    <a href="?detail=<?= $kategori['id'] ?>"
                                       class="btn btn-xs bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 rounded-lg gap-1.5 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    <!-- Delete -->
                                    <button type="button"
                                            onclick="openDeleteKategoriModal(<?= $kategori['id'] ?>, '<?= htmlspecialchars($kategori['nama_kategori'], ENT_QUOTES) ?>')"
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
                        <td colspan="3" class="text-center text-slate-400 py-8">
                            Data kategori kosong
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
    include "../components/modal/modal_kategori.php";
}
?>

<!-- Modal Konfirmasi Hapus Kategori -->
<dialog id="modalHapusKategori" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-lg">Hapus Kategori</h3>
                <p class="text-sm text-base-content/60">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        <p class="text-sm text-base-content/80 mb-1">Apakah kamu yakin ingin menghapus kategori:</p>
        <p class="font-semibold text-slate-800 mb-4" id="namaKategoriHapus"></p>
        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalHapusKategori').close()">
                Batal
            </button>
            <form method="POST" action="proses_kategori.php" class="inline">
                <input type="hidden" name="delete" id="idKategoriHapus">
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
function openDeleteKategoriModal(id, nama) {
    document.getElementById('idKategoriHapus').value = id;
    document.getElementById('namaKategoriHapus').textContent = nama;
    document.getElementById('modalHapusKategori').showModal();
}
</script>

</body>

</html>
