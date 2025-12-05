<?php
require_once '../includes/auth_user.php';
require_once '../config/db.php';

$user_id  = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? 'Player';

if ($user_id <= 0) {
    header('Location: login_user.php');
    exit;
}

// ambil total poin user
$total_points   = getUserPoints($conn, $user_id);
$points_display = str_pad($total_points, 4, '0', STR_PAD_LEFT);

// ambil daftar quest
$stmt = $conn->prepare("SELECT id, title, description, reward_points FROM quests ORDER BY id ASC");
$stmt->execute();
$result = $stmt->get_result();

$quests = [];
while ($row = $result->fetch_assoc()) {
    $quests[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Quest | Phoenix</title>
</head>
<body>

<nav>
    <a href="dashboard.php">Home</a> |
    <a href="quests.php">Quest</a> |
    <a href="recruit.php">Rekrute</a> |
    <a href="profil.php">Profil</a> |
    <a href="login_admin.php">Admin</a>
</nav>

<hr>

<h1>Quest</h1>
<p>Jawab pertanyaan untuk menambah poin sebanyak-banyaknya dan tukar poin untuk merekrut phoenix.</p>

<h2>Total Poin</h2>
<p><?php echo htmlspecialchars($points_display); ?></p>

<hr>

<h2>Daftar Quest</h2>

<?php if (empty($quests)): ?>

    <p>Belum ada quest tersedia.</p>

<?php else: ?>

    <ol>
        <?php
        $level = 1;
        foreach ($quests as $q):
        ?>
            <li>
                <h3>Level <?php echo $level; ?></h3>

                <p><strong>Judul:</strong>
                    <?php echo htmlspecialchars($q['title']); ?>
                </p>

                <?php if (!empty($q['description'])): ?>
                    <p><strong>Deskripsi:</strong>
                        <?php echo nl2br(htmlspecialchars($q['description'])); ?>
                    </p>
                <?php endif; ?>

                <p><strong>Reward:</strong>
                    <?php echo (int)$q['reward_points']; ?> poin
                </p>

                <p>
                    Menangkan level <?php echo $level; ?>
                    untuk mendapatkan banyak poin dan lanjut ke level
                    <?php echo $level + 1; ?>.
                </p>

                <p>
                    <a href="quest_do.php?id=<?php echo (int)$q['id']; ?>">Mulai</a>
                </p>
            </li>
        <?php
            $level++;
        endforeach;
        ?>
    </ol>

<?php endif; ?>

<hr>

<p><a href="dashboard.php">Kembali ke Dashboard</a></p>

</body>
</html>
