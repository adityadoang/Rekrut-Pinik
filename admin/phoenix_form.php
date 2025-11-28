<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$name = $element = $description = '';
$req_element_power = $req_intelligence = 0;

if ($id > 0) {
    $res = $conn->query("SELECT * FROM phoenix WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $data = $res->fetch_assoc();
        $name = $data['name'];
        $element = $data['element'];
        $req_element_power = $data['req_element_power'];
        $req_intelligence = $data['req_intelligence'];
        $description = $data['description'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $element = $_POST['element'];
    $req_element_power = (int)$_POST['req_element_power'];
    $req_intelligence = (int)$_POST['req_intelligence'];
    $description = $conn->real_escape_string($_POST['description']);

    if ($id > 0) {
        $sql = "
        UPDATE phoenix SET
            name = '$name',
            element = '$element',
            req_element_power = $req_element_power,
            req_intelligence = $req_intelligence,
            description = '$description'
        WHERE id = $id
        ";
    } else {
        $sql = "
        INSERT INTO phoenix
        (name, element, req_element_power, req_intelligence, description)
        VALUES
        ('$name', '$element', $req_element_power, $req_intelligence, '$description')
        ";
    }

    if ($conn->query($sql)) {
        header("Location: phoenix_list.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<h2><?= $id > 0 ? 'Edit' : 'Tambah' ?> Phoenix</h2>
<form method="POST">
    <label>Nama Phoenix</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required><br><br>

    <label>Element</label><br>
    <select name="element" required>
        <option value="">--Pilih--</option>
        <option value="fire" <?= $element=='fire'?'selected':'' ?>>Fire</option>
        <option value="water" <?= $element=='water'?'selected':'' ?>>Water</option>
        <option value="ice" <?= $element=='ice'?'selected':'' ?>>Ice</option>
        <option value="wind" <?= $element=='wind'?'selected':'' ?>>Wind</option>
        <option value="earth" <?= $element=='earth'?'selected':'' ?>>Earth</option>
    </select><br><br>

    <label>Req Element Power</label><br>
    <input type="number" name="req_element_power" value="<?= (int)$req_element_power ?>"><br>

    <label>Req Intelligence</label><br>
    <input type="number" name="req_intelligence" value="<?= (int)$req_intelligence ?>"><br><br>

    <label>Deskripsi</label><br>
    <textarea name="description"><?= htmlspecialchars($description) ?></textarea><br><br>

    <button type="submit">Simpan</button>
</form>
<a href="phoenix_list.php">Kembali</a>
