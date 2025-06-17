<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");
include(__DIR__ . "/../../include/reviewer_functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $loginResult = validateReviewer($conn, $username, $password);

    if (is_numeric($loginResult)) {
        $_SESSION['reviewer_logged_in'] = true;
        $_SESSION['reviewer_id'] = $loginResult;
        $_SESSION['username'] = $username;

        // ✅ Call the update function
        updateReviewerLastLogin($conn, $loginResult);

        header("Location: reviewer_dashboard.php");
        exit();
    } else {
        header("Location: reviewer_login.php?error=" . urlencode($loginResult));
        exit();
    }
} else {
    header("Location: reviewer_login.php");
    exit();
}
?>
