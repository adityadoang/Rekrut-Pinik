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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    
    
</head>
<body>

<!-- NAVIGASI ATAS -->
<div class="topbar">
    <div class="topbar-left">
        <span class="brand">Phoenix Admin</span>
    </div>

    <nav class="topbar-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="quest_list.php">Kelola Quest</a>
        <a href="phoenix_list.php">Kelola Phoenix</a>
        <!-- kalau nanti ada menu lain, tinggal tambahkan di sini -->
    </nav>

    <div class="topbar-right">
        <span>Halo, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></strong></span>
        <span>|</span>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- KONTEN DASHBOARD -->
<div class="container">
    <h1>Dashboard Admin</h1>
    <p class="subtitle">Ringkasan cepat data Quest dan Phoenix.</p>

    <div class="cards">
        <div class="card">
            <h2>Total Quest</h2>
            <div class="number"><?= $questCount ?></div>
            <div class="card-desc">
                Jumlah seluruh quest yang tersedia untuk user.
            </div>
        </div>
        <div class="card">
            <h2>Total Phoenix</h2>
            <div class="number"><?= $phoenixCount ?></div>
            <div class="card-desc">
                Jumlah seluruh Phoenix yang bisa direkrut.
            </div>
        </div>
    </div>
</div>

</body>
</html>
