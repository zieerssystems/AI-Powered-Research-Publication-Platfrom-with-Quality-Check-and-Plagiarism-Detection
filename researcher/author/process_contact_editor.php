<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

$reviewer_id = $_SESSION['reviewer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $paper_id = !empty($_POST['paper_id']) ? intval($_POST['paper_id']) : null;
    $editor_id = !empty($_POST['editor_id']) ? intval($_POST['editor_id']) : null;
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['general_message'] ?? '');

    if ($editor_id && $subject && $message) {
        $success = sendReviewerToEditorMessage($conn, $reviewer_id, $editor_id, $paper_id, $subject, $message);
        if ($success) {
            echo "<script>alert('Message sent successfully!'); window.location.href='reviewer_dashboard.php';</script>";
        } else {
            echo "<script>alert('Database Error. Please try again later.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
