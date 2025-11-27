<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: /phoenix_app/auth/login_user.php");
    exit;
}
?>
