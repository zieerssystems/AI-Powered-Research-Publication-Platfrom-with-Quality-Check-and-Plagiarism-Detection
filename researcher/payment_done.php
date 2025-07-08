<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

$data = json_decode(file_get_contents("php://input"), true);
$payment_id = $data['razorpay_payment_id'];
$order_id = $data['razorpay_order_id'];
$amount = $data['amount'];
$paper_ids = $data['paper_ids'];

$author_id = $_SESSION['user_id'] ?? 0;

foreach ($paper_ids as $paper_id) {
    $stmt = $conn->prepare("INSERT INTO payments (paper_id, payment_amount, razorpay_order_id, payment_status, created_at, amount, razorpay_payment_id, author_id)
                            VALUES (?, ?, ?, 'Paid', NOW(), ?, ?, ?)");
    $stmt->bind_param("idsssi", $paper_id, $amount, $order_id, $amount, $payment_id, $author_id);
    $stmt->execute();
}
echo "Payment saved";
