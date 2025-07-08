<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include(__DIR__ . "/../../include/db_connect.php");
require_once(__DIR__ . "/../../vendor/autoload.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Only for "Author" role
    if ($role === "Author") {
        // Check if email exists in the "author" table
        $author = checkAuthorEmailExists($conn, $email);

        if ($author) {
            // Generate OTP
            $otp = rand(100000, 999999);  // 6-digit OTP
            $_SESSION['otp'] = $otp; // Save OTP in session temporarily

            // Send OTP to email
            $id = $author['id'];
            $username = $author['username'];

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'your-email@gmail.com'; // Replace with your email
                $mail->Password = 'your-app-password'; // Replace with your app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('support@zieers.com', 'Zieers Support');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Zieers - Password Reset OTP';
                $mail->Body = "
                    <p>Dear $username,</p>
                    <p>We received a request to reset your password. Use the OTP below to proceed:</p>
                    <p><strong>OTP: $otp</strong></p>
                    <p>Please use this OTP to reset your password.</p>
                    <br>
                    <p>Regards, <br> Zieers Team</p>
                ";

                $mail->send();
                header("Location: otp_verify.php?email=$email"); // Redirect to OTP verification page
                exit;
            } catch (Exception $e) {
                header("Location: article_register.php?msg=error");
                exit;
            }
        } else {
            header("Location: article_register.php?msg=notfound");
            exit;
        }
    }
}
?>
