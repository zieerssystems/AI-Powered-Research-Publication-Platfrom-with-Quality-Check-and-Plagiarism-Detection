<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['reviewer_logged_in']) || $_SESSION['reviewer_logged_in'] !== true) {
    header("Location: reviewer_login.php");
    exit();
}

$reviewer_id = $_SESSION['reviewer_id'];
$assigned_journals = getReviewerJournals($conn, $reviewer_id);
// $notifications = getReviewerNotifications($conn, $reviewer_id);
?>