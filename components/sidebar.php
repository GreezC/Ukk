<?php
if (!defined('APP_RUNNING')) {
    $prev = $_SERVER['HTTP_REFERER'] ?? '../../index.php';
    header("Location: $prev");
    exit;
}

// Menu berdasarkan role
$role = $_SESSION['role']; 

$menu = [];

if ($role === 'admin') {
    $menu = [
        ['label' => 'Dashboard', 'icon' => 'dashboard.svg', 'link' => 'dashboard.php'],
        ['label' => 'Pengaduan', 'icon' => 'pengaduan.svg', 'link' => 'pengaduan.php'],
        ['label' => 'Prasarana', 'icon' => 'prasarana.svg', 'link' => 'prasarana.php'],
        ['label' => 'Kategori', 'icon' => 'kategori.svg', 'link' => 'kategori.php'],
        ['label' => 'User', 'icon' => 'user.svg', 'link' => 'user.php'],
        ['label' => 'Riwayat Pengaduan', 'icon' => 'riwayat.svg', 'link' => 'riwayat.php'],
    ];
} else {
    // siswa
    $menu = [
        ['label' => 'Pengaduan', 'icon' => 'pengaduan.svg', 'link' => 'dashboard.php'],
        ['label' => 'status', 'icon' => 'status.svg', 'link' => 'status.php'],
        ['label' => 'Riwayat', 'icon' => 'riwayat.svg', 'link' => 'riwayat.php'],
    ];
}

$current_file = basename($_SERVER['PHP_SELF']);
?>

<div class="flex-1 flex flex-col bg-white h-full">

    <!-- BRAND + USER INFO -->
    <div class="px-5 pt-6 pb-5 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 min-w-11 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center shadow-md">
                <img src="../assets/icons/megaphone.svg" class="w-5.5 h-5.5 brightness-0 invert" alt="Logo" />
            </div>
            <div class="flex flex-col leading-tight">
                <span class="text-base font-bold tracking-tight text-primary">LaporinAja</span>
                <span class="text-xs text-slate-400"><?= htmlspecialchars($_SESSION['nama'] ?? '-') ?></span>
            </div>
        </div>
    </div>

    <!-- ===== MENU ===== -->
    <nav class="flex-1 px-3 py-2 overflow-y-auto">
        <ul class="flex flex-col gap-0.5 text-sm font-medium">
            <?php foreach ($menu as $item) : ?>
                <?php $isActive = ($current_file === basename($item['link'])); ?>
                <li>
                    <a href="<?= $item['link']; ?>" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
                        <?= $isActive 
                            ? 'bg-emerald-50 text-emerald-600 font-semibold' 
                            : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700'; ?>">
                        <img src="../assets/icons/<?= $item['icon']; ?>" 
                             class="w-5 h-5 <?= $isActive ? 'brightness-0 saturate-100 hue-rotate-100 sepia invert-0' : 'brightness-0 opacity-50' ?>" 
                             style="<?= $isActive ? 'filter: brightness(0) saturate(100%) invert(42%) sepia(93%) saturate(450%) hue-rotate(115deg) brightness(95%)' : 'filter: brightness(0) opacity(0.5)' ?>" />
                        <span><?= $item['label']; ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- ===== LOGOUT ===== -->
    <div class="px-3 py-3 shrink-0 border-t border-slate-100 mt-auto">
        <button onclick="openLogoutModal()" 
                class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg
                       text-slate-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200">
            <img src="../assets/icons/logout.svg" class="w-5 h-5" style="filter: brightness(0) opacity(0.5)" />
            <span>Keluar</span>
        </button>
    </div>

</div>


<script>
function openLogoutModal() {
    document.getElementById("modalLogout").showModal();
}

function closeLogoutModal() {
    document.getElementById("modalLogout").close();
}
</script>