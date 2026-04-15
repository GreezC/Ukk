<?php
define('APP_RUNNING', true);
include "../config/koneksi.php";
include "../config/session_flash.php";
include "../config/auth_check.php";

/* ======================
   DELETE
====================== */
if (isset($_POST['delete'])) {

    $id = mysqli_real_escape_string($conn, $_POST['delete']);

    mysqli_query($conn, "
        DELETE FROM kategori
        WHERE id='$id'
    ");

    setFlash('success', 'Kategori berhasil di hapus!');
    header("Location: kategori.php");
    exit;
}


/* ======================
   TAMBAH & EDIT
====================== */
if (isset($_POST['save'])) {

    $id = $_POST['id'] ?? null;
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

    /* ======================
       EDIT
    ====================== */
    if (!empty($id)) {
        mysqli_query($conn, "
         UPDATE kategori SET
         nama_kategori='$nama_kategori'
         WHERE id='$id'
        ");
        setFlash('success', 'Kategori berhasil di edit!');
    }

    /* ======================
       TAMBAH
    ====================== */ else {
        mysqli_query($conn, "
            INSERT INTO kategori
            (nama_kategori)
            VALUES
            ('$nama_kategori')
        ");
        setFlash('success', 'Kategori berhasil di tambah!');
    }

    header("Location: kategori.php");
    exit;
}
