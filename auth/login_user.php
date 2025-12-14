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
        
        $login_ok = false;
        if ($user) {
            $dbPass = $user['password'];
            if (password_verify($password, $dbPass)) {
                $login_ok = true;
            } else {
                if (hash_equals((string)$dbPass, (string)$password)) {
                    $login_ok = true;
                }
            }
        }
        
        if ($login_ok) {
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
    font-family: 'Inter', sans-serif;
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
    <div class="login-wrapper">
        <h1 class="login-title">Login User</h1>
        <?php if ($error): ?>
            <p class="message"><?= htmlspecialchars($error) ?></p>
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