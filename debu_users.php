<?php
require_once 'config/db.php';

echo "<h2>DATABASE TEST — USERS TABLE</h2>";

$result = $conn->query("SELECT id, username, role, password FROM users");

if (!$result) {
    die("QUERY ERROR: " . $conn->error);
}

if ($result->num_rows === 0) {
    echo "Tabel users KOSONG.<br>";
} else {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']} | Username: {$row['username']} | Role: {$row['role']} | Password Hash: {$row['password']}<br>";
    }
}
