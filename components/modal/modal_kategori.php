<?php
if (!defined('APP_RUNNING')) {
    header("Location: ../../index.php");
    exit;
}

$is_edit = false;
$id = '';
$nama_kategori = '';

if ($detail !== 'new') {
    $id_safe = mysqli_real_escape_string($conn, $detail);
    $query = mysqli_query($conn, "SELECT * FROM kategori WHERE id='$id_safe'");
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $is_edit = true;
        
        $id = $data['id'];
        $nama_kategori = $data['nama_kategori'];
    }
}
?>

<dialog id="modalKategori" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">

        <h3 class="font-bold text-lg mb-6">
            <?= $is_edit ? 'Edit Kategori' : 'Tambah Kategori Baru' ?>
        </h3>

        <form method="POST" action="proses_kategori.php" class="flex flex-col gap-4">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <?php endif; ?>

            <!-- Nama Kategori -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium text-slate-700">Nama Kategori <span class="text-red-500">*</span></span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="nama_kategori" 
                        value="<?= htmlspecialchars($nama_kategori) ?>"
                        required 
                        placeholder="Contoh: Fasilitas Kelas" 
                        class="input input-bordered w-full pl-10 rounded-xl bg-slate-50 border-slate-200 focus:bg-white focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-colors" />
                </div>
            </div>

            <!-- Modal Action -->
            <div class="modal-action mt-6">
                <a href="kategori.php" class="btn btn-ghost">Batal</a>
                <button type="submit" name="save" class="btn bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded-xl px-8">
                    Simpan
                </button>
            </div>
        </form>
    </div>
    
    <a href="kategori.php" class="modal-backdrop">
        <button>close</button>
    </a>
</dialog>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('modalKategori').showModal();
    });
</script>
