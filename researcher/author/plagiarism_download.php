<?php
// download.php

$file = $_GET['file'] ?? '';

$base_dir = __DIR__ . "/../../uploads/";
$file_path = realpath($base_dir . basename($file));

// Security check: ensure file is inside allowed directory
if ($file_path && strpos($file_path, realpath($base_dir)) === 0 && file_exists($file_path)) {
    $filename = basename($file_path);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    http_response_code(403);
    echo "Access denied or file not found.";
}
?>
