<?php
session_start();
$filename = $_GET['file'] ?? '';

$filepath = realpath(__DIR__ . "/../../uploads/" . $filename);

// Basic validation
if (!$filename || !file_exists($filepath)) {
    die("File not found.");
}

// Optional: Check permissions if the file is paid-access only

header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;
