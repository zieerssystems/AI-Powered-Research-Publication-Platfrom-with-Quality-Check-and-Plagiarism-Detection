<?php
// download_report.php
$report = isset($_GET['report']) ? basename($_GET['report']) : null;

if (!$report) {
    die("Invalid report file.");
}

$filepath = __DIR__ . "/reports/" . $report;

if (file_exists($filepath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $report . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
} else {
    die("Report not found.");
}
?>
