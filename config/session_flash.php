<?php

// Fungsi untuk set flash message
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, info, warning
        'message' => $message
    ];
}

// Fungsi untuk menampilkan flash message
function displayFlash() {
    if(isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        $color = match($flash['type']) {
            'success' => 'bg-success text-white',
            'error' => 'bg-error text-white',
            'warning' => 'bg-warning text-black',
            'info' => 'bg-info text-black',
            default => 'bg-base-200 text-black'
        };
        
        echo "<div id='flashMessage' class='fixed top-4 right-[-100%] px-4 py-2 rounded shadow $color'
                  style='opacity: 0; transition: all 0.5s ease;'>
                {$flash['message']}
              </div>";

        unset($_SESSION['flash']);
    }
}
?>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const flash = document.getElementById('flashMessage');
    if(flash){
        // Muncul: slide dari kanan + fade in
        setTimeout(() => {
            flash.style.right = '1rem';
            flash.style.opacity = '1';
        }, 100);

        // Hilang otomatis: fade out
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }, 3000);
    }
});
</script>