<?php
// Connect to DB
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paper_id = $_POST['paper_id'];
    $editor_id = $_POST['editor_id'];
    $task_type = $_POST['task_type'];
    $decision = $_POST['decision'];

    // Fetch journal_id using function
    $journal_id = getJournalIdByPaper($conn, $paper_id);

    if ($decision === 'accept') {
        updateEditorTaskStatus($conn, $paper_id, $editor_id, $task_type, 'Accepted');
        header("Location: reviewers.php?paper_id=$paper_id&journal_id=$journal_id");
        exit();
    } elseif ($decision === 'reject') {
        updateEditorTaskStatus($conn, $paper_id, $editor_id, $task_type, 'Rejected');
        header("Location: assign_review.php");
        exit();
    }
}

$conn->close();
?>
