<?php
// auth/logout.php

session_start();

// Hapus semua data di session
$_SESSION = [];
session_unset();
session_destroy();

// Optional: hapus cookie session juga (kalau mau benar-benar bersih)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Setelah logout, arahkan ke halaman login user atau admin
// Silakan pilih salah satu dan sesuaikan path-nya:

// Redirect ke halaman login user:
header('Location: login_user.php');

// Kalau mau ke login admin, pakai:
// header('Location: login_admin.php');

exit;
