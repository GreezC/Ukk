<?php
if (!defined('APP_RUNNING')) {
    $prev = $_SERVER['HTTP_REFERER'] ?? '../../index.php';
    header("Location: $prev");
    exit;
}

$id = $_GET['detail'] ?? null;
$prasarana_get = $_GET['prasarana'] ?? null;

$pengaduan = null;
$nama_prasarana = '-';
$prasarana_id = '';

if ($id) {
    // MODE EDIT
    $query = mysqli_query($conn, "
        SELECT p.*, s.nama_prasarana
        FROM pengaduan p
        JOIN prasarana s ON p.prasarana_id = s.id
        WHERE p.id='$id'
        AND p.user_id='" . $_SESSION['user_id'] . "'
        AND p.status='diajukan'
    ");

    $pengaduan = mysqli_fetch_assoc($query);

    if ($pengaduan) {
        $nama_prasarana = $pengaduan['nama_prasarana'];
        $prasarana_id = $pengaduan['prasarana_id'];
    }
} elseif ($prasarana_get) {
    // MODE TAMBAH
    $getPrasarana = mysqli_query($conn, "
        SELECT * FROM prasarana WHERE id='$prasarana_get'
    ");
    $dataPrasarana = mysqli_fetch_assoc($getPrasarana);

    if ($dataPrasarana) {
        $nama_prasarana = $dataPrasarana['nama_prasarana'];
        $prasarana_id = $dataPrasarana['id'];
    }
}
?>

<dialog id="modalForm" class="modal modal-bottom sm:modal-middle"
    <?= ($pengaduan || $prasarana_get) ? 'open' : '' ?>>

    <div class="modal-box max-w-lg">

        <!-- TITLE -->
        <h3 class="font-bold text-lg mb-1">
            <?= $pengaduan ? 'Edit Pengaduan' : 'Pengaduan Prasarana' ?>
        </h3>

        <p class="text-sm text-base-content/60 mb-4">
            Isi form di bawah untuk melaporkan kerusakan prasarana
        </p>

        <!-- INFO SARANA -->
        <div class="mb-4">
            <span class="font-medium">Prasarana dipilih:</span>
            <span class="ml-1 font-bold">
                <?= htmlspecialchars($nama_prasarana) ?>
            </span>
        </div>


        <form method="POST"
            enctype="multipart/form-data"
            action="/pengaduan/user/proses_pengaduan.php">

            <input type="hidden" name="id" value="<?= $pengaduan['id'] ?? '' ?>">
            <input type="hidden" name="prasarana_id" value="<?= $prasarana_id ?>">

            <!-- JUDUL -->
            <div class="form-control mb-3">
                <label class="label">
                    <span class="label-text font-medium">
                        Judul Pengaduan
                    </span>
                </label>
                <input type="text"
                    name="judul"
                    class="input input-bordered w-full"
                    placeholder="Contoh: Kursi Rusak di Kelas X"
                    value="<?= htmlspecialchars($pengaduan['judul'] ?? '') ?>"
                    required>
            </div>

            <!-- DESKRIPSI -->
            <div class="form-control mb-3">
                <label class="label">
                    <span class="label-text font-medium">
                        Deskripsi
                    </span>
                </label>
                <textarea name="deskripsi"
                    class="textarea textarea-bordered w-full min-h-[100px]"
                    placeholder="Ciri-ciri/jelaskan kenapa itu bisa terjadi."
                    required><?= htmlspecialchars($pengaduan['deskripsi'] ?? '') ?></textarea>
            </div>

            <!-- KATEGORI -->
            <div class="form-control mb-3">
                <label class="label">
                    <span class="label-text font-medium">
                        Kategori
                    </span>
                </label>

                <select name="kategori_id"
                    class="select select-bordered w-full"
                    required>

                    <option value="" disabled selected hidden>
                        -- Pilih Kategori --
                    </option>

                    <?php
                    $kategori_result = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

                    while ($kategori = mysqli_fetch_assoc($kategori_result)) :
                    ?>
                        <option value="<?= $kategori['id'] ?>"
                            <?= ($pengaduan['kategori_id'] ?? '') == $kategori['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kategori['nama_kategori']) ?>
                        </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <!-- FOTO -->
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-medium">
                        Foto Pendukung (Opsional)
                    </span>
                </label>
                <input type="file"
                    name="foto"
                    accept="image/*"
                    class="file-input file-input-bordered w-full">
                <label class="label">
                    <span class="label-text-alt text-base-content/60">
                        Format JPG / PNG, max 2MB
                    </span>
                </label>

                <?php if (!empty($pengaduan['foto'])): ?>
                    <div class="mt-3 w-48 h-28 rounded-md overflow-hidden bg-base-200 flex items-center justify-center">
                        <img src="../upload/<?= $pengaduan['foto']; ?>"
                            class="w-full h-full object-contain"
                            alt="Preview Foto" />
                    </div>
                <?php endif; ?>
            </div>

            <!-- ACTION -->
            <div class="modal-action">
                <a href="status.php" class="btn btn-ghost">
                    Batal
                </a>

                <button type="submit"
                    class="btn btn-primary">
                    <?= $pengaduan ? 'Update Pengaduan' : 'Kirim Pengaduan' ?>
                </button>
            </div>

        </form>
    </div>
</dialog>