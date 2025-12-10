<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$name        = '';
$description = '';
$req_points  = 0;
$image       = '';

// MODE EDIT
if ($id > 0) {
    $res = $conn->query("SELECT * FROM phoenix WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $data        = $res->fetch_assoc();
        $name        = $data['name'];
        $description = $data['description'];
        $req_points  = (int)$data['req_points'];
        $image       = $data['image'];
    }
}

// HANDLE SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name        = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $req_points  = isset($_POST['req_points']) ? (int)$_POST['req_points'] : 0;

    // image lama (kalau mode edit)
    $old_image = $_POST['old_image'] ?? '';
    $image     = $old_image;

    // proses upload gambar baru (opsional)
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);

        $newName = 'phoenix_' . time() . '_' . rand(1000, 9999) . '.' . $ext;

        $uploadDir  = '../uploads/phoenix/';
        $uploadPath = $uploadDir . $newName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image = $newName;
        }
    }

    if ($id > 0) {
        // UPDATE
        $stmt = $conn->prepare("
            UPDATE phoenix
               SET name = ?, description = ?, req_points = ?, image = ?
             WHERE id = ?
        ");
        $stmt->bind_param('ssisi', $name, $description, $req_points, $image, $id);
    } else {
        // INSERT
        $stmt = $conn->prepare("
            INSERT INTO phoenix (name, description, req_points, image)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('ssis', $name, $description, $req_points, $image);
    }

    if ($stmt->execute()) {
        $stmt->close();
        header('Location: phoenix_list.php');
        exit;
    } else {
        echo "Error simpan phoenix: " . $stmt->error;
    }
}
?>
<h2><?php echo $id > 0 ? 'Edit Phoenix' : 'Tambah Phoenix'; ?></h2>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
    <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($image); ?>">

    <p>
        <label>Nama</label><br>
        <input type="text" name="name"
               value="<?php echo htmlspecialchars($name); ?>" required>
    </p>

    <p>
        <label>Req Points (biaya merekrut)</label><br>
        <input type="number" name="req_points"
               value="<?php echo (int)$req_points; ?>" min="0" required>
    </p>

    <p>
        <label>Deskripsi</label><br>
        <textarea name="description" rows="4" cols="40"><?php
            echo htmlspecialchars($description);
        ?></textarea>
    </p>

    <p>
        <label>Gambar Phoenix</label><br>
        <?php if (!empty($image)): ?>
            <span>Gambar saat ini: <?php echo htmlspecialchars($image); ?></span><br>
            <img src="../uploads/phoenix/<?php echo htmlspecialchars($image); ?>"
                 alt="Phoenix" width="100"><br>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
    </p>

    <p>
        <button type="submit">Simpan</button>
    </p>
</form>

<p><a href="phoenix_list.php">Kembali</a></p>
