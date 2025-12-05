<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // sesuaikan
$db   = 'phoenix_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

/**
 * Ambil total poin user.
 */
function getUserPoints($conn, $userId)
{
    $stmt = $conn->prepare("SELECT points FROM users WHERE id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $res ? (int)$res['points'] : 0;
}

/**
 * Tambah/kurangi poin user.
 * $points bisa negatif untuk mengurangi poin.
 */
function addUserPoints($conn, $userId, $points)
{
    $stmt = $conn->prepare("UPDATE users SET points = points + ? WHERE id = ?");
    $stmt->bind_param('ii', $points, $userId);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}
?>
