<?php

include "../config/koneksi.php";
include "../config/session_flash.php";
include "../config/auth_check.php";

// ==========================
// AMBIL DATA INPUT
// ==========================
$user_id       = $_SESSION['user_id'];
$pengaduan_id  = $_POST['id'] ?? null;
$prasarana_id     = $_POST['prasarana_id'] ?? null;
$kategori_id   = $_POST['kategori_id'] ?? null;
$judul         = $_POST['judul'] ?? null;
$deskripsi     = $_POST['deskripsi'] ?? null;

$foto_baru = null;

// ==========================
// PROSES UPLOAD FOTO
// ==========================
if (!empty($_FILES['foto']['name'])) {

    $uploaded_file = $_FILES['foto'];

    $file_extension = strtolower(pathinfo($uploaded_file['name'], PATHINFO_EXTENSION));
    $allowed_extension = ['jpg', 'jpeg', 'png'];

    // Validasi format
    if (!in_array($file_extension, $allowed_extension)) {
        die("Format file harus JPG atau PNG");
    }

    // Validasi ukuran
    if ($uploaded_file['size'] > 2 * 1024 * 1024) {
        die("Ukuran file maksimal 2MB");
    }

    // Generate nama file baru
    $foto_baru = time() . "_" . uniqid() . "." . $file_extension;
    $upload_path = "../upload/" . $foto_baru;

    // ==========================
    // HAPUS FOTO LAMA (JIKA EDIT)
    // ==========================
    if (!empty($pengaduan_id)) {

        $old_photo_query = mysqli_query($conn, "SELECT foto FROM pengaduan WHERE id='$pengaduan_id'");
        $old_photo_data  = mysqli_fetch_assoc($old_photo_query);

        if (!empty($old_photo_data['foto'])) {
            $old_photo_path = "../upload/" . $old_photo_data['foto'];

            if (file_exists($old_photo_path)) {
                unlink($old_photo_path);
            }
        }
    }

    // Upload file
    move_uploaded_file($uploaded_file['tmp_name'], $upload_path);
}

// ==========================
// PROSES SIMPAN DATA
// ==========================
if (!empty($pengaduan_id)) {

    // ======================
    // EDIT DATA
    // ======================
    if ($foto_baru) {
        mysqli_query($conn, "
            UPDATE pengaduan SET
            judul='$judul',
            kategori_id='$kategori_id',
            deskripsi='$deskripsi',
            foto='$foto_baru'
            WHERE id='$pengaduan_id'
        ");
    } else {
        mysqli_query($conn, "
            UPDATE pengaduan SET
            judul='$judul',
            kategori_id='$kategori_id',
            deskripsi='$deskripsi'
            WHERE id='$pengaduan_id'
        ");
    }

    setFlash('success', 'Pengaduan berhasil di edit!');

} else {

    // ======================
    // TAMBAH DATA
    // ======================
    mysqli_query($conn, "
        INSERT INTO pengaduan 
        (user_id, prasarana_id, kategori_id, judul, deskripsi, foto, status, tanggal_pengaduan)
        VALUES
        ('$user_id','$prasarana_id','$kategori_id','$judul','$deskripsi','$foto_baru','diajukan',NOW())
    ");

    setFlash('success', 'Pengaduan berhasil ditambahkan!');
}

// ==========================
// REDIRECT
// ==========================
header("Location: status.php");
exit;