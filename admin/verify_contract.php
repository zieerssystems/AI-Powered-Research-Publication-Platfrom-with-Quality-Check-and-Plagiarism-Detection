<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

require_once("../vendor/autoload.php");
include("../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Load SMTP config from pub_config.ini
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost) {
    $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
} else {
    require_once(__DIR__ . '/../config_path.php');  // must define CONFIG_PATH
    $config = parse_ini_file(CONFIG_PATH, true);
}

$mailConfig = $config['mail'] ?? [];

if (
    empty($mailConfig['host']) ||
    empty($mailConfig['username']) ||
    empty($mailConfig['password']) ||
    empty($mailConfig['from_name'])
) {
    echo "<script>alert('SMTP config incomplete.'); window.history.back();</script>";
    exit;
}

// ✅ On contract verify POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewer_id'])) {
    $reviewer_id = intval($_POST['reviewer_id']);

    // Step 1: Update DB status
    if (updateReviewerStatus($conn, $reviewer_id)) {

        // Step 2: Fetch reviewer user details
        $userDetails = fetchUserDetails($conn, $reviewer_id);  // joins reviewers.user_id -> users.*

        if ($userDetails) {
            $first_name = $userDetails['first_name'];
            $last_name = $userDetails['last_name'];
            $email = $userDetails['email'];

            // Step 3: Send Email using SMTP config
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = $mailConfig['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $mailConfig['username'];
                $mail->Password = $mailConfig['password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $mailConfig['port'];
                
                $mail->setFrom($mailConfig['username'], $mailConfig['from_name']);
                $mail->addAddress($email, "$first_name $last_name");
                $mail->isHTML(true);
                $mail->Subject = "Reviewer Application Approved - Zieers";
                $mail->Body = "Dear $first_name,<br><br>
                    Congratulations! Your reviewer contract has been verified and your application is approved.<br>
                    You may now start reviewing papers on our platform.<br><br>
                    Best regards,<br><strong>Zieers Admin Team</strong>";

                $mail->send();
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: " . addslashes($mail->ErrorInfo) . "');</script>";
            }
        }

        echo "<script>alert('Contract verified, reviewer approved, and email sent!'); window.location.href='reviewer_contracts.php';</script>";
    } else {
        echo "<script>alert('Error verifying contract.'); window.location.href='reviewer_contracts.php';</script>";
    }
}
?>
