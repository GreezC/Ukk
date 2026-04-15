<?php
session_start();
include "koneksi.php";

if(!isset($_COOKIE['login_token'])){
    header("Location: ../auth/login.php");
    exit;
}

$token = $_COOKIE['login_token'];

$query = mysqli_query($conn, "
    SELECT users.* FROM tokens
    JOIN users ON tokens.user_id = users.id
    WHERE tokens.token='$token'
    AND tokens.is_revoked=0
");

$user = mysqli_fetch_assoc($query);

if(!$user){
    header("Location: ../auth/login.php");
    exit;
}

// Simpan data user ke session
$_SESSION['user_id'] = $user['id'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['role'] = $user['role'];

$current_path = $_SERVER['PHP_SELF']; 

if (str_starts_with($current_path, '/pengaduan/admin') && $_SESSION['role'] !== 'admin') {
    // siswa coba akses halaman admin
    header("Location: /pengaduan/user/dashboard.php");
    exit;
}

if (str_starts_with($current_path, '/pengaduan/user') && $_SESSION['role'] !== 'siswa') {
    // admin coba akses halaman user
    header("Location: /pengaduan/admin/dashboard.php");
    exit;
}

