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
        // UPDATE (8 string + 2 integer)
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
        // INSERT (8 string + 1 integer)
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
<h2><?= $id > 0 ? 'Edit Quest' : 'Tambah Quest' ?></h2>
<form method="post">
    <input type="hidden" name="id" value="<?= (int)$id ?>">

    <label>Judul</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required><br><br>

    <label>Deskripsi</label><br>
    <textarea name="description"><?= htmlspecialchars($description) ?></textarea><br><br>

    <label>Pertanyaan</label><br>
    <textarea name="question_text" required><?= htmlspecialchars($question_text) ?></textarea><br><br>

    <label>Opsi A</label><br>
    <input type="text" name="option_a" value="<?= htmlspecialchars($option_a) ?>" required><br>

    <label>Opsi B</label><br>
    <input type="text" name="option_b" value="<?= htmlspecialchars($option_b) ?>" required><br>

    <label>Opsi C</label><br>
    <input type="text" name="option_c" value="<?= htmlspecialchars($option_c) ?>" required><br>

    <label>Opsi D</label><br>
    <input type="text" name="option_d" value="<?= htmlspecialchars($option_d) ?>" required><br><br>

    <label>Jawaban Benar</label><br>
    <select name="correct_option">
        <option value="A" <?= $correct_option === 'A' ? 'selected' : '' ?>>A</option>
        <option value="B" <?= $correct_option === 'B' ? 'selected' : '' ?>>B</option>
        <option value="C" <?= $correct_option === 'C' ? 'selected' : '' ?>>C</option>
        <option value="D" <?= $correct_option === 'D' ? 'selected' : '' ?>>D</option>
    </select><br><br>

    <label>Reward Points</label><br>
    <input type="number" name="reward_points" value="<?= (int)$reward_points ?>" min="0" required><br><br>

    <button type="submit">Simpan</button>
</form>
<a href="quest_list.php">Kembali</a>
