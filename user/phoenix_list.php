<?php
include '../includes/auth_user.php';
require_once '../config/db.php';

$phoenix = $conn->query("SELECT * FROM phoenix");
?>
<h2>Perekrutan Phoenix</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>Nama</th>
        <th>Element</th>
        <th>Req Element Power</th>
        <th>Req Intelligence</th>
        <th>Aksi</th>
    </tr>
    <?php while ($p = $phoenix->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['element']) ?></td>
        <td><?= (int)$p['req_element_power'] ?></td>
        <td><?= (int)$p['req_intelligence'] ?></td>
        <td><a href="recruit.php?id=<?= $p['id'] ?>">Rekrut</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="dashboard.php">Kembali ke Dashboard</a>
