<?php
session_start();
include(__DIR__ . "/../include/db_connect.php"); // Includes the function to fetch admin user

$email = $_POST['email'];
$password = $_POST['password'];

if (!empty($email) && !empty($password)) {
    $user = getAdminUserByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['email'] = $user['email'];
        $_SESSION['admin'] = 1;

        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Admin Credentials'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Please fill in all fields'); window.history.back();</script>";
}
?>
