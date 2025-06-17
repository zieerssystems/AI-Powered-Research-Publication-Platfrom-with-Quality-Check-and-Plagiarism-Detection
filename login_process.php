<?php
session_start();
include("include/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = loginUser($email, $password);

    if ($result['status']) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['first_name'] = $result['first_name'];
        $_SESSION['success_message'] = "Login successful. " . $result['first_name'] . "!";

        // ✅ Use redirect path from session
        if (!empty($_SESSION['redirect_after_login'])) {
            $redirect_to = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']); // Clear it
        } else {
            $redirect_to = 'index.php';
        }
        header("Location: $redirect_to");
        exit;

    } else {
        $_SESSION['error_message'] = $result['message'];
        header("Location: login.php");
        exit;
    }
}
?>
