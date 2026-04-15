<?php
if (!defined('APP_RUNNING')) {
    header("Location: ../../index.php");
    exit;
}

$id = $_GET['detail'] ?? null;
$user = null;

if ($id && $id !== 'new') {
    $query = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
    $user = mysqli_fetch_assoc($query);
}
?>

<dialog class="modal modal-bottom sm:modal-middle" open>
    <div class="modal-box bg-white rounded-2xl p-0 overflow-hidden max-w-lg shadow-xl border border-slate-100">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between ">
            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2.5">
            
                <?= $user ? 'Edit Data User' : 'Tambah User Baru' ?>
            </h3>
        </div>

        <!-- Form -->
        <form method="POST" action="proses_user.php" class="p-6">
            <input type="hidden" name="id" value="<?= $user['id'] ?? '' ?>">
            
            <?php if (!$user): ?>
                <input type="hidden" name="role" value="siswa">
            <?php else: ?>
                <input type="hidden" name="role" value="<?= $user['role'] ?>">
            <?php endif; ?>

            <div class="space-y-4">
                <!-- NAMA -->
                <div class="form-control">
                    <label class="label pb-1.5">
                        <span class="label-text font-semibold text-slate-700">Nama Lengkap</span>
                    </label>
                    <input type="text" name="nama" class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all text-sm bg-white" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" placeholder="Masukkan nama lengkap..." required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- EMAIL -->
                    <div class="form-control">
                        <label class="label pb-1.5">
                            <span class="label-text font-semibold text-slate-700">Email</span>
                        </label>
                        <input type="email" name="email" class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all text-sm bg-white" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="contoh@email.com" required>
                    </div>

                    <!-- PASSWORD -->
                    <div class="form-control">
                        <label class="label pb-1.5">
                            <span class="label-text font-semibold text-slate-700 items-baseline flex gap-1">
                                Password
                                <?php if($user): ?>
                                    <span class="text-[11px] text-slate-400 font-normal">(Kosongkan jika tetap)</span>
                                <?php endif; ?>
                            </span>
                        </label>
                        <input type="password" name="password" class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all text-sm bg-white" <?= $user ? '' : 'required' ?> placeholder="<?= $user ? 'Ketik password baru...' : 'Buat password...' ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NISN -->
                    <div class="form-control">
                        <label class="label pb-1.5">
                            <span class="label-text font-semibold text-slate-700">NISN</span>
                        </label>
                        <input type="text" name="nisn" class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all text-sm bg-white" value="<?= htmlspecialchars($user['nisn'] ?? '') ?>" placeholder="Nomor Induk Siswa Nasional...">
                    </div>

                    <!-- NO TELP -->
                    <div class="form-control">
                        <label class="label pb-1.5">
                            <span class="label-text font-semibold text-slate-700">No Telepon</span>
                        </label>
                        <input type="text" name="no_telp" class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all text-sm bg-white" value="<?= htmlspecialchars($user['no_telp'] ?? '') ?>" placeholder="08xx xxxx xxxx">
                    </div>
                </div>

                <!-- ROLE (INFO ONLY) -->
                <div class="form-control">
                    <label class="label pb-1.5">
                        <span class="label-text font-semibold text-slate-700">Role Sistem</span>
                    </label>
                    <input type="text" class="input input-bordered w-full rounded-xl bg-slate-50 border-slate-200 text-slate-500 cursor-not-allowed text-sm font-medium" value="<?= $user ? ucfirst($user['role']) : 'User (Siswa)' ?>" disabled>
                    <label class="label pt-1.5 h-auto">
                        <span class="label-text-alt text-slate-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Role <?= $user ? 'tidak dapat diubah setelah dibuat' : 'otomatis diatur sebagai User/Siswa' ?>
                        </span>
                    </label>
                </div>
            </div>

            <!-- ACTION -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="user.php" class="btn bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 rounded-xl px-6 font-medium transition-colors">Batal</a>
                <button type="submit" name="save" class="btn bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded-xl px-8 font-medium shadow-sm shadow-emerald-200/50 transition-colors">
                    <?= $user ? 'Simpan Perubahan' : 'Tambah User' ?>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Modal Backdrop -->
    <a href="user.php" class="modal-backdrop bg-slate-900/20 backdrop-blur-sm">
        <button class="hidden cursor-default">close</button>
    </a>
</dialog>