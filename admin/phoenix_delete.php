<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: phoenix_list.php");
    exit;
}

$id = (int)$_GET['id'];

$conn->begin_transaction();

try {
    $stmt1 = $conn->prepare("DELETE FROM user_phoenix WHERE phoenix_id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("DELETE FROM phoenix WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();

}

header("Location: phoenix_list.php");
exit;
