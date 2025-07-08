<?php
session_start();
include(__DIR__ . "/../include/db_connect.php");

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['paper_id'], $_POST['doi'])) {
    $paper_id = intval($_POST['paper_id']);
    $doi = trim($_POST['doi']);
    $volume = trim($_POST['volume']);
    $issue = trim($_POST['issue']);
    $year = trim($_POST['year']);

    // Call the function to update paper details
    if (updatePaperDetails($conn, $paper_id, $doi, $volume, $issue, $year)) {
        echo "<script>alert('DOI, Volume, Issue, and Year updated successfully.'); window.location.href='review_paper.php';</script>";
    } else {
        echo "<script>alert('Update failed.'); window.location.href='review_paper.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='review_paper.php';</script>";
}
?>
