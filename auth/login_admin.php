<?php
session_start();
require_once '../config/db.php';

$error = '';

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


                if (password_verify($password, $hash) || $password === $hash) {
                    $_SESSION['user_id']  = $admin['id'];
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['role']     = $admin['role']; 

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
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; }
    body {
    color: #fff;
    background: #111;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
}
.login-wrapper {
    background: rgba(0, 0, 0, 0.35) url('../assets/phoenix-bg.jpeg') center/cover no-repeat;
    padding: 40px;
    border-radius: 16px;
    border: 2px solid #981008;
    box-shadow: 0 0 40px black;
    max-width: 500px;
    width: 100%;
}
.login-title {
    font-family: 'Press Start 2P', sans-serif;
    text-align: center;
    font-size: 32px;
    margin-bottom: 30px;
}
label {
    font-size: 18px;
    margin-bottom: 8px;
    display: block;
    font-family: 'Courier New', Courier, monospace;
}
input {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 20px;
    border: 2px solid #981008;
    background: rgba(0, 0, 0, 0.55);
    color: white;
}
button, .button {
    font-family: 'Courier New', Courier, monospace;
    display: block;
    margin: 0 auto;
    padding: 12px 48px;
    background-color: #981008;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 18px;
    width: fit-content;
}

.button {
    margin-top: 1em;
}

button:hover, .button:hover {
    filter: brightness(1.1);
}

.message {
    text-align: center;
    font-family: 'Courier New', Courier, monospace;
}
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
