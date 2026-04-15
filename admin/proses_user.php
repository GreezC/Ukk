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
        DELETE FROM users
        WHERE id='$id'
    ");

    setFlash('success', 'User berhasil di hapus!');
    header("Location: user.php");
    exit;
}


/* ======================
   TAMBAH & EDIT
====================== */
if (isset($_POST['save'])) {

    $id       = $_POST['id'] ?? null;
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // ==========================
    // AMANKAN ROLE (EDIT TIDAK BISA UBAH)
    // ==========================
    if (!empty($id)) {
        // MODE EDIT → AMBIL ROLE DARI DATABASE
        $id_safe = mysqli_real_escape_string($conn, $id);

        $q = mysqli_query($conn, "SELECT role FROM users WHERE id='$id_safe'");
        $data = mysqli_fetch_assoc($q);

        $role = $data['role'];

    } else {
        // MODE TAMBAH → AMBIL DARI FORM
        $role = mysqli_real_escape_string($conn, $_POST['role']);
    }

    // Default NULL (untuk admin)
    $nisn = null;
    $no_telp = null;

    // Jika role siswa → wajib isi
    if ($role == 'siswa') {

        if (empty($_POST['nisn']) || empty($_POST['no_telp'])) {
            setFlash('error', 'NISN dan No Telp wajib untuk siswa');
            header("Location: user.php");
            exit;
        }

        $nisn    = mysqli_real_escape_string($conn, $_POST['nisn']);
        $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    }

    /* ======================
       EDIT
    ====================== */
    if (!empty($id)) {

        $updatePassword = "";

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $updatePassword = ", password='$hash'";
        }

        mysqli_query($conn, "
         UPDATE users SET
         nama='$nama',
         email='$email',
         role='$role',
         nisn='$nisn',
         no_telp='$no_telp'
         $updatePassword
         WHERE id='$id'
        ");
        setFlash('success', 'User berhasil di edit!');
    }

    /* ======================
       TAMBAH
    ====================== */ else {

        if (empty($password)) {
            die("Password wajib diisi untuk user baru");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query($conn, "
            INSERT INTO users
            (nama, email, password, role, nisn, no_telp)
            VALUES
            ('$nama','$email','$hash','$role','$nisn','$no_telp')
        ");
        setFlash('success', 'User berhasil di tambah!');
    }

    header("Location: user.php");
    exit;
}