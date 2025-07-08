<?php
require '../vendor/autoload.php'; // Composer's autoloader
include("../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost)
  $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
else{
  require_once(__DIR__ . '/../../config_path.php');
$config = parse_ini_file(CONFIG_PATH, true);
}
// $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
if (!$config || !isset($config['mail']['username'])) {
    die("Mail config not loaded properly.");
}
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if (!in_array($action, ['accept', 'reject'])) {
        die("Invalid action.");
    }

    // Fetch reviewer request using function from db_connect.php
    $result = getReviewerRequestById($conn, $id);

    if ($result->num_rows === 0) {
        die("Request not found.");
    }

    $data = $result->fetch_assoc();
    $reviewerEmail = $data['email'];
    $reviewerName = $data['first_name'] . " " . $data['last_name'];
    $journalName = $data['journal_name'];

    $subject = ($action === 'accept') ? "Review Request Approved for $journalName" : "Review Request Rejected for $journalName";
    
    $message = "<html><body>";
    $message .= "<h2>Dear $reviewerName,</h2>";
    if ($action === 'accept') {
        $message .= "<p>We are pleased to inform you that your request to review papers for the journal <strong>$journalName</strong> has been approved.</p>";
        $message .= "<p>You will soon receive manuscripts for review. Thank you for your willingness to contribute to our academic community.</p>";
    } else {
        $message .= "<p>We regret to inform you that your request to review papers for the journal <strong>$journalName</strong> has not been approved.</p>";
        $message .= "<p>We appreciate your interest and encourage you to apply again in the future.</p>";
    }
    $message .= "<br><p>Sincerely,<br>The Editorial Team</p>";
    $message .= "</body></html>";

    // Send Email via PHPMailer
     $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $config['mail']['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['mail']['username'];
        $mail->Password   = $config['mail']['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['mail']['port'];

        $mail->setFrom($config['mail']['username'], $config['mail']['from_name']);
         $mail->addAddress($reviewerEmail, $reviewerName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();

        // Update request status
        $newStatus = ($action === 'accept') ? 'accepted' : 'rejected';
        $stmt = $conn->prepare("UPDATE reviewer_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();

        header("Location: editor_details.php?msg=" . urlencode("Email sent and request $newStatus."));
        exit;
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Missing parameters.";
}
