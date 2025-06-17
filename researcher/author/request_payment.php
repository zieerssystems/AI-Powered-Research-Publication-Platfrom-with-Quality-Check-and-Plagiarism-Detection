<?php
session_start();
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

$reviewer_id = $_SESSION['reviewer_id'];

include(__DIR__ . "/../../include/db_connect.php");

// Insert the payment request into the payment_requests table using the function from db_connect.php
if (insertPaymentRequest($conn, $reviewer_id)) {
    // Redirect to the dashboard or payment request page
    header("Location: reviewer_dashboard.php");
    exit();
} else {
    echo "Error submitting payment request.";
}
?>
