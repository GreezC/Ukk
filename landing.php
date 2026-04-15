<?php
include "config/koneksi.php";

// ==========================
// TOTAL PENGADUAN
// ==========================
$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan");
$total = mysqli_fetch_assoc($q_total)['total'] ?? 0;


// ==========================
// PERSEN SELESAI
// ==========================
$q_selesai = mysqli_query($conn, "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status IN ('selesai','ditolak') THEN 1 ELSE 0 END) as selesai
    FROM pengaduan
");

$data = mysqli_fetch_assoc($q_selesai);

$total_data = $data['total'] ?? 0;
$total_selesai = $data['selesai'] ?? 0;

$persen_selesai = $total_data > 0
    ? round(($total_selesai / $total_data) * 100)
    : 0;


// ==========================
// RATA-RATA WAKTU (hari)
// ==========================
$query_rata = mysqli_query($conn, "
    SELECT AVG(DATEDIFF(updated_at, tanggal_pengaduan)) as rata_hari
    FROM pengaduan
    WHERE status='selesai' AND updated_at IS NOT NULL
");

$rata = mysqli_fetch_assoc($query_rata)['rata_hari'] ?? 0;
$rata = round($rata);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/output.css">
</head>

<body>
    <div class="min-h-screen flex flex-col">
        <div class="navbar bg-base-100 shadow-sm">
            <div class="navbar-start">
                <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                    </div>
                    <ul
                        tabindex="-1"
                        class="menu menu-sm dropdown-content bg-base-100 rounded-box z-10 mt-3 w-52 p-2 shadow">
                        <li><a>Home</a></li>
                    </ul>
                </div>
                <a href="index.php" class="btn btn-ghost text-primary text-xl">
                    LaporinAja
                </a>
            </div>

            <div class="navbar-end">
                <a href="auth/login.php" class="btn btn-sm btn-primary">
                    Login
                </a>
            </div>
        </div>

        <section class="px-6 pt-12 max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <!-- TEXT -->
                <div>
                    <p class="text-sm text-primary mb-2">
                        • Platform Pengaduan Sekolah
                    </p>
                    <h1 class="text-5xl font-bold leading-tight">
                        Sarana Sekolah <br />
                        <span class="text-primary">Lebih Baik & Transparan</span>
                    </h1>
                    <p class="mt-6 text-base-content/70 max-w-lg">
                        Laporkan kerusakan fasilitas sekolah secara online dan
                        pantau status pengaduan dengan mudah.
                    </p>

                    <div class="mt-8 flex gap-4">
                        <a href="auth/login.php" class="btn btn-primary">
                            Ajukan Pengaduan
                        </a>
                        <a href="#alur" class="btn btn-outline">
                            Lihat Alur
                        </a>
                    </div>
                </div>

                <!-- IMAGE CARD -->
                <div class="relative">
                    <img
                        src="https://smkn1cisarua.sch.id/images/video.jpg"
                        class="rounded-2xl object-cover h-[420px] w-full" />
                    <div
                        class="absolute bottom-4 left-4 bg-base-100 p-4 rounded-xl shadow">
                        <p class="text-sm font-semibold">Pengaduan Terpantau</p>
                        <p class="text-xs text-base-content/70">
                            Status diperbarui secara real-time
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6 text-center">
                <div>
                    <h2 class="text-3xl font-bold text-primary">
                        <?= $total ?>+
                    </h2>
                    <p class="text-sm text-base-content/70">Pengaduan Masuk</p>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-primary">
                        <?= $persen_selesai ?>%
                    </h2>
                    <p class="text-sm text-base-content/70">Ditindaklanjuti</p>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-primary">
                        <?= $rata > 0 ? "<" . $rata . " Hari" : "-"; ?>
                    </h2>
                    <p class="text-sm text-base-content/70">Respon Rata-rata</p>
                </div>
            </div>
        </section>

        <section class="bg-base-200 py-20 px-6">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12">
                    Fokus Pada Pelayanan Terbaik
                </h2>

                <div class="grid md:grid-cols-3 gap-8 mt-12">

                    <!-- Card 1 -->
                    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100/60 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3 tracking-wide">Laporan Mudah</h3>
                        <p class="text-[15px] text-slate-500 leading-relaxed">
                            Laporkan kerusakan sarana dengan mengisi form sederhana dan upload foto bukti
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100/60 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3 tracking-wide">Pantau Status</h3>
                        <p class="text-[15px] text-slate-500 leading-relaxed">
                            Lacak progress perbaikan secara real-time dari pengajuan hingga selesai
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100/60 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3 tracking-wide">Riwayat Lengkap</h3>
                        <p class="text-[15px] text-slate-500 leading-relaxed">
                            Akses riwayat semua laporan yang pernah dibuat dengan mudah
                        </p>
                    </div>

                </div>
            </div>
        </section>
        <section id="alur" class="py-20 px-6 max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-10">
                Alur Pengaduan
            </h2>

            <ul class="steps steps-vertical lg:steps-horizontal w-full">
                <li class="step step-primary">Login</li>
                <li class="step ">Memilih Sarana</li>
                <li class="step">Isi Pengaduan</li>
                <li class="step ">Diproses</li>
                <li class="step ">Selesai</li>
            </ul>
        </section>

        <section class="bg-primary text-primary-content py-20 text-center">
            <h2 class="text-3xl font-bold mb-4">
                Wujudkan Sarana Sekolah yang Lebih Baik
            </h2>
            <p class="mb-6">
                Sampaikan keluhan Anda dan bantu sekolah berkembang
            </p>
            <a href="auth/login.php" class="btn btn-outline btn-lg">
                Ajukan Pengaduan
            </a>
        </section>
        <footer class="bg-neutral text-neutral-content mt-auto">
            <div class="max-w-6xl mx-auto px-6 py-10 grid md:grid-cols-3 gap-8 text-sm">

                <div>
                    <h3 class="font-semibold mb-3">Tentang Sistem</h3>
                    <p class="text-neutral-content/70">
                        Sistem ini digunakan untuk mempermudah pelaporan kerusakan
                        sarana sekolah secara transparan dan terdokumentasi.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold mb-3">Kontak</h3>
                    <p class="text-neutral-content/70">
                        SMKN 1 Cisarua<br>
                        Jl. Pendidikan No. 123<br>
                        Email: info@smkn1cisarua.sch.id
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold mb-3">Navigasi</h3>
                    <ul class="space-y-2 text-neutral-content/70">
                        <li><a href="#" class="hover:underline">Beranda</a></li>
                        <li><a href="#alur" class="hover:underline">Alur</a></li>
                        <li><a href="auth/login.php" class="hover:underline">Login</a></li>
                    </ul>
                </div>

            </div>

            <div class="text-center text-xs py-4 border-t border-neutral-content/10">
                © 2026 SMKN 1 Cisarua • Sistem Pengaduan Sarana
            </div>
        </footer>

    </div>
</body>

</html>