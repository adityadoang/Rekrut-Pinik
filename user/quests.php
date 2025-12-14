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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quest | Phoenix</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    overflow-x: hidden;
}

body {
    font-family: 'Arial', sans-serif;
    background: gray;
    min-height: 100vh;
    padding-bottom: 50px;
    overflow-x: hidden;
}

header {
    background-color: rgb(175, 72, 38);
    padding: 0;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 50px;
    max-width: 1400px;
    margin: 0 auto;
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: white;
}

.logo img {
    width: 40px;
    height: 40px;
    object-fit: contain;
}

.nav-menu {
    display: flex;
    list-style: none;
    gap: 40px;
}

.nav-menu a {
    text-decoration: none;
    color: white;
    font-size: 18px;
    font-weight: 500;
    transition: color 0.3s;
}

.nav-menu a:hover,
.nav-menu a.active {
    color: #ff6600;
}

.logout-btn {
    background-color: #dc3545;
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.logout-btn:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

/* Header Section */
.header-section {
    text-align: center;
    padding: 60px 20px 40px;
    color: white;
    background: url("../assets/phoenix-card.jpeg") no-repeat;
    background-position: center -30em;
    background-size: cover;
    border-radius: 0 0 120px 120px;
    margin-bottom: 2em;
    height: 100vh;
    position: relative;
}

.header-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 0 0 120px 120px;
}

.header-section > * {
    position: relative;
    z-index: 1;
}

.header-section h1 {
    font-size: 4em;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.header-section p {
    font-size: 1.1em;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Points Display */
.points-container {
    max-width: 600px;
    margin: 30px auto;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.points-container h2 {
    color: white;
    font-size: 1.5em;
    margin-bottom: 15px;
}

.points-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.coin-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.coin-icon img {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: cover;
}

.points-number {
    font-size: 3em;
    font-weight: bold;
    color: #ff6b35;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

/* Quest Cards Container */
.quests-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.quest-card {
    background: url("../assets/phoenix-icon.jpeg");
    background-size: cover;
    background-position: center;
    border-radius: 0 120px 120px 0;
    padding: 50px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    min-height: 250px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    width: 100%;
    max-width: 640px;
    transition: all 0.3s ease;
}

.quest-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

.quest-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
}

.quest-content {
    position: relative;
    z-index: 2;
    color: white;
}

.quest-content h3 {
    font-size: 2em;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.quest-description {
    font-size: 1.2em;
    margin-bottom: 30px;
    line-height: 1.6;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.start-btn {
    background-color: #ff6b35;
    color: white;
    padding: 15px 50px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1em;
    display: inline-block;
    transition: all 0.3s;
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.4);
}

.start-btn:hover {
    background-color: #ff5722;
    transform: translateY(-3px);
    box-shadow: 0 7px 25px rgba(255, 107, 53, 0.6);
}

.no-quests {
    text-align: center;
    color: white;
    font-size: 1.2em;
    padding: 50px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 20px;
}

.back-link {
    text-align: center;
    margin-top: 40px;
    padding: 0 20px;
}

.back-link a {
    color: white;
    text-decoration: none;
    font-size: 1.1em;
    padding: 12px 35px;
    border: 2px solid white;
    border-radius: 25px;
    display: inline-block;
    transition: all 0.3s;
}

.back-link a:hover {
    background-color: white;
    color: rgb(175, 72, 38);
}

/* ==================== RESPONSIVE ==================== */

/* Tablet (768px - 1024px) */
@media (max-width: 1024px) {
    .navbar {
        padding: 20px 30px;
    }
    
    .header-section {
        height: 80vh;
        background-position: center -20em;
        border-radius: 0 0 80px 80px;
    }
    
    .header-section h1 {
        font-size: 3em;
    }
    
    .quest-card {
        border-radius: 0 80px 80px 0;
        padding: 40px;
    }
    
    .quest-content h3 {
        font-size: 1.8em;
    }
}

/* Mobile Large (600px - 768px) */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        gap: 15px;
        padding: 15px 20px;
    }
    
    .nav-menu {
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        font-size: 16px;
    }
    
    .logo img {
        width: 35px;
        height: 35px;
    }
    
    .header-section {
        height: 70vh;
        padding: 40px 15px 30px;
        background-position: center -10em;
        border-radius: 0 0 60px 60px;
        margin-bottom: 1.5em;
    }
    
    .header-section h1 {
        font-size: 2.5em;
        margin-bottom: 15px;
    }
    
    .header-section p {
        font-size: 1em;
        padding: 0 15px;
    }
    
    .points-container {
        max-width: 90%;
        padding: 25px;
        border-radius: 40px;
        margin: 25px auto;
    }
    
    .points-container h2 {
        font-size: 1.3em;
    }
    
    .coin-icon {
        width: 40px;
        height: 40px;
    }
    
    .points-number {
        font-size: 2.5em;
    }
    
    .quests-container {
        padding: 0 15px;
    }
    
    .quest-card {
        border-radius: 0 60px 60px 0;
        padding: 30px 20px;
        min-height: 200px;
        margin-left: 0 !important;
        margin-bottom: 20px;
    }
    
    .quest-content h3 {
        font-size: 1.5em;
        margin-bottom: 15px;
    }
    
    .start-btn {
        padding: 12px 40px;
        font-size: 1em;
    }
    
    .back-link a {
        font-size: 1em;
        padding: 10px 30px;
    }
}

/* Mobile Medium (480px - 600px) */
@media (max-width: 600px) {
    .navbar {
        padding: 12px 15px;
        gap: 12px;
    }
    
    .nav-menu {
        gap: 12px;
        font-size: 14px;
    }
    
    .header-section {
        height: 65vh;
        padding: 30px 12px 25px;
        background-position: center -5em;
        border-radius: 0 0 50px 50px;
    }
    
    .header-section h1 {
        font-size: 2em;
    }
    
    .header-section p {
        font-size: 0.95em;
    }
    
    .points-container {
        padding: 20px;
        border-radius: 35px;
        margin: 20px auto;
    }
    
    .points-container h2 {
        font-size: 1.2em;
    }
    
    .coin-icon {
        width: 35px;
        height: 35px;
    }
    
    .points-number {
        font-size: 2em;
    }
    
    .quest-card {
        border-radius: 0 50px 50px 0;
        padding: 25px 18px;
        min-height: 180px;
    }
    
    .quest-content h3 {
        font-size: 1.3em;
    }
    
    .start-btn {
        padding: 10px 35px;
        font-size: 0.95em;
    }
}

/* Mobile Small (< 480px) */
@media (max-width: 480px) {
    .navbar {
        padding: 10px 12px;
    }
    
    .logo img {
        width: 30px;
        height: 30px;
    }
    
    .nav-menu {
        gap: 10px;
        font-size: 13px;
    }
    
    .header-section {
        height: 60vh;
        padding: 25px 10px 20px;
        background-position: center 0;
        border-radius: 0 0 40px 40px;
    }
    
    .header-section h1 {
        font-size: 1.8em;
    }
    
    .header-section p {
        font-size: 0.9em;
        line-height: 1.5;
    }
    
    .points-container {
        padding: 18px;
        border-radius: 30px;
        margin: 18px auto;
    }
    
    .points-container h2 {
        font-size: 1.1em;
        margin-bottom: 12px;
    }
    
    .coin-icon {
        width: 30px;
        height: 30px;
    }
    
    .points-number {
        font-size: 1.8em;
    }
    
    .quests-container {
        padding: 0 10px;
    }
    
    .quest-card {
        border-radius: 0 40px 40px 0;
        padding: 20px 15px;
        min-height: 160px;
        margin-bottom: 18px;
    }
    
    .quest-content h3 {
        font-size: 1.2em;
        margin-bottom: 12px;
    }
    
    .start-btn {
        padding: 10px 30px;
        font-size: 0.9em;
        border-radius: 25px;
    }
    
    .back-link {
        margin-top: 30px;
    }
    
    .back-link a {
        font-size: 0.95em;
        padding: 10px 25px;
    }
}

/* Mobile Extra Small (< 360px) */
@media (max-width: 360px) {
    .nav-menu {
        font-size: 12px;
        gap: 8px;
    }
    
    .header-section {
        height: 55vh;
    }
    
    .header-section h1 {
        font-size: 1.5em;
    }
    
    .header-section p {
        font-size: 0.85em;
    }
    
    .points-number {
        font-size: 1.6em;
    }
    
    .quest-content h3 {
        font-size: 1.1em;
    }
    
    .start-btn {
        padding: 8px 25px;
        font-size: 0.85em;
    }
}
</style>
</head>
<body>
<header>
<nav class="navbar">
    <div class="logo"><img src= "../assets/index-logo.png"></div>
    <ul class="nav-menu">
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="quests.php" class="active">Quest</a></li>
        <li><a href="recruit.php">Rekrut</a></li>
        <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
</nav>
</header>

<div class="header-section">
    <h1>Quest</h1>
    <p>jawab pertanyaan untuk menambah poin sebanyak-banyaknya<br>dan tukar poin untuk mendapatkan phoenix</p>
    <div class="points-container">
    <h2>Total Poin</h2>
    <div class="points-display">
        <div class="coin-icon"><img src="../assets/point.png"></div>
        <div class="points-number"><?php echo htmlspecialchars($points_display); ?></div>
    </div>
</div>

</div>


<div class="quests-container">
    <?php if (empty($quests)): ?>
        <p class="no-quests">Belum ada quest tersedia.</p>
    <?php else: ?>
        <?php
        $level = 1;
        foreach ($quests as $q):
        ?>
        <div class="quest-card" style="margin-left: <?= ($level - 1) * 10 ?>em;">
            <div class="quest-content">
                <h3>Menangkan <?php echo ($level === 1) ? 'Question' : 'level ' . $level; ?> untuk mendapatkan banyak poin</h3>
                <a href="quest_do.php?id=<?php echo (int)$q['id']; ?>" class="start-btn">Mulai</a>
            </div>
        </div>
        <?php
        $level++;
        endforeach;
        ?>
    <?php endif; ?>
</div>

<div class="back-link">
    <a href="dashboard.php">Kembali ke Dashboard</a>
</div>
</body>
</html>