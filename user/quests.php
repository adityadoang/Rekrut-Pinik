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

body {
    font-family: 'Arial', sans-serif;
    background: gray;
    min-height: 100vh;
    padding-bottom: 50px;
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
}

.header-section h1 {
    font-size: 4em;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.header-section p {
    font-size: 1.1em;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
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
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
    font-size: 24px;
}

.coin-icon img {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: cover
}

.points-number {
    font-size: 3em;
    font-weight: bold;
    color: #ff6b35;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

/* Quest Cards */
.quests-container {
    max-width: 1200px;
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
    width: 40em;
}

.quest-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1;
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
}

.back-link {
    text-align: center;
    margin-top: 40px;
}

.back-link a {
    color: white;
    text-decoration: none;
    font-size: 1.1em;
    padding: 10px 30px;
    border: 2px solid white;
    border-radius: 25px;
    display: inline-block;
    transition: all 0.3s;
}

.back-link a:hover {
    background-color: white;
    color: #667eea;
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

@media (max-width: 768px) {
    
    .header-section h1 {
        font-size: 2.5em;
    }
    
    .quest-card {
        padding: 30px 20px;
        min-height: 200px;
    }
    
    .quest-content h3 {
        font-size: 1.5em;
    }
    
    .points-number {
        font-size: 2em;
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
                <h3>Menangkan <?php echo ($level === 1) ? 'Question' : 'level ' . $level; ?> untuk mendapatkan banyak poin<?php echo ($level === 1) ? '' : ' dan lanjut ke level ' . ($level + 1); ?></h3>
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