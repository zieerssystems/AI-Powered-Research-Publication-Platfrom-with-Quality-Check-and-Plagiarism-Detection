<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Check if the editor is logged in
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

// Get the paper ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid paper ID.");
}
$paper_id = intval($_GET['id']);

// 🔁 Use helper function to get paper details
$paper = getPaperDetailsById($conn, $paper_id);

if (!$paper) {
    die("❌ Paper not found.");
}

// Get safe file path
$file_path = __DIR__ . "/../../uploads/" . basename($paper['file_path']);
$file_path = realpath($file_path);

// Check if file exists
if (!$file_path || !file_exists($file_path)) {
    die("❌ File not found at: " . htmlspecialchars($file_path));
}

// Set headers to display the PDF in browser
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . basename($file_path) . "\"");
header("Content-Length: " . filesize($file_path));

// Output the file
@readfile($file_path);
exit();
