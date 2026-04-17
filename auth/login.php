<?php
session_start();
include "../config/session_flash.php";

if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
        exit;
    }

    if ($_SESSION['role'] == 'siswa') {
        header("Location: ../user/dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login - Pengaduan Sarana</title>
  <link rel="stylesheet" href="../assets/css/output.css"> <!-- DaisyUI/Tailwind -->
</head>

<body class="min-h-screen flex items-center justify-center bg-base-200 px-4">
    
  <?php displayFlash(); ?>
  <div class="absolute top-4 left-4">
  <a href="../index.php" class="btn btn-sm">
    ←
  </a>
</div>

  <div class="card w-full max-w-md shadow-2xl bg-base-100">
    <div class="card-body space-y-6">
      
      <!-- Title -->
      <div class="text-center">
        <h2 class="text-3xl font-bold">Login</h2>
        <p class="text-sm text-base-content/60">Silakan masuk untuk melanjutkan</p>
      </div>

      <!-- Form -->
      <form method="POST" action="proses_login.php" class="space-y-4">
        
        <!-- Email -->
        <div class="form-control">
          <label class="label">
            <span class="label-text font-medium">Email</span>
          </label>
          <input type="email" name="email" placeholder="email@gmail.com"
       class="input input-bordered w-full" required />
        </div>

        <!-- Password -->
        <div class="form-control">
          <label class="label">
            <span class="label-text font-medium">Password</span>
          </label>
          <input type="password" name="password" placeholder="••••••••"
                 class="input input-bordered w-full" required />
        </div>

        <!-- Button -->
        <button type="submit" class="btn btn-primary w-full">
          Login
        </button>

        <!-- Lupa Password Link -->
        <div class="text-center mt-3">
            <button type="button" onclick="document.getElementById('modalLupaPassword').showModal()" class="text-sm text-slate-500 hover:text-primary transition-colors hover:underline font-medium focus:outline-none">
                Lupa password?
            </button>
        </div>

      </form>

      <?php if (!empty($_SESSION['error_login'])): ?>
  <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mt-3 shadow-sm animate-shake">
    <svg class="w-5 h-5 mt-0.5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <p class="text-sm font-medium leading-snug">
      <?= htmlspecialchars($_SESSION['error_login']); ?>
    </p>
  </div>
  <?php unset($_SESSION['error_login']); ?>
<?php endif; ?>


    </div>
  </div>

  <!-- Modal Admin Contact -->
  <dialog id="modalLupaPassword" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box p-6 bg-white rounded-2xl shadow-xl border border-slate-100 relative">
      <form method="dialog">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-slate-400 hover:text-slate-600 focus:outline-none">✕</button>
      </form>
      <h3 class="font-bold text-xl text-slate-800 mb-2 mt-2">Lupa Password?</h3>
      <p class="text-slate-500 mb-6 text-sm leading-relaxed">Jika kamu lupa password atau mengalami kendala login, silakan hubungi admin sekolah melalui kontak di bawah ini:</p>
      
      <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60 space-y-2">
        <a href="mailto:admin@sekolah.com" class="flex items-center gap-4 p-3 hover:bg-white rounded-lg transition-all border border-transparent hover:border-slate-200 hover:shadow-sm group">
          <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex justify-center items-center shrink-0 group-hover:scale-110 transition-transform">
             <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <div>
            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">Email Admin</p>
            <p class="text-[15px] font-semibold text-slate-700">adang@smkn1cisarua.com</p>
          </div>
        </a>
        
        <a href="https://wa.me/6289662250053" target="_blank" class="flex items-center gap-4 p-3 hover:bg-white rounded-lg transition-all border border-transparent hover:border-slate-200 hover:shadow-sm group">
          <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex justify-center items-center shrink-0 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          </div>
          <div>
            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">WhatsApp Admin</p>
            <p class="text-[15px] font-semibold text-slate-700">089662250053</p>
          </div>
        </a>
      </div>

    </div>
    <form method="dialog" class="modal-backdrop bg-slate-900/40 backdrop-blur-sm transition-all text-transparent">
      <button>Tutup</button>
    </form>
  </dialog>

</body>
</html>