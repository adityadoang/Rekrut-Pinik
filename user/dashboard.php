<?php
require_once '../includes/auth_user.php';
require_once '../config/db.php';

$user_id  = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? 'Player';

if ($user_id <= 0) {
    header('Location: login_user.php');
    exit;
}

$total_points   = getUserPoints($conn, $user_id);
$points_display = str_pad($total_points, 4, '0', STR_PAD_LEFT);

$sql = "
    SELECT id, name, req_points, image
    FROM phoenix
    ORDER BY id ASC
    LIMIT 12
";
$result = $conn->query($sql);

$phoenix_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['image'])) {
            $imagePath = '../uploads/phoenix/' . $row['image'];
        } else {
            $imagePath = '../assets/phoenix-card.jpeg';
        }

        $phoenix_list[] = [
            'id'    => (int)$row['id'],
            'name'  => $row['name'],
            'image' => $imagePath,
            'price' => str_pad((int)$row['req_points'], 4, '0', STR_PAD_LEFT),
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phoenix Home</title>
    <link rel="stylesheet" href="../assets/style.css?v=<?php echo time(); ?>">
    <style>
        /* FORCE 4 COLUMNS - Inline CSS untuk override */
        .phoenix-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 25px !important;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><img src="../assets/index-logo.png" alt="Logo"></div>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="active">Home</a></li>
                <li><a href="quests.php">Quest</a></li>
                <li><a href="recruit.php">Rekrut</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="hero-section">
        <div class="content-wrapper">
            <div class="left">
                <div class="points-card">
                    <div class="coin-icon"><img src="../assets/point.png" alt="Coin"></div>
                    <div class="points-amount">
                        <?php echo htmlspecialchars($points_display); ?>
                    </div>
                </div>
                <div class="cta-card">
                    <p class="cta-text">Coba kuis untuk<br>dapatkan poin lebih<br>banyak</p>
                </div>
            </div>
            <div class="phoenix-circle">
                <img src="../assets/phoenix-cta.jpeg" alt="Phoenix" class="phoenix-icon">
            </div>
        </div>
    </main>

    <section class="recommendation-section" id="rekomendasi">
        <div class="container">
            <h2 class="section-title">Rekomendasi</h2>

            <div class="phoenix-grid" id="phoenixGrid">
                <?php if (empty($phoenix_list)): ?>
                    <p style="grid-column: 1/-1; text-align: center;">Belum ada phoenix yang tersedia.</p>
                <?php else: ?>
                    <?php foreach ($phoenix_list as $p): ?>
                        <div class="phoenix-card">
                            <div class="card-image">
                                <img src="<?php echo htmlspecialchars($p['image']); ?>"
                                     alt="<?php echo htmlspecialchars($p['name']); ?>"
                                     onerror="this.src='../assets/phoenix-card.jpeg'">
                                <button class="favorite-btn" type="button">
                                    <span class="heart">♥</span>
                                </button>
                            </div>
                            <div class="card-footer">
                                <h3 class="card-title">
                                    <?php echo htmlspecialchars($p['name']); ?>
                                </h3>
                                <div class="price-tag">
                                    <div class="coin-icon"><img src="../assets/point.png" alt="Coin"></div>
                                    <span class="price">
                                        <?php echo htmlspecialchars($p['price']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        // Toggle favorite
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const heart = this.querySelector('.heart');
                if (heart.style.color === 'rgb(255, 68, 68)') {
                    heart.style.color = '#ccc';
                } else {
                    heart.style.color = '#ff4444';
                }
            });
        });
    </script>
</body>
</html>