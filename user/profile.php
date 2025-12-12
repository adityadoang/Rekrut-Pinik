<?php

require_once '../includes/auth_user.php';   
require_once '../config/db.php';          

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    header('Location: ../login_user.php');
    exit;
}

// ambil data user (nama & avatar)
$stmt = $conn->prepare("SELECT username, avatar, points FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$username_db   = $user['username'] ?? 'Player';
$avatar        = $user['avatar'] ?? null;
$currentPoints = (int)($user['points'] ?? 0);

// kalau mau, tetap pakai username di session
$display_name = $_SESSION['username'] ?? $username_db;

// tampilkan poin 4 digit (0000)
$points_display = str_pad($currentPoints, 4, '0', STR_PAD_LEFT);

// handle update profil (upload avatar + ganti nama) – opsional
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {

    $new_name = trim($_POST['display_name'] ?? $display_name);
    $new_avatar = $avatar;

    // proses upload avatar kalau ada file
    if (!empty($_FILES['avatar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (in_array($ext, $allowed)) {
            $uploadDir  = '../uploads/avatar/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName   = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                $new_avatar = $fileName;
            }
        }
    }

    // update ke database
    $stmt = $conn->prepare("UPDATE users SET username = ?, avatar = ? WHERE id = ?");
    $stmt->bind_param('ssi', $new_name, $new_avatar, $user_id);
    $stmt->execute();
    $stmt->close();

    // update juga di session
    $_SESSION['username'] = $new_name;

    // reload halaman supaya data baru tampil
    header('Location: profil.php');
    exit;
}

// tentukan path avatar yang akan ditampilkan
if (!empty($avatar)) {
    $avatarPath = '../uploads/avatar/' . $avatar;
} else {
    // gambar default kalau belum upload
    $avatarPath = '../assets/img/avatar-default.png'; // sesuaikan dengan filemu
}

// ambil daftar phoenix yang SUDAH direkrut user
$sql = "
    SELECT p.id, p.name, p.req_points, p.image
    FROM user_phoenix up
    JOIN phoenix p ON p.id = up.phoenix_id
    WHERE up.user_id = ?
    ORDER BY up.id DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();

$owned_phoenix = [];
while ($row = $res->fetch_assoc()) {
    $img = !empty($row['image'])
        ? '../uploads/phoenix/' . $row['image']
        : '../assets/img/phoenix-card.jpeg'; // default card

    $owned_phoenix[] = [
        'id'    => (int)$row['id'],
        'name'  => $row['name'],
        'image' => $img,
        'price' => str_pad((int)$row['req_points'], 4, '0', STR_PAD_LEFT),
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">LOGO</div>
        <ul class="nav-menu">
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="quests.php">Quest</a></li>
            <li><a href="rekrut.php">Rekrut</a></li>
            <li><a href="profil.php" class="active">Profil</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main class="profile-page">
    <!-- BAGIAN ATAS: KARTU PROFIL -->
    <section class="profile-header">
        <div class="profile-card">
            <div class="profile-avatar-wrapper">
                <!-- form upload avatar -->
                <form method="post" enctype="multipart/form-data">
                    <label class="profile-avatar-label">
                        <img src="<?php echo htmlspecialchars($avatarPath); ?>"
                             alt="Avatar" class="profile-avatar">
                        <input type="file" name="avatar" accept="image/*" style="display:none">
                    </label>
            </div>

            <div class="profile-info">
                <h2 class="profile-name">
                    <input type="text" name="display_name"
                           value="<?php echo htmlspecialchars($display_name); ?>">
                </h2>

                <div class="profile-points">
                    <span class="coin-icon">🪙</span>
                    <span class="points-amount">
                        <?php echo htmlspecialchars($points_display); ?>
                    </span>
                </div>

                <button type="submit" name="save_profile" class="edit-profile-btn">
                    Edit Profil
                </button>
                </form>
            </div>
        </div>
    </section>

    <!-- BAGIAN BAWAH: PHOENIX YANG SUDAH DIREKRUT -->
    <section class="owned-phoenix-section">
        <h2 class="section-title">Phoenix yang Direkrut</h2>

        <div class="phoenix-grid">
            <?php if (empty($owned_phoenix)): ?>
                <p>Kamu belum merekrut phoenix apa pun.</p>
            <?php else: ?>
                <?php foreach ($owned_phoenix as $p): ?>
                    <div class="phoenix-card">
                        <div class="card-image">
                            <img src="<?php echo htmlspecialchars($p['image']); ?>"
                                 alt="<?php echo htmlspecialchars($p['name']); ?>">
                        </div>
                        <div class="card-footer">
                            <h3 class="card-title">
                                <?php echo htmlspecialchars($p['name']); ?>
                            </h3>
                            <div class="price-tag">
                                <span class="coin-small">🪙</span>
                                <span class="price">
                                    <?php echo htmlspecialchars($p['price']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<script src="script.js"></script>
</body>
</html>
