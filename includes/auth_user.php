<?php
// includes/auth_user.php

// Mulai session kalau belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah sudah login dan rolenya user
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../auth/login_user.php');
    exit;
}
