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

        .nav-menu a:hover,
        .nav-menu a.active {
            color: red;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh; s
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
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }


        header {
            background-color: rgb(175, 72, 38);
            padding: 0;
        }

        .coin-icon {
            width: 30px;
            height: 30px;
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



        .phoenix-card {
            background: linear-gradient(to bottom, rgba(128, 128, 128, 0.95), rgba(100, 100, 100, 0.95));
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
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
            gap: .75em;
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

        @media (max-width: 1024px) {
            .phoenix-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }
        }

        @media (max-width: 768px) {
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
            }

            .container {
                padding: 0 20px;
            }

            .section-title {
                font-size: 32px;
                margin-bottom: 30px;
            }

            .phoenix-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .card-image {
                height: 250px;
            }
        }
    </style>
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