<?php 
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_POST['paper_id'], $_POST['task_type'], $_POST['editor_id'])) {
    $_SESSION['task_message'] = "Missing parameters.";
    header("Location: task.php");
    exit();
}

$paper_id = intval($_POST['paper_id']);
$task_type = intval($_POST['task_type']);
$editor_id = intval($_POST['editor_id']);

$deleteQuery = "DELETE FROM editor_tasks WHERE paper_id = ? AND task_type = ? AND editor_id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("iii", $paper_id, $task_type, $editor_id);

if ($stmt->execute()) {
    $_SESSION['popup_message'] = "✅ Task deleted successfully.";
} else {
    $_SESSION['popup_message'] =  "❌ Error deleting task.";
}

header("Location: task.php"); // Redirect to task.php
exit();
?>
