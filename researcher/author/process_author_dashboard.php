<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    header("Location: author_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$author_id = $_SESSION['author_id'];

// Fetch author details
$authorDetails = getAuthorDetails($author_id);
$authorName = $authorDetails['first_name'] . " " . $authorDetails['last_name'];
$authorEmail = $authorDetails['email'];
$lastLogin = $authorDetails['last_login'];

// Fetch author statistics
$authorStats = getAuthorStats($author_id);
?>
