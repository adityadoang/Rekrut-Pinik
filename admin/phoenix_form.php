<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$name        = '';
$description = '';
$req_points  = 0;
$image       = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name        = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$req_points  = isset($_POST['req_points']) ? (int)$_POST['req_points'] : 0;

$old_image = $_POST['old_image'] ?? '';
$image     = $old_image;

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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id > 0 ? 'Edit Phoenix' : 'Tambah Pheonik'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #b5b5b5;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
        }

        h2 {
            font-size: 36px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 40px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"],
        textarea,
        input[type="number"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #1a1a1a;
            border-radius: 12px;
            background: white;
            font-size: 14px;
            font-family: 'Arial', sans-serif;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #4a4a4a;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input[type="number"] {
            width: 150px;
        }

        /* File Upload Styling */
        .file-upload-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 8px;
        }

        input[type="file"] {
            display: none;
        }

        .file-label {
            padding: 10px 20px;
            border: 2px solid #1a1a1a;
            border-radius: 12px;
            background: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .file-label:hover {
            background: #f5f5f5;
        }

        .file-name {
            font-size: 14px;
            color: #4a4a4a;
        }

        .current-image {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #4a4a4a;
        }

        .current-image img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #1a1a1a;
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #8b0000 0%, #5a0000 100%);
            color: white;
            padding: 14px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            width: fit-content;
            text-transform: lowercase;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 0, 0, 0.4);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #0066cc;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #0052a3;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            h2 {
                font-size: 28px;
                margin-bottom: 30px;
            }

            input[type="text"],
            textarea,
            input[type="number"] {
                font-size: 16px;
            }

            .file-upload-wrapper {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo $id > 0 ? 'Edit Pheonik' : 'Tambah Pheonik'; ?></h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
            <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($image); ?>">

            <div class="form-group">
                <label>Nama Pheonik</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="form-group">
                <label>Req Points (biaya merekrut)</label>
                <input type="number" name="req_points" value="<?php echo (int)$req_points; ?>" min="0" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="form-group">
                <label>Gambar</label>
                <?php if (!empty($image)): ?>
                    <div class="current-image">
                        <img src="../uploads/phoenix/<?php echo htmlspecialchars($image); ?>" alt="Phoenix">
                        <span>Gambar saat ini: <?php echo htmlspecialchars($image); ?></span>
                    </div>
                <?php endif; ?>
                <div class="file-upload-wrapper">
                    <label for="file-input" class="file-label">Browse.......</label>
                    <span class="file-name" id="file-name">No File Selected</span>
                    <input type="file" id="file-input" name="image" accept="image/*">
                </div>
            </div>

            <button type="submit">simpan</button>
        </form>
        <a href="phoenix_list.php" class="back-link">Kembali</a>
    </div>

    <script>
        // Update file name display
        document.getElementById('file-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No File Selected';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>
</html>