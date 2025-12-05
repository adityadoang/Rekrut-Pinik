<?php
include '../includes/auth_user.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'] ?? 0;
$quest_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    die("User tidak valid.");
}

if ($quest_id <= 0 && isset($_POST['quest_id'])) {
    $quest_id = (int)$_POST['quest_id'];
}

if ($quest_id <= 0) {
    die("Quest tidak ditemukan.");
}

// Ambil data quest
$stmt = $conn->prepare("SELECT * FROM quests WHERE id = ?");
$stmt->bind_param('i', $quest_id);
$stmt->execute();
$quest = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$quest) {
    die("Quest tidak ditemukan.");
}

$message = '';
$is_submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer = $_POST['answer'] ?? null;

    if (!$answer) {
        $message = "Silakan pilih jawaban terlebih dahulu.";
    } else {
        $is_correct = ($answer === $quest['correct_option']) ? 1 : 0;

        // Simpan ke user_quests
        $stmt = $conn->prepare("
            INSERT INTO user_quests (user_id, quest_id, is_correct, answered_option)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('iiis', $user_id, $quest_id, $is_correct, $answer);
        $stmt->execute();
        $stmt->close();

        // Jika benar, tambahkan poin
        if ($is_correct) {
            $reward = (int)$quest['reward_points'];
            if ($reward > 0) {
                addUserPoints($conn, $user_id, $reward);
            }
            $message = "Jawaban kamu benar! Kamu mendapatkan {$reward} poin.";
        } else {
            $message = "Jawaban kamu belum tepat. Coba lagi ya!";
        }

        $is_submitted = true;
    }
}
?>
<h2>Kerjakan Quest: <?= htmlspecialchars($quest['title']) ?></h2>
<p><?= nl2br(htmlspecialchars($quest['description'])) ?></p>
<p><strong>Reward:</strong> <?= (int)$quest['reward_points'] ?> poin</p>

<?php if ($message): ?>
    <p><strong><?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>

<?php if (!$is_submitted): ?>
<form method="post">
    <input type="hidden" name="quest_id" value="<?= (int)$quest_id ?>">

    <p><?= nl2br(htmlspecialchars($quest['question_text'])) ?></p>

    <label>
        <input type="radio" name="answer" value="A">
        A. <?= htmlspecialchars($quest['option_a']) ?>
    </label><br>
    <label>
        <input type="radio" name="answer" value="B">
        B. <?= htmlspecialchars($quest['option_b']) ?>
    </label><br>
    <label>
        <input type="radio" name="answer" value="C">
        C. <?= htmlspecialchars($quest['option_c']) ?>
    </label><br>
    <label>
        <input type="radio" name="answer" value="D">
        D. <?= htmlspecialchars($quest['option_d']) ?>
    </label><br><br>

    <button type="submit">Kirim Jawaban</button>
</form>
<?php endif; ?>

<a href="quests.php">Kembali ke Daftar Quest</a><br>
<a href="dashboard.php">Kembali ke Dashboard</a>
