<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST['verification_code'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'] ?? '';

    // Verify code
    if ($entered_code == $_SESSION['verification_code']) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            // Update password in DB using the function from db_connect.php
            if (ReviewerPassword($conn, $new_password, $email)) {
                echo "Password reset successful! <a href='reviewer_login.php'>Login</a>";
                unset($_SESSION['verification_code'], $_SESSION['reset_email']);
            } else {
                echo "Error updating password.";
            }
        } else {
            echo "Passwords do not match!";
        }
    } else {
        echo "Invalid verification code!";
    }
}
?>
