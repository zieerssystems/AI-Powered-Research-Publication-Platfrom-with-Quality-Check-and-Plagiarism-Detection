<?php
include(__DIR__ . "/../../include/db_connect.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['first_name']) || !isset($_POST['last_name']) || !isset($_POST['email']) || !isset($_POST['role']) || !isset($_POST['journal_id'])) {
        die("Error: Required fields are missing.");
    }

    $_SESSION['journal_id'] = $_POST['journal_id'];
    $_SESSION['first_name'] = $_POST['first_name'];
    $_SESSION['middle_name'] = $_POST['middle_name'] ?? '';
    $_SESSION['last_name'] = $_POST['last_name'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['role'] = $_POST['role']; // Role selection (Author/Reviewer)

    // Redirect based on role
    if ($_SESSION['role'] == "Author") {
        header("Location: author_reg.php");
    } elseif ($_SESSION['role'] == "Reviewer") {
        header("Location: reviewer_reg.php");
    } else {
        die("Invalid role selection.");
    }
    exit();
}
?>
