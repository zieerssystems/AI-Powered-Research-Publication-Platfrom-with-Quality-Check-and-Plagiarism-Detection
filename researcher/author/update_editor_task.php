<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paper_id = $_POST['paper_id'];
    $result = $_POST['result'];
    $editor_id = $_SESSION['editor_id'];
    $task_type = 3;
    $status = 'Completed';
    $response_date = date('Y-m-d H:i:s');

    if (taskExists($conn, $paper_id, $task_type, $editor_id)) {
        updateTask($conn, $paper_id, $task_type, $editor_id, $result, $status, $response_date);
    } else {
        insertTask2($conn, $paper_id, $editor_id, $result, $status, $task_type, $response_date);
    }

    header("Location: decisions.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
