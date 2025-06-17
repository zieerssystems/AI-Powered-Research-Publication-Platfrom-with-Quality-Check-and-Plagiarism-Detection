<?php
include(__DIR__ . "/../../include/db_connect.php");
include(__DIR__ . "/../../include/db_functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paper_id = $_POST['paper_id'];
    $task_type = $_POST['task_type'];
    $result = $_POST['result'];
    $feedback = $_POST['feedback'] ?? '';

    $revision_request_time = date('Y-m-d H:i:s');

    if ($result === 'Revision Request' && !empty($_FILES['formatted_file']['name'])) {
        $upload_dir = "../../uploads";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES["formatted_file"]["name"]);
        $target_file = $upload_dir . "/" . $filename;

        if (move_uploaded_file($_FILES["formatted_file"]["tmp_name"], $target_file)) {
            $relative_path = str_replace("\\", "/", $target_file);
            $relative_path = ltrim($relative_path, "../../");
            updatePaperFile($conn, $paper_id, $relative_path);
        }
    }

    if ($result === 'Revision Request') {
        updateEditorTaskWithFeedback($conn, $paper_id, $task_type, $result, $feedback);
        updateformateStatus($conn, $paper_id, 'Revision Requested', $revision_request_time);
    } else {
        if ($task_type == 4) {
            updateEditorTaskWithFeedback($conn, $paper_id, $task_type, $result, $feedback);
        } else {
            updateEditorTaskStatusOnly($conn, $paper_id, $task_type, $result);
        }
    }

    header("Location: editor_dashboard.php");
    exit();
}
?>
