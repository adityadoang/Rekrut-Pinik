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
$reward_points = 0;
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
            $reward_points = (int)$quest['reward_points'];
            if ($reward_points > 0) {
                addUserPoints($conn, $user_id, $reward_points);
            }
            $message = "Selamat, poin yang kamu dapatkan: {$reward_points} poin.";
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

*:focus {
    outline: none;
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
    color: orange;
}

.level-icon {
    width: 30px;
    height: 30px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.level-header h1 {
    font-size: 1.5em;
    font-weight: 500;
}

/* Hero Section */
.hero-section {
    background: url('../assets/phoenix-bg.jpeg');
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
    padding: 80px;
    min-height: 500px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.result-card {
    background: white;
    border-radius: 0 150px 150px 0;
    padding: 60px 80px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    max-width: 700px;
    width: 100%;
}

.result-title {
    font-size: 3em;
    margin-bottom: 20px;
    color: #2d2d2d;
    font-weight: 600;
}

.result-subtitle {
    font-size: 1.3em;
    color: #666;
    margin-bottom: 30px;
}

.points-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-bottom: 50px;
}

.coin-icon {
    width: 80px;
    height: 80px;
}

.coin-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.points-number {
    font-size: 2.5em;
    font-weight: bold;
    color: #ff8c42;
}

.result-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-back {
    background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1em;
    display: inline-block;
    transition: all 0.3s;
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.3);
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 25px rgba(255, 107, 53, 0.5);
}

.btn-secondary {
    background: #2d2d2d;
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1em;
    display: inline-block;
    transition: all 0.3s;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 25px rgba(0, 0, 0, 0.3);
}

/* Incorrect Result */
.result-card.incorrect .result-title {
    color: #dc3545;
}

.result-card.incorrect .points-number {
    color: #dc3545;
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
    
    .question-section {
        padding: 40px 30px;
    }
    
    .result-section {
        padding: 40px 20px;
    }
    
    .result-card {
        padding: 40px 30px;
        border-radius: 0 80px 80px 0;
    }
    
    .result-title {
        font-size: 2em;
    }
    
    .result-subtitle {
        font-size: 1.1em;
    }
    
    .points-number {
        font-size: 2.5em;
    }
    
    .options-container {
        grid-template-columns: 1fr;
    }
    
    .question-section h3 {
        font-size: 1.4em;
    }
    
    .result-actions {
        flex-direction: column;
    }
    
    .btn-back, .btn-secondary {
        width: 100%;
        text-align: center;
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
            <div class="result-card <?= $is_correct ? 'correct' : 'incorrect' ?>">
                <h1 class="result-title"><?= $is_correct ? 'Selamat !!!' : 'Oops!' ?></h1>
                <p class="result-subtitle">Total Poin Yang didapatkan</p>
                
                <div class="points-display">
                    <div class="coin-icon">
                        <img src="../assets/point.png" alt="Coin">
                    </div>
                    <div class="points-number"><?= str_pad($reward_points, 4, '0', STR_PAD_LEFT) ?></div>
                </div>
                
                <div class="result-actions">
                    <a href="quests.php" class="btn-back">Kembali ke Quest</a>
                    <a href="dashboard.php" class="btn-secondary">Dashboard</a>
                </div>
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