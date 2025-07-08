<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

// Correct path to db_connect.php
include(__DIR__ . "/../include/db_connect.php");


$approved_reviewers = getReviewerCount($conn, 'approved');
$pending_reviewers = getReviewerCount($conn, 'pending');
// Fetch all reviewers
$reviewers = getAllReviewersWithJournals($conn);
?>
