<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? AND role = 'admin'");
    if (!$stmt) {
        $error = "Error prepare: " . $conn->error;
    } else {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['user_id']  = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role']     = 'admin';

            header("Location: ..\admin\dashboard.php");
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
    <title>Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/login-style.css">
</head>
<body>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>


    <div class="overlay"></div>
        <div class="login-wrapper">
            <h1 class="login-title">Login Admin</h1>
            <form method="POST">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" placeholder="Username" required>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <a href="login_user.php">Login sebagai User</a>
</body>
</html>