<?php
session_start();
include("include/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'] ?? '';
    $middle = $_POST['middle_name'] ?? '';
    $last = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    } else {
        $response = registerUser($first, $middle, $last, $email, $password);

if ($response['status']) {
    $_SESSION['success_message'] = $response['message'];
    header("Location: register.php");
    exit();
} else {
    $_SESSION['error_message'] = $response['message'];
    header("Location: register.php");
    exit();
}
    }
}
?>
