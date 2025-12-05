<?php
require_once 'config/db.php';

$hash = password_hash('admin123', PASSWORD_BCRYPT);
echo "Hash baru: " . $hash . "<br>";

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin' AND role = 'admin'");
$stmt->bind_param('s', $hash);

if ($stmt->execute()) {
    echo "Password admin di-update ke admin123";
} else {
    echo "Gagal update: " . $conn->error;
}
