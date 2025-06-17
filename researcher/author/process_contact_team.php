<?php 
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$sender_id = $_SESSION['chief_editor_id'];
$sender_role = 'editor';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_inputs = $_POST['recipient_ids'];
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($recipient_inputs) || empty($subject) || empty($message)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    sendEditorMessages($conn, $sender_id, $recipient_inputs, $subject, $message);

    echo "<script>alert('Message(s) sent successfully!'); window.location.href='chief-dashboard.php';</script>";
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>
