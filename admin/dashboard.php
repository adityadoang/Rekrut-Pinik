<?php
// admin/dashboard.php
// Cek login admin
require_once '../includes/auth_admin.php';
// Koneksi database
require_once '../config/db.php';
// Inisialisasi nilai default
$questCount   = 0;
$phoenixCount = 0;
// Hitung jumlah quest
$resultQuest = $conn->query("SELECT COUNT(*) AS total FROM quests");
if ($resultQuest) {
    $rowQuest   = $resultQuest->fetch_assoc();
    $questCount = (int)($rowQuest['total'] ?? 0);
}
// Hitung jumlah phoenix
$resultPhoenix = $conn->query("SELECT COUNT(*) AS total FROM phoenix");
if ($resultPhoenix) {
    $rowPhoenix   = $resultPhoenix->fetch_assoc();
    $phoenixCount = (int)($rowPhoenix['total'] ?? 0);
}

// Ambil daftar quest untuk tabel (hanya 10 data untuk preview)
$quests = [];
$stmtQuest = $conn->query("SELECT id, title, reward_points FROM quests ORDER BY id ASC LIMIT 10");
if ($stmtQuest) {
    while ($row = $stmtQuest->fetch_assoc()) {
        $quests[] = $row;
    }
}

// Ambil daftar phoenix untuk tabel (hanya 10 data untuk preview)
$phoenixList = [];
$stmtPhoenix = $conn->query("SELECT id, name, image, req_points, description FROM phoenix ORDER BY id ASC LIMIT 10");
if ($stmtPhoenix) {
    while ($row = $stmtPhoenix->fetch_assoc()) {
        $phoenixList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color:rgba(208, 208, 208, 0.81);
    color: #333;
    min-height: 100vh;
}

/* NAVIGASI ATAS */
.topbar {
    background: grey;
    backdrop-filter: blur(10px);
    padding: 15px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid rgba(255, 107, 53, 0.3);
}

.topbar-left .brand {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.topbar-nav {
    display: flex;
    gap: 30px;
}

.topbar-nav a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
    padding: 8px 16px;
    border-radius: 5px;
    transition: all 0.3s;
}

.topbar-nav a:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.topbar-right {
    display: flex;
    gap: 15px;
    align-items: center;
    color: #fff;
}

.topbar-right a {
    background-color: #8b0000;
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.topbar-right a:hover {
    background-color: #a00000;
    transform: translateY(-2px);
}

/* KONTEN DASHBOARD */
.container {
    padding: 40px 50px;
    max-width: 1400px;
    margin: 0 auto;
}

.container h1 {
    font-size: 48px;
    color: #fff;
    text-align: center;
    margin-bottom: 10px;
}

.subtitle {
    text-align: center;
    color: #aaa;
    margin-bottom: 40px;
    font-size: 18px;
}

/* CARDS */
.cards {
    display: flex;
    gap: 4em;
    justify-content: center;
    margin-bottom: 60px;
    margin-top: 7em;
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
    background: url("../../assets/total.png") no-repeat;
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
    content: '';
    display: block;
    position: absolute;
    background: url("../../assets/total-2.png") no-repeat;
    background-size: 20em;
    top: 0;
    left: 0;
    width: 140%;
    height: 120%;
    z-index: -2;
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
    margin-bottom: 15px;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);
}

.card-desc {
    position: relative;
    transform: skewX(3deg);
    color: rgba(255, 255, 255, 0.9);
    font-size: 14px;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

/* SECTION */
.quest-section, .phoenix-section {
    background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%);
    border-radius: 30px;
    padding: 40px;
    margin-bottom: 40px;
    border: 2px solid #999;
}

.section-header {
    text-align: center;
    margin-bottom: 30px;
}

.section-header h2 {
    font-size: 42px;
    margin-bottom: 20px;
}

.section-header h2 .kelola {
    color: #8b0000;
}

.section-header h2 .quest,
.section-header h2 .pheonik {
    color: #ff8c42;
}

.add-btn {
    background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
    color: white;
    padding: 10px 25px;
    border: none;
    border-radius: 20px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 107, 53, 0.5);
}

/* TABLE */
.table-container {
    background: white;
    border-radius: 20px;
    padding: 25px;
    border: 2px solid #ccc;
    overflow-x: auto;
}

.table-header {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 15px;
    padding: 15px 20px;
    background: #f5f5f5;
    border-radius: 10px;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 10px;
}

.quest-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 15px;
    padding: 15px 20px;
    align-items: center;
    border-bottom: 1px solid #e0e0e0;
}

.quest-row:last-child {
    border-bottom: none;
}

/* PHOENIX TABLE */
.phoenix-table-header {
    display: grid;
    grid-template-columns: 0.4fr 1fr 1.5fr 0.8fr 2.5fr 1.5fr;
    gap: 12px;
    padding: 15px 20px;
    background: #f5f5f5;
    border-radius: 10px;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 10px;
}

.phoenix-row {
    display: grid;
    grid-template-columns: 0.4fr 1fr 1.5fr 0.8fr 2.5fr 1.5fr;
    gap: 12px;
    padding: 15px 20px;
    align-items: center;
    border-bottom: 1px solid #e0e0e0;
}

.phoenix-row:last-child {
    border-bottom: none;
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: white;
}

.logo img {
    width: 40px;
}

.phoenix-image {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #ddd;
}

.quest-title, .phoenix-name, .phoenix-description {
    font-size: 15px;
    color: #333;
}

.reward-points, .phoenix-points {
    font-size: 15px;
    text-align: center;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.btn-edit {
    background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
    color: white;
    padding: 8px 20px;
    border: none;
    border-radius: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
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
    border: none;
    border-radius: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 0, 0, 0.4);
}

.view-all-btn {
    background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
    color: white;
    padding: 10px 25px;
    border: none;
    border-radius: 20px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    display: block;
    margin: 25px auto 0;
}

.view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 107, 53, 0.5);
}

/* RESPONSIVE */
@media (max-width: 1024px) {
    .cards {
        flex-direction: column;
        align-items: center;
    }
    
    .card {
        transform: skewX(0);
        border-radius: 20px;
        max-width: 400px;
        width: 100%;
    }
    
    .card h2,
    .card .number,
    .card-desc {
        transform: skewX(0);
    }
    
    .phoenix-table-header,
    .phoenix-row {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}

@media (max-width: 768px) {
    .topbar {
        flex-direction: column;
        padding: 20px;
        gap: 15px;
    }
    
    .topbar-nav {
        flex-direction: column;
        gap: 10px;
    }
    
    .container {
        padding: 20px;
    }
    
    .container h1 {
        font-size: 36px;
    }
    
    .table-header,
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
<!-- NAVIGASI ATAS -->
<div class="topbar">
    <div class="topbar-left">
        <div class="logo"><img src= "../../assets/index-logo.png"></div>
    </div>
    <nav class="topbar-nav">
        <a href="dashboard.php">Home</a>
        <a href="quest_list.php">Kelola Quest</a>
        <a href="phoenix_list.php">Kelola Phoenix</a>
    </nav>
    <div class="topbar-right">
        <span>Halo, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></strong></span>
        <span>|</span>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- KONTEN DASHBOARD -->
<div class="container">
    <h1>Admin</h1>
    <p class="subtitle">Ringkasan cepat data Quest dan Phoenix.</p>
    
    <div class="cards">
        <div class="card">
            <h2>Total Pheonix</h2>
            <div class="number"><?= str_pad($phoenixCount, 5, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="card">
            <h2>Total Quest</h2>
            <div class="number"><?= str_pad($questCount, 5, '0', STR_PAD_LEFT) ?></div>
        </div>
    </div>

    <!-- QUEST SECTION -->
    <div class="quest-section">
        <div class="section-header">
            <h2><span class="kelola">Kelola</span> <span class="quest">Quest</span></h2>
            <button class="add-btn" onclick="window.location.href='quest_form.php'">+ Tambah Quest</button>
        </div>
        
        <div class="table-container">
            <div class="table-header">
                <div>Judul</div>
                <div>Reward Poin</div>
                <div>Aksi</div>
            </div>
            
            <?php if (empty($quests)): ?>
                <div class="quest-row">
                    <div style="text-align: center; grid-column: 1/-1; padding: 30px;">
                        Belum ada quest. Silakan tambahkan quest baru.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($quests as $quest): ?>
                <div class="quest-row">
                    <div class="quest-title"><?= htmlspecialchars($quest['title']) ?></div>
                    <div class="reward-points"><?= str_pad($quest['reward_points'], 2, '0', STR_PAD_LEFT) ?></div>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="window.location.href='quest_form.php?id=<?= $quest['id'] ?>'">Edit</button>
                        <button class="btn-delete" onclick="if(confirm('Yakin hapus quest ini?')) window.location.href='quest_delete.php?id=<?= $quest['id'] ?>'">Hapus</button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <button class="view-all-btn" onclick="window.location.href='quest_list.php'">Selengkapnya</button>
    </div>

    <!-- PHOENIX SECTION -->
    <div class="phoenix-section">
        <div class="section-header">
            <h2><span class="kelola">Kelola</span> <span class="pheonik">Pheonik</span></h2>
            <button class="add-btn" onclick="window.location.href='phoenix_form.php'">+ Tambah Quest</button>
        </div>
        
        <div class="table-container">
            <div class="phoenix-table-header">
                <div>Id</div>
                <div>Gambar</div>
                <div>Nama</div>
                <div>Poin</div>
                <div>Deskripsi</div>
                <div>Aksi</div>
            </div>
            
            <?php if (empty($phoenixList)): ?>
                <div class="phoenix-row">
                    <div style="text-align: center; grid-column: 1/-1; padding: 30px;">
                        Belum ada phoenix. Silakan tambahkan phoenix baru.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($phoenixList as $phoenix): ?>
                <div class="phoenix-row">
                    <div><?= $phoenix['id'] ?></div>
                    <div>
                        <img src="../../uploads/phoenix/<?= htmlspecialchars($phoenix['image']) ?>" alt="<?= htmlspecialchars($phoenix['name']) ?>" class="phoenix-image">
                    </div>
                    <div class="phoenix-name"><?= htmlspecialchars($phoenix['name']) ?></div>
                    <div class="phoenix-points"><?= $phoenix['req_points'] ?></div>
                    <div class="phoenix-description"><?= htmlspecialchars($phoenix['description']) ?></div>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="window.location.href='phoenix_form.php?id=<?= $phoenix['id'] ?>'">Edit</button>
                        <button class="btn-delete" onclick="if(confirm('Yakin hapus phoenix ini?')) window.location.href='phoenix_delete.php?id=<?= $phoenix['id'] ?>'">Hapus</button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <button class="view-all-btn" onclick="window.location.href='phoenix_list.php'">Selengkapnya</button>
    </div>
</div>
</body>
</html>