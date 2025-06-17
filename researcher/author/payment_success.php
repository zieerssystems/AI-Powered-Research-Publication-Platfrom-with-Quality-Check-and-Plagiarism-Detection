<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_POST['payment_id'], $_POST['paper_id'])) {
    http_response_code(400);
    exit("Invalid request");
}

$payment_id = $_POST['payment_id'];
$paper_id = intval($_POST['paper_id']);

// TODO: Verify payment with Razorpay API (Recommended for security)
// For now, assume success and update session

if (!isset($_SESSION['paid_papers'])) {
    $_SESSION['paid_papers'] = [];
}
if (!in_array($paper_id, $_SESSION['paid_papers'])) {
    $_SESSION['paid_papers'][] = $paper_id;
}

// Optionally, store payment info in DB here for record

http_response_code(200);
echo "Success";
?>
