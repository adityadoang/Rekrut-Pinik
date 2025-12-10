<?php
session_start();

require_once '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = 'Username dan password wajib diisi.';
    } else {
        // cek apakah username sudah dipakai
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if (!$stmt) {
            $message = "Error prepare: " . $conn->error;
        } else {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = 'Username sudah digunakan.';
            } else {
                // hash password
                $hash = password_hash($password, PASSWORD_BCRYPT);

                // insert user baru dengan role 'user' dan atribut awal 0
                $stmt = $conn->prepare("
                    INSERT INTO users 
                        (username, password, role, element_fire, element_water, element_ice, element_wind, element_earth, intelligence)
                    VALUES 
                        (?, ?, 'user', 0, 0, 0, 0, 0, 0)
                ");

                if (!$stmt) {
                    $message = "Error prepare insert: " . $conn->error;
                } else {
                    $stmt->bind_param('ss', $username, $hash);

                    if ($stmt->execute()) {
                        $message = 'Registrasi berhasil. Silakan login.';
                    } else {
                        $message = 'Gagal registrasi: ' . $conn->error;
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register User</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/register-stylexs.css">
    <link rel="stylesheet" href="../assets/register-style.css">
</head>
<body>

    <div class="register-wrapper">
        <h1 class="register-title">Register</h1>
        <?php if ($message): ?>
            <p class="message" ><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Username" required>
            <label for="password">Password</label>
            <input id="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>

        <a class="button" href="login_user.php">Login User</a>
    </div>
</body>
</html>