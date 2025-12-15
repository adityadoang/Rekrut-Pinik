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
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        if (!$stmt) {
            $message = "Error prepare: " . $conn->error;
        } else {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $message = 'Username sudah digunakan.';
            } else {
                // hash password
                $hash = password_hash($password, PASSWORD_DEFAULT);


                $role   = 'user';
                $points = 0;
                $avatar = null;

                $insert = $conn->prepare("
                    INSERT INTO users (username, password, role, points, avatar)
                    VALUES (?, ?, ?, ?, ?)
                ");

                if (!$insert) {
                    $message = "Error prepare insert: " . $conn->error;
                } else {
                    $insert->bind_param('sssis', $username, $hash, $role, $points, $avatar);

                    if ($insert->execute()) {
                        $message = 'Registrasi berhasil. Silakan login.';
                    } else {
                        if ($conn->errno == 1062) {
                            $message = 'Username sudah digunakan.';
                        } else {
                            $message = 'Gagal registrasi: ' . $conn->error;
                        }
                    }
                    $insert->close();
                }
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register User</title>
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
.register-wrapper {
background: rgba(0, 0, 0, 0.35) url('../assets/phoenix-bg.jpeg') center/cover no-repeat;
padding: 40px;
border-radius: 16px;
border: 2px solid #981008;
box-shadow: 0 0 40px black;
max-width: 500px;
width: 100%;
}
.register-title {
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
    margin-bottom: 12px;
}
    </style>
</head>
<body>

    <div class="register-wrapper">
        <h1 class="register-title">Register</h1>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Username" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Password" required>

            <button type="submit">Register</button>
        </form>

        <a class="button" href="login_user.php">Login User</a>
    </div>
</body>
</html>
