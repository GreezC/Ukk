<?php
session_start();
include "../config/koneksi.php";
include "../config/session_flash.php";

$email = $_POST['email'];
$password = $_POST['password'];

// ==========================
// LOGIN ATTEMPT (INIT)
// ==========================
if (!isset($_SESSION['login_attempt'])) {
    $_SESSION['login_attempt'] = 0;
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($query);

if($user && password_verify($password, $user['password'])) {

    // ==========================
    // RESET ATTEMPT JIKA BERHASIL
    // ==========================
    $_SESSION['login_attempt'] = 0;

    // ✅ SET SESSION (INI YANG KURANG)
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_id'] = $user['id'];

    // Generate token random
    $token = bin2hex(random_bytes(32));

    // Simpan token ke database
    mysqli_query($conn, "
        INSERT INTO tokens (token, user_id, is_revoked, createdAt, updatedAt)
        VALUES ('$token', '{$user['id']}', 0, NOW(), NOW())
    ");

    // Simpan cookie
    setcookie("login_token", $token, time() + (60*60*24), "/", "", false, true);

    setFlash('success', 'Login berhasil!');

    // ✅ CEK ROLE DARI DATABASE, BUKAN SESSION KOSONG
    if($user['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }
    exit;

} else {

    // ==========================
    // TAMBAH ATTEMPT JIKA GAGAL
    // ==========================
    $_SESSION['login_attempt']++;

    setFlash('error', 'Email atau password salah!');
    header("Location: ../auth/login.php");
    exit;
}