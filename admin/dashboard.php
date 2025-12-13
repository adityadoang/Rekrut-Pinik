<?php


require_once '../includes/auth_admin.php';
require_once '../config/db.php';

// ==================== DATA FETCHING ====================

$questCount = 0;
$phoenixCount = 0;
$quests = [];
$phoenixList = [];

// Hitung total quest
$resultQuest = $conn->query("SELECT COUNT(*) AS total FROM quests");
if ($resultQuest) {
    $rowQuest = $resultQuest->fetch_assoc();
    $questCount = (int)($rowQuest['total'] ?? 0);
}

// Hitung total phoenix
$resultPhoenix = $conn->query("SELECT COUNT(*) AS total FROM phoenix");
if ($resultPhoenix) {
    $rowPhoenix = $resultPhoenix->fetch_assoc();
    $phoenixCount = (int)($rowPhoenix['total'] ?? 0);
}

// TOP 5 Quest berdasarkan reward_points terbesar
$resultTopQuest = $conn->query("
    SELECT id, title, reward_points
    FROM quests
    ORDER BY reward_points DESC, id DESC
    LIMIT 5
");
if ($resultTopQuest) {
    $quests = $resultTopQuest->fetch_all(MYSQLI_ASSOC);
}

// TOP 5 Phoenix berdasarkan req_points terbesar
$resultTopPhoenix = $conn->query("
    SELECT id, name, image, req_points, description
    FROM phoenix
    ORDER BY req_points DESC, id DESC
    LIMIT 5
");
if ($resultTopPhoenix) {
    $phoenixList = $resultTopPhoenix->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kelola Quest & Phoenix</title>
    <style>
        /* ==================== RESET & BASE ==================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: rgba(208, 208, 208, 0.81);
            color: #333;
            min-height: 100vh;
        }

        /* ==================== NAVIGATION ==================== */
        .navbar {
            background: rgb(175, 72, 38);
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .logo img {
            width: 50px;
            height: 50px;
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
            transition: color 0.3s ease;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #ff6600;
        }

        /* ==================== CONTAINER ==================== */
        .container {
            padding: 40px 50px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 48px;
            color: #fff;
            text-align: center;
            margin-bottom: 10px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 18px;
        }

        /* ==================== STATISTICS CARDS ==================== */
        .cards {
            display: flex;
            gap: 4em;
            justify-content: center;
            margin: 7em 0 60px;
        }

        .card {
            padding: 5em;
            position: relative;
            scale: 1.15;
        }

        .card::after {
            content: '';
            display: block;
            position: absolute;
            background: url("../assets/total.png") no-repeat;
            background-size: 20em;
            top: 5%;
            left: -20%;
            width: 120%;
            height: 120%;
            z-index: -2;
        }

        .card:nth-child(2) {
            margin-top: 2em;
        }

        .card:nth-child(2)::after {
            background: url("../assets/total-2.png") no-repeat;
            background-size: 20em;
            top: 0;
            left: 0;
            width: 140%;
        }

        .card h2 {
            position: relative;
            transform: skewX(3deg);
            font-size: 16px;
            color: white;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .card .number {
            position: relative;
            transform: skewX(3deg);
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);
        }

        /* ==================== SECTIONS ==================== */
        .section {
            background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%);
            border-radius: 30px;
            padding: 40px;
            margin-bottom: 40px;
            border: 2px solid #999;
            scroll-margin-top: 100px;
        }

        .section-header {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 42px;
            margin: 0 0 20px 0;
            text-align: center;
        }

        .section-title .kelola {
            color: #8b0000;
        }

        .section-title .quest {
            color: #ff8c42;
        }

        .section-title .phoenix {
            color: #ff8c42;
        }

        /* ==================== BUTTONS ==================== */
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 20px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-add {
            background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
            padding: 8px 20px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 107, 53, 0.5);
        }

        .btn-edit {
            background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 15px;
            font-size: 14px;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #c62828 0%, #8b0000 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 15px;
            font-size: 14px;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 0, 0, 0.4);
        }

        /* ==================== TABLE ==================== */
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            border: 2px solid #ccc;
            overflow-x: auto;
        }

        .table-header {
            display: grid;
            padding: 15px 20px;
            background: #f5f5f5;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .table-row {
            display: grid;
            padding: 15px 20px;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        /* Quest Table Grid */
        .quest-table-header,
        .quest-row {
            grid-template-columns: 2fr 1fr 1fr;
            gap: 15px;
        }

        .quest-table-header > div:nth-child(2),
        .quest-row > div:nth-child(2) {
            text-align: center;
        }

        .quest-table-header > div:nth-child(3),
        .quest-row > div:nth-child(3) {
            text-align: center;
        }

        /* Phoenix Table Grid */
        .phoenix-table-header,
        .phoenix-row {
            grid-template-columns: 1fr 1.5fr 0.8fr 2.5fr 1.5fr; /* DIUBAH GRIDNYA KRN ID DIHILANGKAN */
            gap: 12px;
        }

        .phoenix-table-header > div:nth-child(1),
        .phoenix-row > div:nth-child(1) {
            text-align: center;
        }

        .phoenix-table-header > div:nth-child(3),
        .phoenix-row > div:nth-child(3) {
            text-align: center;
        }

        .phoenix-table-header > div:nth-child(5),
        .phoenix-row > div:nth-child(5) {
            text-align: center;
        }

        .phoenix-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            grid-column: 1/-1;
            padding: 30px;
            color: #666;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 1024px) {
            .cards {
                flex-direction: column;
                align-items: center;
            }

            .card {
                transform: skewX(0);
                max-width: 400px;
                width: 100%;
            }

            .card h2,
            .card .number {
                transform: skewX(0);
            }

            .phoenix-table-header,
            .phoenix-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                padding: 20px;
                gap: 15px;
            }

            .nav-menu {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .container {
                padding: 20px;
            }

            .page-title {
                font-size: 36px;
            }

            .quest-table-header,
            .quest-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .action-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <img src="../assets/index-logo.png" alt="Logo">
        </div>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="quest_list.php">Kelola Quest</a></li>
            <li><a href="phoenix_list.php">Kelola Phoenix</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h1 class="page-title">Admin</h1>
    <p class="subtitle">Ringkasan cepat data Quest dan Phoenix</p>

    <div class="cards">
        <div class="card">
            <h2>Total Phoenix</h2>
            <div class="number"><?= str_pad($phoenixCount, 5, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="card">
            <h2>Total Quest</h2>
            <div class="number"><?= str_pad($questCount, 5, '0', STR_PAD_LEFT) ?></div>
        </div>
    </div>

    <!-- TOP 5 QUEST -->
    <section class="section" id="kelola-quest">
        <div class="section-header">
            <h2 class="section-title">
                <span class="kelola">TOP 5</span>
                <span class="quest">Quest</span>
            </h2>
            <button class="btn btn-add" onclick="window.location.href='quest_form.php'">+ Tambah Quest</button>
        </div>

        <div class="table-container">
            <div class="table-header quest-table-header">
                <div>Judul</div>
                <div>Reward Poin</div>
                <div>Aksi</div>
            </div>

            <?php if (empty($quests)): ?>
                <div class="table-row quest-row">
                    <div class="empty-state">Belum ada quest. Silakan tambahkan quest baru.</div>
                </div>
            <?php else: ?>
                <?php foreach ($quests as $quest): ?>
                    <div class="table-row quest-row">
                        <div><?= htmlspecialchars($quest['title'] ?? '-') ?></div>
                        <div><?= str_pad((string)($quest['reward_points'] ?? '0'), 2, '0', STR_PAD_LEFT) ?></div>
                        <div class="action-buttons">
                            <button class="btn btn-edit" onclick="window.location.href='quest_form.php?id=<?= (int)$quest['id'] ?>'">Edit</button>
                            <button class="btn btn-delete" onclick="if(confirm('Yakin hapus quest ini?')) window.location.href='quest_delete.php?id=<?= (int)$quest['id'] ?>'">Hapus</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- TOP 5 PHOENIX (TANPA ID) -->
    <section class="section" id="kelola-phoenix">
        <div class="section-header">
            <h2 class="section-title">
                <span class="kelola">TOP 5</span>
                <span class="phoenix">Phoenix</span>
            </h2>
            <button class="btn btn-add" onclick="window.location.href='phoenix_form.php'">+ Tambah Phoenix</button>
        </div>

        <div class="table-container">
            <div class="table-header phoenix-table-header">
                <div>Gambar</div>
                <div>Nama</div>
                <div>Poin</div>
                <div>Deskripsi</div>
                <div>Aksi</div>
            </div>

            <?php if (empty($phoenixList)): ?>
                <div class="table-row phoenix-row">
                    <div class="empty-state">Belum ada phoenix. Silakan tambahkan phoenix baru.</div>
                </div>
            <?php else: ?>
                <?php foreach ($phoenixList as $phoenix): ?>
                    <div class="table-row phoenix-row">
                        <div>
                            <img src="../uploads/phoenix/<?= htmlspecialchars($phoenix['image'] ?? '') ?>"
                                 alt="<?= htmlspecialchars($phoenix['name'] ?? '') ?>"
                                 class="phoenix-image"
                                 onerror="this.onerror=null;this.style.display='none';">
                        </div>
                        <div><?= htmlspecialchars($phoenix['name'] ?? '-') ?></div>
                        <div><?= htmlspecialchars($phoenix['req_points'] ?? '-') ?></div>
                        <div><?= htmlspecialchars($phoenix['description'] ?? '-') ?></div>
                        <div class="action-buttons">
                            <button class="btn btn-edit" onclick="window.location.href='phoenix_form.php?id=<?= (int)$phoenix['id'] ?>'">Edit</button>
                            <button class="btn btn-delete" onclick="if(confirm('Yakin hapus phoenix ini?')) window.location.href='phoenix_delete.php?id=<?= (int)$phoenix['id'] ?>'">Hapus</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</div>
</body>
</html>
