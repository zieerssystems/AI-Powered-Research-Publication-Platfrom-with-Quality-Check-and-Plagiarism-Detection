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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewer_id'])) {
    $reviewer_id = intval($_POST['reviewer_id']);

    // Step 1: Update contract_status and registration_status
    if (updateReviewerStatus($conn, $reviewer_id)) {

        // Step 2: Fetch user details from users table via reviewers.user_id
        $userDetails = fetchUserDetails($conn, $reviewer_id);

        if ($userDetails) {
            $first_name = $userDetails['first_name'];
            $last_name = $userDetails['last_name'];
            $email = $userDetails['email'];

            // Step 3: Send approval email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@gmail.com';  // Replace with your email
                $mail->Password = 'your_app_password';     // Replace with your App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('your_email@gmail.com', 'Zieers Admin');
                $mail->addAddress($email, "$first_name $last_name");
                $mail->isHTML(true);
                $mail->Subject = "Reviewer Application Approved";
                $mail->Body = "Dear $first_name,<br><br>
                    Congratulations! Your application as a reviewer has been approved.<br>
                    You can now start reviewing research papers.<br><br>
                    Regards,<br><strong>Zieers Admin</strong>";

                $mail->send();
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "');</script>";
            }
        }

        echo "<script>alert('Contract verified, reviewer approved, and email sent!'); window.location.href='reviewer_contracts.php';</script>";
    } else {
        echo "<script>alert('Error verifying contract.'); window.location.href='reviewer_contracts.php';</script>";
    }
}
?>
