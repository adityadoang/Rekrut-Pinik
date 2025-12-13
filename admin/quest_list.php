<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

// Ambil data quest dari database
$quests = [];
$result = $conn->query("SELECT * FROM quests ORDER BY id ASC");
if ($result) {
    $quests = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quest List</title>
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

        /* ==================== SECTIONS ==================== */
        .section {
            background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%);
            border-radius: 30px;
            padding: 40px;
            margin: 40px auto;
            border: 2px solid #999;
            scroll-margin-top: 100px;
            max-width: 1400px;
        }

        .section-header {
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
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
            width: 100%;
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

            .section {
                margin: 20px;
                padding: 20px;
            }

            .section-title {
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

    <!-- ==================== NAVBAR ==================== -->
    <nav class="navbar">
        <div class="logo">
            <img src="../assets/index-logo.png" alt="Logo" onerror="this.style.display='none';">
        </div>

        <ul class="nav-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="quest_list.php" class="active">Kelola Quest</a></li>
            <li><a href="phoenix_list.php">Kelola Phoenix</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- ==================== QUEST SECTION ==================== -->
    <section class="section" id="kelola-quest">
        <div class="section-header">
            <h2 class="section-title">
                <span class="kelola">Kelola</span>
                <span class="quest">Quest</span>
            </h2>
            <button class="btn btn-add" onclick="window.location.href='quest_form.php'">
                + Tambah Quest
            </button>
        </div>

        <div class="table-container">
            <div class="table-header quest-table-header">
                <div>Judul</div>
                <div>Reward Poin</div>
                <div>Aksi</div>
            </div>

            <?php if (empty($quests)): ?>
                <div class="table-row quest-row">
                    <div class="empty-state">
                        Belum ada quest. Silakan tambahkan quest baru.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($quests as $quest): ?>
                    <div class="table-row quest-row">
                        <div><?= htmlspecialchars($quest['title'] ?? '-') ?></div>

                        <div>
                            <?= htmlspecialchars($quest['reward_points'] ?? '-') ?>
                        </div>

                        <div class="action-buttons">
                            <button class="btn btn-edit"
                                    onclick="window.location.href='quest_form.php?id=<?= (int)$quest['id'] ?>'">
                                Edit
                            </button>
                            <button class="btn btn-delete"
                                    onclick="if(confirm('Yakin hapus quest ini?')) window.location.href='quest_delete.php?id=<?= (int)$quest['id'] ?>'">
                                Hapus
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</body>
</html>
