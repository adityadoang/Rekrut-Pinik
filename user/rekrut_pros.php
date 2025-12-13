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

$user_points = getUserPoints($conn, $user_id);
$cost        = (int)$ph['req_points'];
$message     = '';
$status      = ''; // 'success', 'error', 'owned'

if ($owned) {
    $status = 'owned';
    $message = "Kamu sudah merekrut " . htmlspecialchars($ph['name']) . " sebelumnya.";
} elseif ($user_points < $cost) {
    $status = 'error';
    $message = "Poin kamu belum cukup untuk merecrute " . htmlspecialchars($ph['name']) . ". Butuh {$cost} poin. poin kamu sekarang {$user_points}.";
} else {
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
        $status = 'success';
        $sisa = getUserPoints($conn, $user_id);
        $message = "Selamat! Kamu berhasil merekrut " . htmlspecialchars($ph['name']) . ". Poin yang terpakai: {$cost}. Poin tersisa: {$sisa}.";
    } catch (Exception $e) {
        $conn->rollback();
        $status = 'error';
        $message = "Terjadi kesalahan saat merekrut phoenix: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekrut Phoenix</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #b5b5b5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-container {
            background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%);
            border-radius: 30px;
            padding: 50px 60px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .message-text {
            font-size: 18px;
            line-height: 1.6;
            color: #1a1a1a;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .back-link {
            display: inline-block;
            color: #4a90e2;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #357abd;
            text-decoration: underline;
        }

        /* Success state */
        .modal-container.success .message-text {
            color: #2c5f2d;
        }

        /* Error state */
        .modal-container.error .message-text {
            color: #8b0000;
        }

        /* Owned state */
        .modal-container.owned .message-text {
            color: #4a4a4a;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modal-container {
                padding: 40px 30px;
            }

            .message-text {
                font-size: 16px;
            }

            .back-link {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="modal-container <?php echo $status; ?>">
        <p class="message-text"><?php echo $message; ?></p>
        <a href="recruit.php" class="back-link">Kembali ke daftar rekrut</a>
    </div>
</body>
</html>