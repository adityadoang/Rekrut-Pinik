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
</head>
<body>
    <h2>Register User</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
    </form>

    <a href="login_user.php">Login User</a>
</body>
</html>
