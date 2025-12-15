<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$title = $description = $question_text = '';
$option_a = $option_b = $option_c = $option_d = '';
$correct_option = 'A';
$reward_points = 0;

// MODE EDIT
if ($id > 0) {
    $res = $conn->query("SELECT * FROM quests WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $data = $res->fetch_assoc();
        $title          = $data['title'];
        $description    = $data['description'];
        $question_text  = $data['question_text'];
        $option_a       = $data['option_a'];
        $option_b       = $data['option_b'];
        $option_c       = $data['option_c'];
        $option_d       = $data['option_d'];
        $correct_option = $data['correct_option'];
        $reward_points  = (int)$data['reward_points'];
    }
}

// HANDLE SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title         = $_POST['title'] ?? '';
    $description   = $_POST['description'] ?? '';
    $question_text = $_POST['question_text'] ?? '';
    $option_a      = $_POST['option_a'] ?? '';
    $option_b      = $_POST['option_b'] ?? '';
    $option_c      = $_POST['option_c'] ?? '';
    $option_d      = $_POST['option_d'] ?? '';
    $correct_option = $_POST['correct_option'] ?? 'A';
    $reward_points = isset($_POST['reward_points']) ? (int)$_POST['reward_points'] : 0;

    // Pastikan cuma 1 karakter (A/B/C/D)
    $correct_option = substr($correct_option, 0, 1);

    if ($id > 0) {

        $stmt = $conn->prepare("
            UPDATE quests
               SET title = ?, description = ?, question_text = ?,
                   option_a = ?, option_b = ?, option_c = ?, option_d = ?,
                   correct_option = ?, reward_points = ?
             WHERE id = ?
        ");
        $stmt->bind_param(
            'ssssssssii',
            $title, $description, $question_text,
            $option_a, $option_b, $option_c, $option_d,
            $correct_option, $reward_points, $id
        );
    } else {

        $stmt = $conn->prepare("
            INSERT INTO quests
                (title, description, question_text,
                 option_a, option_b, option_c, option_d,
                 correct_option, reward_points)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param(
            'ssssssssi',
            $title, $description, $question_text,
            $option_a, $option_b, $option_c, $option_d,
            $correct_option, $reward_points
        );
    }

    if ($stmt->execute()) {
        $stmt->close();
        header('Location: quest_list.php');
        exit;
    } else {
        echo "Error simpan quest: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id > 0 ? 'Edit Quest' : 'Tambah Quest' ?></title>
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

        label {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"],
        textarea,
        select,
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
        select:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #4a4a4a;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        textarea {
            min-height: 80px;
            resize: vertical;
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%231a1a1a' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        input[type="number"] {
            width: 150px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
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
            select,
            input[type="number"] {
                font-size: 16px; /* Prevent zoom on iOS */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= $id > 0 ? 'Edit Quest' : 'Tambah Quest' ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?= (int)$id ?>">

            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div class="form-group">
                <label>Pertanyaan</label>
                <textarea name="question_text" required><?= htmlspecialchars($question_text) ?></textarea>
            </div>

            <div class="form-group">
                <label>Opsi A</label>
                <input type="text" name="option_a" value="<?= htmlspecialchars($option_a) ?>" required>
            </div>

            <div class="form-group">
                <label>Opsi B</label>
                <input type="text" name="option_b" value="<?= htmlspecialchars($option_b) ?>" required>
            </div>

            <div class="form-group">
                <label>Opsi C</label>
                <input type="text" name="option_c" value="<?= htmlspecialchars($option_c) ?>" required>
            </div>

            <div class="form-group">
                <label>Opsi D</label>
                <input type="text" name="option_d" value="<?= htmlspecialchars($option_d) ?>" required>
            </div>

            <div class="form-group">
                <label>Jawaban Benar</label>
                <select name="correct_option">
                    <option value="A" <?= $correct_option === 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $correct_option === 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= $correct_option === 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= $correct_option === 'D' ? 'selected' : '' ?>>D</option>
                </select>
            </div>

            <div class="form-group">
                <label>Reword Points</label>
                <input type="number" name="reward_points" value="<?= (int)$reward_points ?>" min="0" required>
            </div>

            <button type="submit">simpan</button>
        </form>
        <a href="quest_list.php" class="back-link">Kembali</a>
    </div>
</body>
</html>