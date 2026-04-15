<?php
if (!defined('APP_RUNNING')) {
    $prev = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    header("Location: $prev");
    exit;
}
include "../config/session_flash.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Pengaduan</title>
    <link rel="stylesheet" href="../assets/css/output.css">
</head>

<body class="h-screen flex bg-base-200 overflow-hidden">
    <?php displayFlash(); ?>

    <!-- OVERLAY (mobile) -->
    <div id="overlay"
         class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"
         onclick="toggleSidebar(false)">
    </div>

    <!-- SIDEBAR -->
    <aside id="sidebar"
           class="w-64 shrink-0 fixed md:static inset-y-0 left-0 z-50
                  transform -translate-x-full md:translate-x-0
                  transition-transform duration-200 border-r border-base-200">

        <?php include __DIR__ . '../../components/sidebar.php'; ?>

    </aside>

    <!-- CONTENT -->
    <div class="flex-1 flex flex-col w-full">

        <!-- HEADER -->
        <header class="h-16 bg-base-100 px-4 flex items-center gap-3 shadow shrink-0">

            <!-- Toggle button (mobile only) -->
            <button class="btn btn-ghost btn-sm md:hidden"
                    onclick="toggleSidebar(true)">
                ☰
            </button>

            <h1 class="font-semibold">Pengaduan Sarana Sekolah</h1>
        </header>

        <!-- MAIN SLOT -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <?= $content ?>
        </main>

    </div>

    <dialog id="modalLogout" class="modal sm:modal-middle">
    <div class="modal-box max-w-sm">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Logout</h3>
        <p class="text-sm text-base-content/60 mb-4">
            Apakah kamu yakin ingin keluar dari akun ini?
        </p>

        <div class="modal-action justify-end">
            <button type="button" class="btn btn-ghost" onclick="closeLogoutModal()">
                Batal
            </button>
            <a href="../auth/logout.php" class="btn btn-error">
                Logout
            </a>
        </div>
    </div>
</dialog>

    <!-- SCRIPT -->
    <script>
        function toggleSidebar(open) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            if (open) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>

</body>
</html>