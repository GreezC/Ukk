<?php
session_start();
include "../config/koneksi.php";
include "../config/session_flash.php";

if(isset($_COOKIE['login_token'])){

    $token = $_COOKIE['login_token'];

    // revoke token
    mysqli_query($conn, "
        UPDATE tokens SET is_revoked=1 
        WHERE token='$token'
    ");

    // hapus cookie
    setcookie("login_token", "", time() - 3600, "/");
    }
    setFlash('success', 'Logout berhasil!');
unset($_SESSION['user_id']);
unset($_SESSION['nama']);
unset($_SESSION['role']);
header("Location: login.php");
exit;
?>