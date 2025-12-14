<?php
include '../includes/auth_user.php';
require_once '../config/db.php';
$user_id  = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? 'Player';
if ($user_id <= 0) {
    header('Location: ../auth/login_user.php');
    exit;
}
// total poin user
$total_points   = getUserPoints($conn, $user_id);
$points_display = str_pad($total_points, 4, '0', STR_PAD_LEFT);
// ambil list phoenix + status apakah sudah direkrut user ini
$sql = "
    SELECT p.id, p.name, p.description, p.req_points, p.image,
           up.id AS owned
    FROM phoenix p
    LEFT JOIN user_phoenix up
      ON up.phoenix_id = p.id AND up.user_id = ?
    ORDER BY p.id ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$phoenix_list = [];
while ($row = $result->fetch_assoc()) {
    $phoenix_list[] = $row;
}
$stmt->close();
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

html {
    overflow-x: hidden;
}

body {
    font-family: 'Arial', sans-serif;
    background: #f5f5f5;
    min-height: 100vh;
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

.header {
    background: linear-gradient(135deg, #ff6600, #ff8533);
    color: white;
    padding: 10px 50px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.header-content {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.user-info-header {
    font-size: 24px;
}

.points-display {
    font-size: 28px;
    font-weight: bold;
    background: rgba(255, 255, 255, 0.2);
    padding: 10px 25px;
    border-radius: 25px;
}

.recommendation-section {
    padding: 60px 0;
    min-height: 100vh;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 50px;
}

.section-title {
    text-align: center;
    font-size: 42px;
    color: #333;
    margin-bottom: 50px;
    font-weight: 600;
}

.empty-message {
    text-align: center;
    font-size: 20px;
    color: #666;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.phoenix-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Force 3 kolom di desktop */
@media (min-width: 1200px) {
    .phoenix-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.coin-icon {
    width: 30px;
    height: 30px;
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

.phoenix-card {
    background: linear-gradient(to bottom, rgba(128, 128, 128, 0.95), rgba(100, 100, 100, 0.95));
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    width: 100%;
}

.phoenix-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
}

.status-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: rgba(34, 197, 94, 0.95);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
    z-index: 10;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.card-image {
    position: relative;
    width: calc(100% - 30px);
    height: 240px;
    background: #000;
    border-radius: 15px;
    margin: 15px 15px 0 15px;
    overflow: hidden;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-footer {
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 12px;
}

.card-title {
    color: #fff;
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    text-align: center;
}

.card-description {
    color: #e0e0e0;
    font-size: 14px;
    text-align: center;
    line-height: 1.5;
    min-height: 40px;
}

.price-tag {
    background: #fff;
    padding: 8px 20px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
}

.coin-small {
    font-size: 16px;
}

.price {
    color: #ff6600;
    font-weight: bold;
    font-size: 16px;
}

.action-button {
    background: linear-gradient(135deg, #ff6600, #ff8533);
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    transition: all 0.3s ease;
    display: inline-block;
    margin-top: 10px;
    box-shadow: 0 4px 15px rgba(255, 102, 0, 0.3);
}

.action-button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(255, 102, 0, 0.5);
}

.action-button.disabled {
    background: linear-gradient(135deg, #999, #777);
    cursor: not-allowed;
    box-shadow: none;
}

.action-button.disabled:hover {
    transform: none;
}

.back-link {
    display: block;
    text-align: center;
    margin-top: 40px;
    font-size: 18px;
}

.back-link a {
    color: #ff6600;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.back-link a:hover {
    color: #ff8533;
}

/* ==================== RESPONSIVE ==================== */

/* Tablet Large (900px - 1199px) - 2 kolom */
@media (max-width: 1199px) and (min-width: 900px) {
    .phoenix-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 25px;
    }
}

/* Tablet (768px - 899px) */
@media (max-width: 899px) {
    .navbar {
        padding: 20px 30px;
    }
    
    .container {
        padding: 0 30px;
    }
    
    .section-title {
        font-size: 36px;
        margin-bottom: 40px;
    }
    
    .phoenix-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px;
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
    
    .header {
        padding: 20px;
    }
    
    .header-content {
        flex-direction: column;
        gap: 15px;
    }
    
    .user-info-header {
        font-size: 20px;
    }
    
    .points-display {
        font-size: 24px;
        padding: 8px 20px;
    }
    
    .recommendation-section {
        padding: 40px 0;
    }
    
    .container {
        padding: 0 20px;
    }
    
    .section-title {
        font-size: 32px;
        margin-bottom: 30px;
    }
    
    .phoenix-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 15px;
    }
    
    .card-image {
        height: 200px;
        width: calc(100% - 20px);
        margin: 10px 10px 0 10px;
    }
    
    .card-footer {
        padding: 15px;
        gap: 10px;
    }
    
    .card-title {
        font-size: 18px;
    }
    
    .card-description {
        font-size: 13px;
        min-height: auto;
    }
    
    .status-badge {
        top: 15px;
        left: 15px;
        padding: 6px 12px;
        font-size: 12px;
    }
    
    .action-button {
        padding: 10px 25px;
        font-size: 14px;
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
    
    .container {
        padding: 0 15px;
    }
    
    .section-title {
        font-size: 28px;
        margin-bottom: 25px;
    }
    
    .phoenix-grid {
        gap: 12px;
    }
    
    .card-image {
        height: 180px;
    }
    
    .card-title {
        font-size: 16px;
    }
    
    .card-description {
        font-size: 12px;
    }
    
    .price-tag {
        padding: 6px 15px;
    }
    
    .price {
        font-size: 14px;
    }
    
    .coin-icon {
        width: 24px;
        height: 24px;
    }
}

/* Mobile Small (< 480px) - 1 kolom lebih nyaman */
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
    
    .recommendation-section {
        padding: 30px 0;
    }
    
    .section-title {
        font-size: 24px;
        margin-bottom: 20px;
    }
    
    /* 1 KOLOM untuk mobile kecil - lebih enak dilihat */
    .phoenix-grid {
        grid-template-columns: 1fr !important;
        gap: 15px;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .phoenix-card {
        border-radius: 15px;
    }
    
    .card-image {
        height: 220px;
        width: calc(100% - 20px);
        margin: 10px 10px 0 10px;
        border-radius: 12px;
    }
    
    .card-footer {
        padding: 15px;
    }
    
    .card-title {
        font-size: 18px;
    }
    
    .card-description {
        font-size: 13px;
    }
    
    .status-badge {
        top: 12px;
        left: 12px;
        padding: 6px 12px;
        font-size: 11px;
    }
    
    .action-button {
        padding: 10px 25px;
        font-size: 14px;
    }
    
    .back-link {
        margin-top: 30px;
        font-size: 16px;
    }
}

/* Mobile Extra Small (< 360px) */
@media (max-width: 360px) {
    .nav-menu {
        font-size: 12px;
        gap: 8px;
    }
    
    .section-title {
        font-size: 20px;
    }
    
    .phoenix-grid {
        max-width: 100%;
        padding: 0 5px;
    }
    
    .card-image {
        height: 200px;
    }
    
    .card-title {
        font-size: 16px;
    }
    
    .card-description {
        font-size: 12px;
    }
    
    .price {
        font-size: 13px;
    }
    
    .action-button {
        padding: 8px 20px;
        font-size: 13px;
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
                <li><a href="quests.php">Quest</a></li>
                <li><a href="recruit.php" class="active">Rekrut</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="recommendation-section">
        <div class="container">
            <h1 class="section-title">Rekrut Phoenix</h1>

            <?php if (empty($phoenix_list)): ?>
                <div class="empty-message">
                    <p>Belum ada Phoenix yang dapat direkrut.</p>
                </div>
            <?php else: ?>
                <div class="phoenix-grid">
                    <?php foreach ($phoenix_list as $p): ?>
                        <div class="phoenix-card">
                            <?php if ($p['owned']): ?>
                                <div class="status-badge">✓ Sudah Direkrut</div>
                            <?php endif; ?>

                            <div class="card-image">
                                <?php
                                if (!empty($p['image'])) {
                                    $img_src = '../uploads/phoenix/' . $p['image'];
                                } else {
                                    $img_src = '../assets/img/phoenix-default.png';
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($img_src); ?>"
                                     alt="<?php echo htmlspecialchars($p['name']); ?>">
                            </div>

                            <div class="card-footer">
                                <h3 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h3>
                                
                                <?php if (!empty($p['description'])): ?>
                                    <p class="card-description"><?php echo htmlspecialchars($p['description']); ?></p>
                                <?php endif; ?>

                                <div class="price-tag">
                                <div class="coin-icon"><img src="../assets/point.png"></div>
                                    <span class="price"><?php echo (int)$p['req_points']; ?> poin</span>
                                </div>

                                <?php if ($p['owned']): ?>
                                    <span class="action-button disabled">Sudah Dimiliki</span>
                                <?php else: ?>
                                    <a href="rekrut_pros.php?id=<?php echo (int)$p['id']; ?>" 
                                       class="action-button">Rekrut Sekarang</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="back-link">
                <a href="dashboard.php">← Kembali ke Dashboard</a>
            </div>
        </div>
    </section>
</body>
</html>