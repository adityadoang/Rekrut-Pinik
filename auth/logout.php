<?php
// auth/logout.php
session_start();

// Hapus semua data session
$_SESSION = [];

// Hancurkan session
session_destroy();

// Hapus cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect ke login_user.php yang ada di folder auth juga
header("Location: login_user.php");  // atau "Location: ./login_user.php"
exit();
?>