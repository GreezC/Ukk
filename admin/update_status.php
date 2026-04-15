<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
include "../config/session_flash.php";
require "../config/auth_check.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;
    $catatan = $_POST['catatan'] ?? '';

    if (!$id || !$status) {
        header("Location: pengaduan.php");
        exit;
    }

    $allowed_status = ['diajukan','diproses','selesai','ditolak'];

    if (!in_array($status, $allowed_status)) {
        header("Location: pengaduan.php");
        exit;
    }

    $id = mysqli_real_escape_string($conn, $id);
    $status = mysqli_real_escape_string($conn, $status);
    $catatan = mysqli_real_escape_string($conn, $catatan);

    mysqli_query($conn, "
        UPDATE pengaduan 
        SET status='$status',
            catatan='$catatan'
        WHERE id='$id'
    ");

    setFlash('success', 'Pengaduan Berhasil di Update!');

    header("Location: pengaduan.php");
    exit;
} else {
    setFlash('error', 'data gagal diperbarui!');

    exit;
}