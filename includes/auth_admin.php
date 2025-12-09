<?php
// includes/auth_admin.php

// Mulai session kalau belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah sudah login dan rolenya admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    // Redirect ke halaman login admin
    // Sesuaikan path jika perlu, misalnya: /Rekrut-Pinik/auth/login_admin.php
    header('Location: ../auth/login_admin.php');
    exit;
}
