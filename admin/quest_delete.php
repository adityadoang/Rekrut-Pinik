<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $conn->query("DELETE FROM quests WHERE id = $id");
}

header("Location: quest_list.php");
exit;
