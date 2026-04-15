<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
require "../config/auth_check.php";

$detail = $_GET['detail'] ?? null;
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? 'all';

$where = "WHERE 1=1";

if ($search) {
    $safe = mysqli_real_escape_string($conn, $search);
    $where .= " AND (
        nama LIKE '%$safe%' OR
        nisn LIKE '%$safe%' OR
        no_telp LIKE '%$safe%' OR
        email LIKE '%$safe%'
    )";
}

if ($roleFilter != 'all') {
    $safeRole = mysqli_real_escape_string($conn, $roleFilter);
    $where .= " AND role='$safeRole'";
}

$data_user = mysqli_query($conn, "SELECT * FROM users $where ORDER BY id DESC");

// Hitung stats
$count_all = mysqli_query($conn, "SELECT COUNT(*) as c FROM users");
$total_user = mysqli_fetch_assoc($count_all)['c'];

$count_siswa = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='siswa'");
$total_siswa = mysqli_fetch_assoc($count_siswa)['c'];

$count_admin = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='admin'");
$total_admin = mysqli_fetch_assoc($count_admin)['c'];

ob_start()
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">User</h1>
    <p class="text-sm text-slate-400 mt-1">Kelola data pengguna sistem</p>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <p class="text-xs font-semibold text-emerald-500 mb-1">Total User</p>
        <p class="text-2xl font-bold text-slate-800"><?= $total_user ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <p class="text-xs font-semibold text-slate-400 mb-1">Siswa</p>
        <p class="text-2xl font-bold text-slate-800"><?= $total_siswa ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <p class="text-xs font-semibold text-slate-400 mb-1">Admin</p>
        <p class="text-2xl font-bold text-slate-800"><?= $total_admin ?></p>
    </div>
</div>

<!-- Search + Filter + Tambah -->
<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
    <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3 w-full">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Cari nama atau email..."
                class="input input-bordered w-full pl-4 rounded-xl bg-white border-slate-200 text-sm focus:border-emerald-400 focus:outline-none" />
        </div>

        <!-- Filter Role -->
        <select name="role" onchange="this.form.submit()"
                class="select select-bordered rounded-xl border-slate-200 text-sm bg-white">
            <option value="all" <?= $roleFilter == 'all' ? 'selected' : '' ?>>Semua Role</option>
            <option value="siswa" <?= $roleFilter == 'siswa' ? 'selected' : '' ?>>Siswa</option>
            <option value="admin" <?= $roleFilter == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </form>

    <!-- Tambah User -->
    <a href="?detail=new" class="btn bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded-lg flex items-center gap-1.5 shadow-sm shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah User
    </a>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table w-full text-sm">
            <thead>
                <tr class="text-emerald-600 border-b border-slate-100">
                    <th class="font-semibold bg-transparent">ID</th>
                    <th class="font-semibold bg-transparent">Nama</th>
                    <th class="font-semibold bg-transparent">NISN</th>
                    <th class="font-semibold bg-transparent">No Telp</th>
                    <th class="font-semibold bg-transparent">Email</th>
                    <th class="font-semibold bg-transparent">Role</th>
                    <th class="text-center font-semibold bg-transparent">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $has_data = false;
                if (mysqli_num_rows($data_user) > 0):
                    while ($user = mysqli_fetch_assoc($data_user)):
                        $has_data = true;
                        $id_display = '' . str_pad($user['id'], 3, '0', STR_PAD_LEFT);
                        $is_admin = ($user['role'] === 'admin');

                        // Role badge
                        $role_badge = $is_admin
                            ? 'bg-blue-50 text-blue-600 border-blue-100'
                            : 'bg-emerald-50 text-emerald-600 border-emerald-100';

                        // NISN & No Telp — tampilkan "-" jika kosong, tapi untuk admin tampilkan tanda strip abu
                        $nisn_display = !empty($user['nisn']) ? htmlspecialchars($user['nisn']) : ($is_admin ? '<span class="text-slate-300">—</span>' : '<span class="text-slate-300">—</span>');
                        $telp_display = !empty($user['no_telp']) ? htmlspecialchars($user['no_telp']) : ($is_admin ? '<span class="text-slate-300">—</span>' : '<span class="text-slate-300">—</span>');
                ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="font-semibold text-slate-700"><?= $id_display ?></td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-slate-700"><?= htmlspecialchars($user['nama']) ?></span>
                                </div>
                            </td>
                            <td class="text-slate-500"><?= $nisn_display ?></td>
                            <td class="text-slate-500"><?= $telp_display ?></td>
                            <td class="text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <?= htmlspecialchars($user['email']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-sm border <?= $role_badge ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Edit -->
                                    <a href="?detail=<?= $user['id'] ?>"
                                       class="btn btn-xs bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 rounded-lg gap-1.5 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    <!-- Delete -->
                                    <?php if ($user['role'] != 'admin') : ?>
                                    <button type="button"
                                            onclick="openDeleteUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nama'], ENT_QUOTES) ?>')"
                                            class="btn btn-xs bg-white border border-red-200 text-red-500 hover:bg-red-50 rounded-lg gap-1.5 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-slate-400 py-8">
                            Data user kosong
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
    include "../components/modal/modal_user.php";
}
?>

<!-- Modal Konfirmasi Hapus User -->
<dialog id="modalHapusUser" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-lg">Hapus User</h3>
                <p class="text-sm text-base-content/60">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        <p class="text-sm text-base-content/80 mb-1">Apakah kamu yakin ingin menghapus user:</p>
        <p class="font-semibold text-slate-800 mb-4" id="namaUserHapus"></p>
        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalHapusUser').close()">
                Batal
            </button>
            <form method="POST" action="proses_user.php" class="inline">
                <input type="hidden" name="delete" id="idUserHapus">
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
function openDeleteUserModal(id, nama) {
    document.getElementById('idUserHapus').value = id;
    document.getElementById('namaUserHapus').textContent = nama;
    document.getElementById('modalHapusUser').showModal();
}
</script>

</body>

</html>