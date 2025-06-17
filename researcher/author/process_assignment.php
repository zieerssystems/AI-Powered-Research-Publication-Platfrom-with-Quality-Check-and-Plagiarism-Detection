<?php
session_start();
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reviewer_id = intval($_POST['reviewer_id']);
    $paper_id = intval($_POST['paper_id']);
    $deadline = $_POST['deadline'];

    if (!$reviewer_id || !$paper_id || !$deadline) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: assign_review.php");
        exit();
    }

    if (checkReviewerAlreadyAssigned($conn, $paper_id, $reviewer_id)) {
        $_SESSION['error'] = "This paper has already been assigned to this reviewer.";
        header("Location: assign_review.php");
        exit();
    }

    if (!assignPaperToReviewer($conn, $paper_id, $reviewer_id, $deadline)) {
        $_SESSION['error'] = "Assignment failed.";
        header("Location: assign_review.php");
        exit();
    }

    $reviewer = getReviewerDetails($conn, $reviewer_id);
    $paper_title = getPaperTitle($conn, $paper_id);

    if ($reviewer && $paper_title) {
        $reviewer_email = $reviewer['email'];
        $reviewer_name = $reviewer['first_name'] . ' ' . $reviewer['last_name'];

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'anjuscaria7@gmail.com';
            $mail->Password = 'dlvr dkbu sdob fqfu';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('anjuscaria7@gmail.com', 'Editorial Team');
            $mail->addAddress($reviewer_email, $reviewer_name);
            $mail->Subject = "New Paper Assigned for Review";
            $mail->Body = "Dear $reviewer_name,\n\nA new paper has been assigned to you for review.\n\nPaper Title: $paper_title\nDeadline: $deadline\n\nRegards,\nEditorial Team";

            $mail->send();
            $_SESSION['success'] = "Paper assigned and email notification sent.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Email could not be sent. Error: " . $mail->ErrorInfo;
        }
    }

    header("Location: assign_review.php");
    exit();
}
?>
