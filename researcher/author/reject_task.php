<?php
session_start();
include('../../include/db_connect.php');
require_once(__DIR__ . "/../../vendor/autoload.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paper_id = $_POST['paper_id'];
    $task_id = $_POST['task_id'];
    $comment = $_POST['comment'];

    // Get task type to determine paper status using the function from db_connect.php
    $task_type = getTaskType($conn, $task_id);

    $status = in_array($task_type, [1, 2]) ? 'Rejected (Pre-Review)' : 'Rejected (Post-Review)';

    // Update paper status and comment using the function from db_connect.php
    updatePaperStatusAndComment($conn, $status, $comment, $paper_id);

    // Update editor task result using the function from db_connect.php
    updateEditorResult($conn, $task_id);

    // Fetch author's email using the function from db_connect.php
    $authorDetails = fetchAuthorDetails($conn, $paper_id);
    $author_email = $authorDetails['email'];
    $author_fname = $authorDetails['first_name'];
    $author_lname = $authorDetails['last_name'];
    $title = $authorDetails['title'];

    // Fetch co-author emails and names using the function from db_connect.php
    $co_authors = fetchCoAuthorDetails($conn, $paper_id);

    // Send email to author and co-authors
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // SMTP username
        $mail->Password = 'your_app_password'; // SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your_email@gmail.com', 'Editorial Office');
        $mail->addAddress($author_email, $author_fname . ' ' . $author_lname);

        foreach ($co_authors as $co) {
            if (!empty($co['email'])) {
                $mail->addAddress($co['email'], $co['name']);
            }
        }

        $mail->isHTML(true);
        $mail->Subject = "Your Paper Submission Has Been Rejected";
        $mail->Body = "
            <p>Dear Author,</p>
            <p>We regret to inform you that your paper submitted to our platform (Paper ID: $title) has been <strong>rejected</strong> and will no longer be published.</p>
            <p><strong>Reason/Comments:</strong></p>
            <blockquote>$comment</blockquote>
            <p>Thank you for considering our platform.<br>Regards,<br><strong>Editorial Team</strong></p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
    }

    header("Location: task_monitor.php");
    exit();
}
?>
