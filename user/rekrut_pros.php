<?php
include '../includes/auth_user.php';
require_once '../config/db.php';

$user_id    = $_SESSION['user_id'] ?? 0;
$phoenix_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    header('Location: ../auth/login_user.php');
    exit;
}

if ($phoenix_id <= 0) {
    die("Phoenix tidak ditemukan.");
}

// Ambil data phoenix
$stmt = $conn->prepare("SELECT id, name, req_points FROM phoenix WHERE id = ?");
$stmt->bind_param('i', $phoenix_id);
$stmt->execute();
$ph = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ph) {
    die("Phoenix tidak ditemukan.");
}

// Cek apakah user sudah punya phoenix ini
$stmt = $conn->prepare("SELECT id FROM user_phoenix WHERE user_id = ? AND phoenix_id = ?");
$stmt->bind_param('ii', $user_id, $phoenix_id);
$stmt->execute();
$owned = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($owned) {
    echo "Kamu sudah merekrut " . htmlspecialchars($ph['name']) . " sebelumnya.<br>";
    echo "<br><a href='rekrut_pros.php'>Kembali ke daftar rekrut</a>";
    exit;
}

$cost        = (int)$ph['req_points'];
$user_points = getUserPoints($conn, $user_id);

if ($user_points < $cost) {
    echo "Poin kamu belum cukup untuk merekrut " . htmlspecialchars($ph['name']) . ".<br>";
    echo "Butuh {$cost} poin, poin kamu sekarang {$user_points}.<br>";
    echo "<br><a href='rekrut_pros.php'>Kembali ke daftar rekrut</a>";
    exit;
}

// Proses rekrut
$conn->begin_transaction();

try {
    // insert ke user_phoenix
    $stmt = $conn->prepare("INSERT INTO user_phoenix (user_id, phoenix_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $user_id, $phoenix_id);
    $stmt->execute();
    $stmt->close();

    // kurangi poin
    if (!addUserPoints($conn, $user_id, -$cost)) {
        throw new Exception("Gagal mengurangi poin.");
    }

    $conn->commit();

    $sisa = getUserPoints($conn, $user_id);

    echo "Selamat! Kamu berhasil merekrut " . htmlspecialchars($ph['name']) . ".<br>";
    echo "Poin yang terpakai: {$cost}. Poin tersisa: {$sisa}.<br>";
} catch (Exception $e) {
    $conn->rollback();
    echo "Terjadi kesalahan saat merekrut phoenix: " . $e->getMessage();
}

echo "<br><a href='rekrut_pros.php'>Kembali ke daftar rekrut</a><br>";
echo "<a href='dashboard.php'>Kembali ke Dashboard</a>";
