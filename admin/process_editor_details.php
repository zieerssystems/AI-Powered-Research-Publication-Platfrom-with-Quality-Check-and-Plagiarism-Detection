<?php
include(__DIR__ . "/../include/db_connect.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "alert('Unauthorized access!'); window.location.href='admin-login.php';";
    exit();
}

$approved_editors = getEditorCount($conn, 'approved');
// $rejected_editors = getEditorCount($conn, 'rejected');
$pending_editors = getEditorCount($conn, 'pending');
$total_editors = getTotalEditorCount($conn);
$editors = getAllEditors($conn);
?>
