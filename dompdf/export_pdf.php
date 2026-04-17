<?php
require_once __DIR__ . '/autoload.inc.php';
use Dompdf\Dompdf;

include __DIR__ . '/../config/koneksi.php';

// ambil bulan dari URL
$bulan_filter = $_GET['bulan'] ?? '';

// ambil data utama (hapus join kategori)
$query = mysqli_query($conn, "
    SELECT p.*, s.nama_prasarana
    FROM pengaduan p
    JOIN prasarana s ON p.prasarana_id = s.id
");

// inisialisasi
$laporan = [];
$prasarana_count = [];

while ($pengaduan_row = mysqli_fetch_assoc($query)) {
    $date = new DateTime($pengaduan_row['tanggal_pengaduan']);

    $fmt = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN,
        'MMMM yyyy'
    );

    $bulan = $fmt->format($date);

    // filter bulan
    if ($bulan !== $bulan_filter) continue;

    // laporan utama
    if (!isset($laporan[$bulan])) {
        $laporan[$bulan] = [
            'bulan' => $bulan,
            'total_pengaduan' => 0,
            'diajukan' => 0,
            'diproses' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];
    }

    $status = strtolower($pengaduan_row['status'] ?? '');

    $laporan[$bulan]['total_pengaduan']++;

    if ($status === 'diajukan') $laporan[$bulan]['diajukan']++;
    if ($status === 'diproses') $laporan[$bulan]['diproses']++;
    if ($status === 'selesai') $laporan[$bulan]['selesai']++;
    if ($status === 'ditolak') $laporan[$bulan]['ditolak']++;

    // 🔥 ANALISIS prasarana SAJA
    $prasarana = $pengaduan_row['nama_prasarana'];
    $prasarana_count[$prasarana] = ($prasarana_count[$prasarana] ?? 0) + 1;
}

// sorting
arsort($prasarana_count);

// ambil top data
$prasarana_top = array_slice($prasarana_count, 0, 5, true);

// ambil data utama
$data = reset($laporan);

$total = $data['total_pengaduan'] ?? 0;
$selesai = $data['selesai'] ?? 0;
$ditolak = $data['ditolak'] ?? 0;

$persen = $total > 0
    ? round((($selesai + $ditolak) / $total) * 100)
    : 0;

$dompdf = new Dompdf();

ob_start();
?>

<!-- HEADER -->
<h2 style="text-align:center; margin-bottom:0;">SMKN 1 CISARUA</h2>
<p style="text-align:center; margin-top:2px;">Laporan Pengaduan prasarana</p>
<p style="text-align:center;">Bulan: <?= htmlspecialchars($bulan_filter); ?></p>
<p style="text-align:center;">Tanggal Cetak: <?= date('d-m-Y'); ?></p>
<hr>

<!-- RINGKASAN -->
<h3>Ringkasan</h3>
<table width="100%" border="1" cellpadding="6">
<tr>
    <td>Total</td>
    <td>Diajukan</td>
    <td>Diproses</td>
    <td>Selesai</td>
    <td>Ditolak</td>
    <td>Progress</td>
</tr>
<tr>
    <td><?= $total ?></td>
    <td><?= $data['diajukan'] ?? 0 ?></td>
    <td><?= $data['diproses'] ?? 0 ?></td>
    <td><?= $selesai ?></td>
    <td><?= $data['ditolak'] ?? 0 ?></td>
    <td><?= $persen ?>%</td>
</tr>
</table>

<br>

<!-- prasarana TERBANYAK -->
<h3>prasarana Paling Sering Dilaporkan</h3>
<table width="100%" border="1" cellpadding="6">
<tr><th>prasarana</th><th>Jumlah</th></tr>
<?php foreach ($prasarana_top as $nama_prasarana_top => $jumlah_laporan_top): ?>
<tr>
    <td><?= htmlspecialchars($nama_prasarana_top) ?></td>
    <td><?= htmlspecialchars($jumlah_laporan_top) ?></td>
</tr>
<?php endforeach; ?>
</table>

<br>

<hr>

<!-- FOOTER -->
<p style="text-align:center; font-size:12px;">
Sistem Pengaduan prasarana - SMKN 1 Cisarua
</p>

<?php
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("laporan-$bulan_filter.pdf", ["Attachment" => true]);