<?php
session_start();
include(__DIR__ . "/../include/db_connect.php");


// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access!";
    exit();
}

// Validate journal ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid journal ID";
    exit();
}

$journal_id = intval($_GET['id']);

// Call the delete function
if (deleteJournalById($conn, $journal_id)) {
    echo "Journal deleted successfully!";
} else {
    echo "Error deleting journal.";
}

$conn->close();
?>
