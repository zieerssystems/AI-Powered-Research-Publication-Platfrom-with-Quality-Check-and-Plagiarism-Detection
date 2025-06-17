<?php
session_start(); // Start session at the very top
include(__DIR__ . "/../../include/db_connect.php"); // Include DB connection file

// Ensure journal_id is present in the request
if (!isset($_POST['journal_id']) || empty($_POST['journal_id'])) {
    die("Error: journal_id is missing in the request.");
}

$journal_id = $_POST['journal_id'];

// Fetch journal details
$journal = getJournalDetails($journal_id);

if (!$journal) {
    die("No journal found with the given ID.");
}

// Store journal details
$journal_name = $journal['journal_name'];
$journal_image = $journal['journal_image'];
$image_path = "/my_publication_site/admin/" . htmlspecialchars($journal_image);

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (empty($_POST['username']) || empty($_POST['password'])) {
        die("Error: Username and password are required.");
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Call function to verify login (Updated function to exclude 'role')
    $user = verifyLogin($username, $password, $journal_id, $conn);

    if ($user) {
        $_SESSION['author_id'] = $user['author_id'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['author_email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['journal_id'] = $journal_id;
        $_SESSION['journal_name'] = $journal['journal_name'];
    
        header("Location: paper_submit.php");
        exit();
    }
    else {
        $_SESSION['error_message'] = "Invalid credentials or journal mismatch. Please try again.";
header("Location: submit-article.php?journal_id=$journal_id");
exit();

    }
}
?>
