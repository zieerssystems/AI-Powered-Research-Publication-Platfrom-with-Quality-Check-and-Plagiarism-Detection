<?php
session_start();

// Get the file path from session
$file = $_SESSION['edited_doc'] ?? '';
if (pathinfo($file, PATHINFO_EXTENSION) !== 'docx') {
    echo "Invalid file type.";
    exit;
}


// Validate the file
if (!$file || !file_exists($file)) {
    echo "File not found.";
    exit;
}

// Set headers to force download
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Content-Length: " . filesize($file));
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");

// Output the file
readfile($file);
exit;
?>
