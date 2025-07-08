<?php
include(__DIR__ . "/../../include/db_connect.php");
include(__DIR__ . "/../../include/functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paper_id = $_POST['paper_id'];
    $editor_id = $_POST['editor_id'];
    $task_type = $_POST['task_type'];
    $decision = $_POST['decision'];

    if ($decision === 'accept') {
        updateEditorTaskStatus3($conn, $paper_id, $editor_id, $task_type, 'Accepted');
        header("Location: handle_plagiarism_update.php?paper_id=" . $paper_id);
        exit();
    } elseif ($decision === 'reject') {
        updateEditorTaskStatus3($conn, $paper_id, $editor_id, $task_type, 'Rejected');
        header("Location: plagiarism_check.php");
        exit();
    }
}

$conn->close();
?>

