<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}
include("../include/db_connect.php");
require_once("../vendor/autoload.php");
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
if (isset($_GET['id'])) {
    $reviewer_id = intval($_GET['id']);

    if (approveReviewer($conn, $reviewer_id)) {
        $row = getReviewerDetail($conn, $reviewer_id);

        if ($row) {
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $email = $row['email'];
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
                
                $mail->addAddress($email, "$first_name $last_name");
                $mail->isHTML(true);
                $mail->Subject = "Reviewer Application Approved";
                $mail->Body = "Dear $first_name,<br><br>Your application as a reviewer has been approved.<br>You can now start reviewing research papers.<br><br>Regards,<br>Zieers Admin";

                $mail->send();
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "');</script>";
            }
        }
        echo "<script>alert('Reviewer approved successfully!'); window.location.href='reviewer_contracts.php';</script>";
    } else {
        echo "<script>alert('Error approving reviewer.'); window.location.href='reviewer_contracts.php';</script>";
    }
}
?>
