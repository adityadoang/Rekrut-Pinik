<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

$result = $conn->query("SELECT * FROM phoenix ORDER BY id ASC");
?>
<h2>Daftar Phoenix</h2>
<p><a href="phoenix_form.php">+ Tambah Phoenix</a></p>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Gambar</th>
        <th>Nama</th>
        <th>Req Points</th>
        <th>Deskripsi</th>
        <th>Aksi</th>
    </tr>

    <?php while ($p = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo (int)$p['id']; ?></td>
            <td>
                <?php
                if (!empty($p['image'])) {
                    $src = '../uploads/phoenix/' . $p['image'];
                } else {
                    $src = '../assets/img/phoenix-default.png'; // siapkan gambar default kalau mau
                }
                ?>
                <img src="<?php echo htmlspecialchars($src); ?>"
                     alt="<?php echo htmlspecialchars($p['name']); ?>"
                     width="60">
            </td>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo (int)$p['req_points']; ?> poin</td>
            <td><?php echo nl2br(htmlspecialchars($p['description'])); ?></td>
            <td>
                <a href="phoenix_form.php?id=<?php echo (int)$p['id']; ?>">Edit</a> |
                <a href="phoenix_delete.php?id=<?php echo (int)$p['id']; ?>"
                   onclick="return confirm('Hapus phoenix ini?');">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<p><a href="dashboard.php">Kembali ke Dashboard</a></p>
