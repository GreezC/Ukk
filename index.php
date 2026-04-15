<?php
session_start();

if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }

    if ($_SESSION['role'] == 'siswa') {
        header("Location: user/dashboard.php");
        exit;
    }
}

include 'landing.php';