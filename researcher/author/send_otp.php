<?php
session_start();
require 'send_email.php'; // SMTP email function
require '../../include/db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists in authors table using the function from db_connect.php
    $author = isEmailRegistered($conn, $email);

    if ($author) {
        $author_id = $author['id'];

        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $_SESSION['author_id'] = $author_id; // Temporarily store user ID

        // Send OTP via email
        $subject = "Your OTP Code";
        $message = "Your OTP for login is: $otp. It is valid for 10 minutes.";
        if (sendEmail($email, $subject, $message)) {
            header("Location: verify_otp.php");
            exit();
        } else {
            echo "<p class='error'>Error sending OTP. Try again later.</p>";
        }
    } else {
        echo "<p class='error'>Email not registered.</p>";
    }
}
?>
