<?php
// Connect to DB
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paper_id = $_POST['paper_id'];
    $editor_id = $_POST['editor_id'];
    $task_type = $_POST['task_type'];
    $decision = $_POST['decision'];

    $success = updateEditorTaskStatus2($conn, $paper_id, $editor_id, $task_type, $decision);

    if ($success) {
        $redirect = ($decision === 'accept') ? 'initial_paper_detail.php' : 'initial_review.php';
        header("Location: $redirect");
        exit();
    } else {
        die("Failed to update task status.");
    }
}

$conn->close();

?>
