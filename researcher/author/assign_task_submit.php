<?php
session_start();
require_once(__DIR__ . "/../../vendor/autoload.php");
include(__DIR__ . "/../../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if user is logged in as chief editor
$assigned_by = $_SESSION['chief_editor_id'] ?? null;
if (!$assigned_by) {
    die("Unauthorized access.");
}

// Get POST data
$paper_id = $_POST['paper_id'] ?? null;
$editor_id = $_POST['editor_id'] ?? null;
$task_type = $_POST['task_type'] ?? null;
$deadline = $_POST['deadline'] ?? null;

if (!$paper_id || !$editor_id || !$task_type || !$deadline) {
    die("Missing required data.");
}

// Check if task is already assigned
if (isTaskAssigned($conn, $paper_id, $task_type, $editor_id)) {
    echo "<script>alert('This task has already been assigned to the selected editor for this paper.'); window.history.back();</script>";
    exit;
}

// Insert into editor_tasks table
if (insertTask($conn, $paper_id, $editor_id, $task_type, $assigned_by, $deadline)) {
    // Fetch editor's email and paper title
    $data = fetchEditorAndPaperDetails($conn, $paper_id, $editor_id);

    if ($data) {
        // Setup and send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com'; // Replace with your email
            $mail->Password = 'your_app_password'; // Replace with your App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Zieers');
            $mail->addAddress($data['email'], $data['first_name'] . ' ' . $data['last_name']);
            $mail->Subject = "Task Assigned: Paper - " . $data['title'];
            $mail->Body = "Dear " . $data['first_name'] . ",\n\n" .
                          "You have been assigned a new task (Type $task_type) for the paper titled \"" . $data['title'] . "\".\n" .
                          "Deadline: $deadline\n\n" .
                          "Please login to your editor panel to proceed.\n\n" .
                          "Regards,\nZieers Team";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email could not be sent. Error: " . $mail->ErrorInfo);
        }
    }

    header("Location: task.php"); // Update with actual filename
    exit();
} else {
    die("Failed to assign task.");
}
?>
