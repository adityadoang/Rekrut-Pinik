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
$is_correct = false;
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
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quest - Level <?= (int)$quest_id ?></title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #2d2d2d;
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Level Header */
.level-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
    color: #b8b8ff;
}

.level-icon {
    width: 30px;
    height: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.level-header h1 {
    font-size: 1.5em;
    font-weight: 500;
}

/* Quest Container */
.quest-container {
    border: 3px dashed #667eea;
    border-radius: 30px;
    padding: 3px;
}

/* Hero Section */
.hero-section {
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><defs><linearGradient id="sky" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" style="stop-color:%23ff9a56;stop-opacity:1" /><stop offset="50%" style="stop-color:%23ff6b9d;stop-opacity:1" /><stop offset="100%" style="stop-color:%23764ba2;stop-opacity:1" /></linearGradient></defs><rect fill="url(%23sky)" width="1200" height="600"/></svg>');
    background-size: cover;
    background-position: center;
    border-radius: 30px 30px 0 0;
    padding: 80px 50px;
    position: relative;
    overflow: hidden;
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
}

.hero-content h2 {
    font-size: 2.5em;
    margin-bottom: 30px;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
    line-height: 1.4;
}

.btn-mulai {
    background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
    color: white;
    padding: 18px 80px;
    border: none;
    border-radius: 30px;
    font-size: 1.3em;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
    transition: all 0.3s;
}

.btn-mulai:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(255, 107, 53, 0.6);
}

/* Question Section */
.question-section {
    background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%);
    border-radius: 0 0 30px 30px;
    padding: 60px 80px;
    min-height: 500px;
}

.question-section h3 {
    font-size: 1.8em;
    color: #2d2d2d;
    margin-bottom: 40px;
}

.options-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

.option-label {
    display: flex;
    align-items: center;
    background: white;
    padding: 20px 30px;
    border-radius: 15px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.option-label:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.option-label input[type="radio"] {
    appearance: none;
    width: 30px;
    height: 30px;
    border: 3px solid #ff8c42;
    border-radius: 50%;
    margin-right: 15px;
    position: relative;
    cursor: pointer;
    flex-shrink: 0;
}

.option-label input[type="radio"]:checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    background-color: #ff8c42;
    border-radius: 50%;
}

.option-text {
    font-size: 1.1em;
    color: #2d2d2d;
}

.btn-lanjut {
    background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
    color: white;
    padding: 15px 60px;
    border: none;
    border-radius: 25px;
    font-size: 1.2em;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.3);
    transition: all 0.3s;
}

.btn-lanjut:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 25px rgba(255, 107, 53, 0.5);
}

.btn-lanjut:disabled {
    background: #cccccc;
    cursor: not-allowed;
    box-shadow: none;
}

/* Result Section */
.result-section {
    background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%);
    border-radius: 0 0 30px 30px;
    padding: 60px 80px;
    text-align: center;
}

.result-message {
    font-size: 2em;
    margin-bottom: 30px;
    color: #2d2d2d;
}

.result-message.correct {
    color: #28a745;
}

.result-message.incorrect {
    color: #dc3545;
}

.result-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-back {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    transition: all 0.3s;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 25px rgba(102, 126, 234, 0.5);
}

/* Hidden class */
.hidden {
    display: none;
}

@media (max-width: 768px) {
    .hero-section {
        padding: 50px 30px;
        min-height: 300px;
    }
    
    .hero-content h2 {
        font-size: 1.8em;
    }
    
    .btn-mulai {
        padding: 15px 50px;
        font-size: 1.1em;
    }
    
    .question-section, .result-section {
        padding: 40px 30px;
    }
    
    .options-container {
        grid-template-columns: 1fr;
    }
    
    .question-section h3 {
        font-size: 1.4em;
    }
}
</style>
</head>
<body>
<div class="container">
    <div class="level-header">
        <div class="level-icon">◆</div>
        <h1>level <?= (int)$quest_id ?></h1>
    </div>
    
    <div class="quest-container">
        <?php if (!$is_submitted): ?>
        <!-- Hero Section (tampil sebelum klik Mulai) -->
        <div id="heroSection" class="hero-section">
            <div class="hero-content">
                <h2>Menangkan Question untuk mendapatkan<br>banyak poin</h2>
                <button type="button" class="btn-mulai" onclick="startQuest()">Mulai</button>
            </div>
        </div>
        
        <!-- Question Section (tampil setelah klik Mulai) -->
        <div id="questionSection" class="question-section hidden">
            <form method="post" id="questForm">
                <input type="hidden" name="quest_id" value="<?= (int)$quest_id ?>">
                
                <h3><?= nl2br(htmlspecialchars($quest['question_text'])) ?></h3>
                
                <div class="options-container">
                    <label class="option-label">
                        <input type="radio" name="answer" value="A" required>
                        <span class="option-text"><?= htmlspecialchars($quest['option_a']) ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="answer" value="B" required>
                        <span class="option-text"><?= htmlspecialchars($quest['option_b']) ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="answer" value="C" required>
                        <span class="option-text"><?= htmlspecialchars($quest['option_c']) ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="answer" value="D" required>
                        <span class="option-text"><?= htmlspecialchars($quest['option_d']) ?></span>
                    </label>
                </div>
                
                <button type="submit" class="btn-lanjut">Lanjut</button>
            </form>
        </div>
        
        <?php else: ?>
        <!-- Result Section (tampil setelah submit jawaban) -->
        <div class="hero-section">
            <div class="hero-content">
                <h2>Quest Selesai!</h2>
            </div>
        </div>
        
        <div class="result-section">
            <div class="result-message <?= $is_correct ? 'correct' : 'incorrect' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
            
            <div class="result-actions">
                <a href="quests.php" class="btn-back">Kembali ke Quest</a>
                <a href="dashboard.php" class="btn-back">Dashboard</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function startQuest() {
    document.getElementById('heroSection').classList.add('hidden');
    document.getElementById('questionSection').classList.remove('hidden');
}
</script>
</body>
</html>