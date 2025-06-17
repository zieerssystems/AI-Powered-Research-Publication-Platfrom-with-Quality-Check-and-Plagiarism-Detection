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
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'anjuscaria7@gmail.com';  // Your email
                $mail->Password = 'dlvr dkbu sdob fqfu';     // App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('anjuscaria7@gmail.com', 'Zieers Admin');
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
