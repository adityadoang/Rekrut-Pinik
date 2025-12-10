<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

$result = $conn->query("SELECT * FROM quests");
?>
<h2>Daftar Quest</h2>
<a href="quest_form.php">+ Tambah Quest</a>
<table border="1" cellpadding="8">
    <tr>
        <th>Judul</th>
        <th>Reward Points</th>
        <th>Aksi</th>
    </tr>
    <?php while ($q = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($q['title']) ?></td>
        <td><?= (int)$q['reward_points'] ?></td>
        <td>
            <a href="quest_form.php?id=<?= $q['id'] ?>">Edit</a> |
            <a href="quest_delete.php?id=<?= $q['id'] ?>" onclick="return confirm('Hapus quest ini?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="dashboard.php">Kembali ke Dashboard</a>
