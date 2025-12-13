<?php
include '../includes/auth_admin.php';
require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: quest_list.php");
    exit;
}

$id = (int)$_GET['id'];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn->begin_transaction();

    $dbRes = $conn->query("SELECT DATABASE() AS dbname");
    $dbRow = $dbRes->fetch_assoc();
    $dbName = $dbRow['dbname'];

    $stmtFk = $conn->prepare("
        SELECT TABLE_NAME, COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = ?
          AND REFERENCED_TABLE_NAME = 'quests'
          AND REFERENCED_COLUMN_NAME = 'id'
    ");
    $stmtFk->bind_param("s", $dbName);
    $stmtFk->execute();
    $fkRows = $stmtFk->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtFk->close();

    foreach ($fkRows as $fk) {
        $childTable = $fk['TABLE_NAME'];
        $childCol   = $fk['COLUMN_NAME'];

        $sqlChild = "DELETE FROM `{$childTable}` WHERE `{$childCol}` = ?";
        $stmtChild = $conn->prepare($sqlChild);
        $stmtChild->bind_param("i", $id);
        $stmtChild->execute();
        $stmtChild->close();
    }

    $stmt = $conn->prepare("DELETE FROM quests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows < 1) {
        $stmt->close();
        $conn->rollback();
        die("Gagal menghapus: data quest dengan ID {$id} tidak ditemukan / tidak terhapus.");
    }

    $stmt->close();
    $conn->commit();

    header("Location: quest_list.php");
    exit;

} catch (Exception $e) {
    if ($conn && $conn->errno === 0) {
    }
    if ($conn) {
        $conn->rollback();
    }
    die("Delete gagal: " . htmlspecialchars($e->getMessage()));
}