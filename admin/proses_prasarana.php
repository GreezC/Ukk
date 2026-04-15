<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
include "../config/session_flash.php";
require "../config/auth_check.php";


if (isset($_POST['delete'])) {

    $id = mysqli_real_escape_string($conn, $_POST['delete']);

    mysqli_query($conn, "
        DELETE FROM prasarana
        WHERE id='$id'
    ");

    setFlash('success', 'Prasaran berhasil di hapus!');

    header("Location: prasarana.php");
    exit;
}

if (isset($_POST['save'])) {

    $id = $_POST['id'] ?? null;
    $nama = mysqli_real_escape_string($conn, $_POST['nama_prasarana']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']); 

    // ==========================
    // CEK DUPLIKAT
    // ==========================
    if ($id) {
        // MODE EDIT
        $check = mysqli_query($conn, "
            SELECT id FROM prasarana 
            WHERE nama_prasarana='$nama' 
            AND lokasi='$lokasi'
            AND id != '$id'
        ");
    } else {
        // MODE TAMBAH
        $check = mysqli_query($conn, "
            SELECT id FROM prasarana 
            WHERE nama_prasarana='$nama' 
            AND lokasi='$lokasi'
        ");
    }

    if (mysqli_num_rows($check) > 0) {
        setFlash('error', 'Prasarana dengan nama dan lokasi tersebut sudah ada!');
        header("Location: prasarana.php");
        exit;
    }

    // ==========================
    // LANJUT SIMPAN
    // ==========================
    if ($id) {
        mysqli_query($conn, "
            UPDATE prasarana
            SET nama_prasarana='$nama',
                lokasi='$lokasi'
            WHERE id='$id'
        ");
        setFlash('success', 'Prasarana berhasil di edit!');
    } else {
        mysqli_query($conn, "
            INSERT INTO prasarana (nama_prasarana, lokasi)
            VALUES ('$nama','$lokasi')
        ");
        setFlash('success', 'Prasarana berhasil di tambah!');
    }

    header("Location: prasarana.php");
    exit;
}