<?php
// auth/login_admin.php

session_start();
require_once '../config/db.php';

$error = '';

// Kalau sudah login sebagai admin, langsung lempar ke dashboard
if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: ../admin/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Ambil data admin berdasarkan username dan role
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = 'admin'");
        if (!$stmt) {
            $error = 'Terjadi kesalahan pada server: ' . $conn->error;
        } else {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            if ($admin) {
                $hash = $admin['password'];

                // Support 2 kondisi:
                // 1) password di database sudah di-hash (password_hash)
                // 2) password di database masih plain text
                if (password_verify($password, $hash) || $password === $hash) {
                    // Set session admin
                    $_SESSION['user_id']  = $admin['id'];
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['role']     = $admin['role']; // harus 'admin'

                    // Redirect ke dashboard admin
                    header('Location: ../admin/dashboard.php');
                    exit;
                } else {
                    $error = 'Username atau password salah.';
                }
            } else {
                $error = 'Username atau password salah.';
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link rel="stylesheet" href="../assets/login-style.css">
    <style>
        
    </style>
</head>
<body>
<div class="container">
    <div class="login-wrapper">
        <h1 class="login-title">Login Admin</h1>
        <?php if ($error): ?>
            <p class="message" style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Username" required>
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <a class="button" href="login_user.php">Login sebagai User</a></p>
    </div>
</div>
</body>
</html>
