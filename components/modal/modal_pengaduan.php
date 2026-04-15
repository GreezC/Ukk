<?php
if (!defined('APP_RUNNING')) exit;

$id = $_GET['detail'] ?? null;
$view_only = isset($_GET['view']) && $_GET['view'] == 'only';
if (!$id) return;

$query = mysqli_query($conn, "
    SELECT p.*, u.nama, s.nama_prasarana , k.nama_kategori
    FROM pengaduan p
    JOIN users u ON p.user_id = u.id
    JOIN prasarana s ON p.prasarana_id = s.id
    JOIN kategori k ON p.kategori_id = k.id
    WHERE p.id = '$id'
");

$pengaduan = mysqli_fetch_assoc($query);
if (!$pengaduan) return;

$statuses = [
  'diajukan' => 'badge-warning',
  'diproses' => 'badge-info',
  'selesai' => 'badge-success',
  'ditolak' => 'badge-error'
];
?>

<dialog id="modalAdmin" class="modal" open>
  <div class="modal-box max-w-3xl">

    <h3 class="font-semibold text-lg mb-4">Detail Pengaduan</h3>

    <form method="POST" action="/pengaduan/admin/update_status.php">

      <input type="hidden" name="id" value="<?= $pengaduan['id'] ?>">

      <div class="grid grid-cols-2 gap-4 mb-4">

        <div>
          <p class="text-sm opacity-60">Pelapor</p>
          <p class="font-medium"><?= htmlspecialchars($pengaduan['nama']) ?></p>
        </div>

        <div>
          <p class="text-sm opacity-60">Prasarana</p>
          <p class="font-medium"><?= htmlspecialchars($pengaduan['nama_prasarana']) ?></p>
        </div>

        <div>
          <p class="text-sm opacity-60">Judul</p>
          <p class="font-medium"><?= htmlspecialchars($pengaduan['judul']) ?></p>
        </div>

        <div>
          <p class="text-sm opacity-60 mb-1">Status</p>
          <select name="status"
            class="select select-bordered w-full"
            <?= $view_only ? 'disabled' : '' ?>>
            <?php foreach ($statuses as $status_key => $class): ?>
              <option value="<?= htmlspecialchars($status_key) ?>" <?= $pengaduan['status'] == $status_key ? 'selected' : '' ?>>
                <?= ucfirst(htmlspecialchars($status_key)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <p class="text-sm opacity-60">Kategori</p>
          <p class="font-medium"><?= htmlspecialchars($pengaduan['nama_kategori']) ?></p>
        </div>

      </div>

      <div class="mb-4">
        <p class="text-sm opacity-60 mb-1">Deskripsi</p>
        <p class="bg-base-200 p-3 rounded-lg text-sm">
          <?= htmlspecialchars($pengaduan['deskripsi']) ?>
        </p>
      </div>

      <div class="mb-4">
        <label class="text-sm opacity-60 mb-1 block">Catatan / Respon Admin</label>
        <textarea name="catatan"
          class="textarea textarea-bordered w-full min-h-[90px]"
          <?= $view_only ? 'readonly' : '' ?>><?= htmlspecialchars($pengaduan['catatan'] ?? '') ?></textarea>
      </div>

      <div class="mb-4">
        <label class="text-sm opacity-60 mb-1 block">Bukti</label>
        <?php if ($pengaduan['foto']): ?>
          <img src="../upload/<?= $pengaduan['foto']; ?>"
            class="rounded-md object-contain max-w-[200px] max-h-[140px]">
        <?php else: ?>
          <span class="text-xs opacity-60">Tidak ada gambar</span>
        <?php endif; ?>
      </div>

      <div class="modal-action">
        <a href="?" class="btn btn-ghost">Tutup</a>

        <?php if (!$view_only): ?>
          <button type="submit" class="btn btn-primary">
            Kirim Respon
          </button>
        <?php endif; ?>
      </div>

    </form>
  </div>
</dialog>