<?php
// Include the necessary files
include(__DIR__ . "/../../include/db_connect.php"); // Database connection
include('get_journal_details.php'); // Where the function getJournalDetails is defined

// Assuming you are getting the journal_id from a GET request or form submission
$journal_id = isset($_GET['journal_id']) ? (int) $_GET['journal_id'] : 0;

if ($journal_id > 0) {
    // Call the function to get journal details
    $journal_details = getJournalDetails($journal_id);
    
    if ($journal_details !== null) {
        // Pass the journal details to the journal_details.php file using a session or a URL parameter
        session_start(); // Start session to store the journal details
        $_SESSION['journal_details'] = $journal_details; // Store in session
        header('Location: journal_details.php'); // Redirect to journal_details.php
        exit();
    } else {
        // Handle case when journal details are not found
        echo "Journal not found.";
    }
} else {
    echo "Invalid journal ID.";
}
?>
