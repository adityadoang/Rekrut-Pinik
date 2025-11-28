<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

$result = $conn->query("SELECT * FROM phoenix");
?>
<h2>Daftar Phoenix</h2>
<a href="phoenix_form.php">+ Tambah Phoenix</a>
<table border="1" cellpadding="8">
    <tr>
        <th>Nama</th>
        <th>Element</th>
        <th>Req Element Power</th>
        <th>Req Intelligence</th>
        <th>Aksi</th>
    </tr>
    <?php while ($p = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['element']) ?></td>
        <td><?= (int)$p['req_element_power'] ?></td>
        <td><?= (int)$p['req_intelligence'] ?></td>
        <td>
            <a href="phoenix_form.php?id=<?= $p['id'] ?>">Edit</a> |
            <a href="phoenix_delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus phoenix ini?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="dashboard.php">Kembali ke Dashboard</a>
