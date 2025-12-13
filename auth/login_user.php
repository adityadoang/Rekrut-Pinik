<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? AND role = 'user'");
    if (!$stmt) {
        $error = "Error prepare: " . $conn->error;
    } else {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // ===================== FIX LOGIC =====================
        // Support password yang sudah di-hash (password_hash) dan password lama yang masih plaintext.
        $login_ok = false;
        if ($user) {
            $dbPass = $user['password'];

            // kalau hash -> password_verify akan true
            if (password_verify($password, $dbPass)) {
                $login_ok = true;
            } else {
                // fallback untuk data lama/plaintext (tanpa mengubah flow)
                if (hash_equals((string)$dbPass, (string)$password)) {
                    $login_ok = true;
                }
            }
        }
        // =====================================================

        if ($login_ok) {
            // login sukses
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = 'user';

            header("Location: ../user/dashboard.php");
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login User</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/login-style.css">
</head>
<body>

    <div class="login-wrapper">
        <h1 class="login-title">Login User</h1>
        <?php if ($error): ?>
            <p class="message" style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Username" required>
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <a class="button" href="register_user.php">Register</a>
    
        <a class="button" href="login_admin.php">Login sebagai Admin</a>
    </div>

</body>
</html>
