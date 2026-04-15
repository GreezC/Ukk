<?php
if (!defined('APP_RUNNING')) {
    header("Location: ../../index.php");
    exit;
}

$id = $_GET['detail'] ?? null;
$prasarana = null;

if ($id && $id !== 'new') {
    $query = mysqli_query($conn, "SELECT * FROM prasarana WHERE id='$id'");
    $prasarana = mysqli_fetch_assoc($query);
}
?>

<dialog id="modalPrasarana" class="modal modal-bottom sm:modal-middle" open>
    <div class="modal-box bg-white rounded-2xl p-0 overflow-hidden max-w-lg shadow-xl border border-slate-100">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between ">
            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2.5">
                <?= $prasarana ? 'Edit Data Prasarana' : 'Tambah Prasarana Baru' ?>
            </h3>
        </div>

        <form method="POST" action="proses_prasarana.php" class="p-6">
            <input type="hidden" name="id" value="<?= $prasarana['id'] ?? '' ?>">

            <div class="space-y-4">
                <!-- NAMA PRASARANA -->
                <div class="form-control">
                    <label class="label pb-1.5">
                        <span class="label-text font-semibold text-slate-700">Nama Prasarana</span>
                    </label>
                    <input type="text" name="nama_prasarana" class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all text-sm bg-white" placeholder="Contoh: Lab Komputer RPL..." value="<?= htmlspecialchars($prasarana['nama_prasarana'] ?? '') ?>" required>
                </div>

                <!-- LOKASI -->
                <div class="form-control">
                    <label class="label pb-1.5">
                        <span class="label-text font-semibold text-slate-700">Lokasi</span>
                    </label>
                    <input type="text" name="lokasi" class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all text-sm bg-white" placeholder="Contoh: Gedung H..." value="<?= htmlspecialchars($prasarana['lokasi'] ?? '') ?>" required>
                </div>
            </div>

            <!-- ACTION -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="prasarana.php" class="btn bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 rounded-xl px-6 font-medium transition-colors">Batal</a>
                <button type="submit" name="save" class="btn bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded-xl px-8 font-medium shadow-sm shadow-emerald-200/50 transition-colors">
                    <?= $prasarana ? 'Simpan Perubahan' : 'Tambah Prasarana' ?>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Modal Backdrop -->
    <a href="prasarana.php" class="modal-backdrop bg-slate-900/20 backdrop-blur-sm">
        <button class="hidden cursor-default">close</button>
    </a>
</dialog>