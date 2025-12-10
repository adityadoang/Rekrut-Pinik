<?php
// SESUAIKAN path dengan project kamu
require_once '../includes/auth_user.php';   // misal: 'includes/auth_user.php'
require_once '../config/db.php';          // misal: 'config/db.php'

$user_id  = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? 'Player';

if ($user_id <= 0) {
    header('Location: login_user.php');
    exit;
}

// --- TOTAL POIN USER ---
$total_points   = getUserPoints($conn, $user_id);
$points_display = str_pad($total_points, 4, '0', STR_PAD_LEFT);

// --- DATA REKOMENDASI PHOENIX ---
// ambil dari tabel phoenix (sama seperti data di halaman rekrut)
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
    // kalau tidak ada gambar di DB, pakai gambar default
    if (!empty($row['image'])) {
        // dari /user/ ke /uploads/phoenix/
        $imagePath = '../uploads/phoenix/' . $row['image'];
    } else {
        // sesuaikan dengan lokasi gambar default kamu
        $imagePath = '../assets/img/phoenix-card.jpeg';
        // atau kalau file default ada di /user/:
        // $imagePath = 'phoenix-card.jpeg';
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
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><img src= "../assets/index-logo.png"></div>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="active">Home</a></li>
                <li><a href="quests.php">Quest</a></li>
                <li><a href="recruit.php">Rekrut</a></li>
                <li><a href="profile.php">Profil</a></li>
                <li><a href="admin/login_admin.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <main class="hero-section">
        <div class="content-wrapper">
            <div class="left">
                <div class="points-card">
                    <div class="coin-icon"><img src="../assets/point.png"></div>
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
                    <p>Belum ada phoenix yang tersedia.</p>
                <?php else: ?>
                    <?php foreach ($phoenix_list as $p): ?>
                        <div class="phoenix-card">
                            <div class="card-image">
                                <img src="<?php echo htmlspecialchars($p['image']); ?>"
                                     alt="<?php echo htmlspecialchars($p['name']); ?>">
                                <button class="favorite-btn" type="button">
                                    <span class="heart">♥</span>
                                </button>
                            </div>
                            <div class="card-footer">
                                <h3 class="card-title">
                                    <?php echo htmlspecialchars($p['name']); ?>
                                </h3>
                                <div class="price-tag">
                                    <div class="coin-icon"><img src="../assets/point.png"></div>
                                    <span class="price">
                                        <?php echo htmlspecialchars($p['price']); ?>
                                    </span>
                                </div>
                                <!-- optional: tombol rekrut langsung -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>
