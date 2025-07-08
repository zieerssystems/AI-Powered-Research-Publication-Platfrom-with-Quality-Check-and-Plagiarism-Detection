<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

include("../include/db_connect.php");
require_once("../vendor/autoload.php");  // Include the function file
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost)
  $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
else{
  require_once(__DIR__ . '/../config_path.php');
$config = parse_ini_file(CONFIG_PATH, true);
}
// Load config from pub_config.ini
// $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
$mail_host = $config['mail']['host'];
$mail_user = $config['mail']['username'];
$mail_pass = $config['mail']['password'];
$mail_port = $config['mail']['port'];
$mail_from_name = $config['mail']['from_name'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = $mail_host;
$mail->SMTPAuth = true;
$mail->Username = $mail_user;
$mail->Password = $mail_pass;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = $mail_port;
$mail->setFrom($mail_user, $mail_from_name);
$mail->isHTML(true);


// ✅ Reject and DELETE Reviewer (if ?id= exists)
if (isset($_GET['id']) && !isset($_GET['editor_id'])) {
    $reviewer_id = intval($_GET['id']);

    // Fetch reviewer details before deletion using function
  $row = getReviewerDetails($conn, $reviewer_id);
if ($row) {

        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];

        try {
            $mail->addAddress($email, "$first_name $last_name");
            $mail->Subject = "Reviewer Application Rejected";
            $mail->Body = "Dear $first_name,<br><br>We regret to inform you that your application as a reviewer has been rejected and your data has been removed from our system.<br><br>Regards,<br>Zieers Admin";
            $mail->send();
        } catch (Exception $e) {
            echo "<script>alert('Mailer Error (Reviewer): " . $mail->ErrorInfo . "');</script>";
        }
    }

    // Delete reviewer using the function
    if (deleteReviewer($conn, $reviewer_id)) {
        echo "<script>alert('Reviewer rejected and deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting reviewer.'); window.location.href='admin_dashboard.php';</script>";
    }
}

// ✅ Reject and DELETE Editor (if ?editor_id= exists)
if (isset($_GET['editor_id'])) {
    $editor_id = intval($_GET['editor_id']);

    // Fetch editor details before deletion using function
    // $editor_result = getEditorDetails($conn, $editor_id);
$editor_row = getEditorDetails($conn, $editor_id);
if ($editor_row) {
        $editor_first_name = $editor_row['first_name'];
        $editor_last_name = $editor_row['last_name'];
        $editor_email = $editor_row['email'];

        try {
            $mail->clearAddresses();
            $mail->addAddress($editor_email, "$editor_first_name $editor_last_name");
            $mail->Subject = "Editor Application Rejected";
            $mail->Body = "Dear $editor_first_name,<br><br>We regret to inform you that your application as an editor has been rejected and your data has been removed from our system.<br><br>Regards,<br>Zieers Admin";
            $mail->send();
        } catch (Exception $e) {
            echo "<script>alert('Mailer Error (Editor): " . $mail->ErrorInfo . "');</script>";
        }
    }

    // Delete editor using the function
    if (deleteEditor($conn, $editor_id)) {
        echo "<script>alert('Editor rejected and deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting editor.'); window.location.href='admin_dashboard.php';</script>";
    }
}
?>
