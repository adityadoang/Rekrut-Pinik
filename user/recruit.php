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
    <title>Rekrut Phoenix</title>
</head>
<body>

<h1>Rekrut Phoenix</h1>

<p>Halo, <?php echo htmlspecialchars($username); ?></p>
<p>Total poin kamu: <?php echo htmlspecialchars($points_display); ?></p>

<hr>

<?php if (empty($phoenix_list)): ?>

    <p>Belum ada Phoenix yang dapat direkrut.</p>

<?php else: ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Biaya Poin</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php foreach ($phoenix_list as $p): ?>
            <tr>
                <td>
                    <?php
                    if (!empty($p['image'])) {
                        $img_src = '../uploads/phoenix/' . $p['image'];
                    } else {
                        $img_src = '../assets/img/phoenix-default.png';
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($img_src); ?>"
                         alt="<?php echo htmlspecialchars($p['name']); ?>"
                         width="80">
                </td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo (int)$p['req_points']; ?> poin</td>
                <td>
                    <?php if ($p['owned']): ?>
                        Sudah direkrut
                    <?php else: ?>
                        Belum dimiliki
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($p['owned']): ?>
                        -
                    <?php else: ?>
                        <a href="rekrut_pros.php?id=<?php echo (int)$p['id']; ?>">Rekrut</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php endif; ?>

<p><a href="dashboard.php">Kembali ke Dashboard</a></p>

</body>
</html>
