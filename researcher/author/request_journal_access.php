<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Check if reviewer is logged in
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['request_journal'])) {
    $reviewer_id = $_SESSION['reviewer_id'];
    $journal_id = intval($_POST['journal_id']);

    // Check if request already exists using the function from db_connect.php
    if (checkExistingRequest($conn, $reviewer_id, $journal_id)) {
        $_SESSION["error_message"] = "You have already requested access to this journal.";
        header("Location: reviewer_account.php");
        exit();
    }

    // Insert new request using the function from db_connect.php
    if (insertJournalAccessRequest($conn, $reviewer_id, $journal_id)) {
        $_SESSION["success_message"] = "Journal access request submitted successfully!";
    } else {
        $_SESSION["error_message"] = "Failed to submit request. Please try again.";
    }

    header("Location: update_profile.php");
    exit();
}
?>
