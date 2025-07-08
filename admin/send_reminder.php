<?php 
require_once(__DIR__ . "/../vendor/autoload.php");
include(__DIR__ . "/../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost)
  $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
else{
  require_once(__DIR__ . '/../config_path.php');
$config = parse_ini_file(CONFIG_PATH, true);
}
// $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
$mail_host = $config['mail']['host'];
$mail_user = $config['mail']['username'];
$mail_pass = $config['mail']['password'];
$mail_port = $config['mail']['port'];
$from_name = $config['mail']['from_name'];


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
            $mail->Host = $mail_host;
            $mail->SMTPAuth = true;
            $mail->Username = $mail_user;
            $mail->Password = $mail_pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $mail_port;

            $mail->setFrom($mail_user, $from_name);
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
