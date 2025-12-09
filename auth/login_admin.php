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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-top: 12px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            margin-top: 4px;
            box-sizing: border-box;
        }
        button {
            margin-top: 16px;
            width: 100%;
            padding: 10px;
            border: none;
            background: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            margin-top: 10px;
            color: red;
            font-size: 0.9rem;
            text-align: center;
        }
        .link-user {
            margin-top: 12px;
            text-align: center;
            font-size: 0.9rem;
        }
        .link-user a {
            color: #007bff;
            text-decoration: none;
        }
        .link-user a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login Admin</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">Username</label>
        <input
            id="username"
            type="text"
            name="username"
            placeholder="Masukkan username"
            required
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
        >

        <label for="password">Password</label>
        <input
            id="password"
            type="password"
            name="password"
            placeholder="Masukkan password"
            required
        >

        <button type="submit">Login</button>
    </form>

    <div class="link-user">
        <p>Bukan admin? <a href="login_user.php">Login sebagai User</a></p>
    </div>
</div>
</body>
</html>
