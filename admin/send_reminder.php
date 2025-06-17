<?php 
require_once(__DIR__ . "/../vendor/autoload.php");
include(__DIR__ . "/../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (
        isset($_POST['paper_id'], $_POST['email'], $_POST['paper_title'], 
              $_POST['journal_name'], $_POST['author_first_name'], $_POST['author_last_name'])
    ) {
        $paper_id = $_POST['paper_id'];
        $email = $_POST['email'];
        $paper_title = $_POST['paper_title'];
        $journal_name = $_POST['journal_name'];
        $author_first_name = $_POST['author_first_name'];
        $author_last_name = $_POST['author_last_name'];

        // Email content
        $subject = "Reminder: Payment Required for Publication in $journal_name";
        $body = "
            Dear {$author_first_name} {$author_last_name},<br><br>
            Your paper titled <strong>" . htmlspecialchars($paper_title) . "</strong> has been accepted for publication in <strong>" . htmlspecialchars($journal_name) . "</strong>.<br><br>
            This journal is open access. To proceed with publication, you are required to complete the payment.<br><br>
            <strong>Please log in to your dashboard and complete the payment to avoid delay in publication.</strong><br><br>
            Thank you,<br>
            Editorial Team – " . htmlspecialchars($journal_name) . "
        ";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'anjuscaria7@gmail.com'; // Use env variable in production
            $mail->Password = 'dlvr dkbu sdob fqfu';   // Use env variable in production
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('anjuscaria7@gmail.com', 'Editorial Office');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            echo "<script>alert('Reminder email sent successfully.'); window.history.back();</script>";

        } catch (Exception $e) {
            echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Missing required form data.'); window.history.back();</script>";
    }
}
?>
