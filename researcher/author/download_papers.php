<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Helper to fetch paper details by ID
function getPaperById($conn, $id) {
    $stmt = $conn->prepare("SELECT id, file_path, title FROM papers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

if (!isset($_GET['paper_ids'])) {
    die("No paper IDs received.");
}

$paperIds = json_decode($_GET['paper_ids'], true);
if (!is_array($paperIds)) {
    die("Invalid paper ID list.");
}

$zip = new ZipArchive();
$zipFileName = "selected_papers_" . time() . ".zip";
$zipFilePath = sys_get_temp_dir() . "/" . $zipFileName;

if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
    die("Failed to create ZIP file.");
}

$downloaded = 0;

foreach ($paperIds as $id) {
    $paper = getPaperById($conn, $id);
    if ($paper) {
        $filePath = "../../uploads/" . $paper['file_path'];
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($paper['file_path']));
            $downloaded++;
        }
    }
}

$zip->close();

if ($downloaded === 0) {
    die("No valid Open Access papers found to download.");
}

// Serve the zip file for download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
header('Content-Length: ' . filesize($zipFilePath));
readfile($zipFilePath);

// Delete temp file after download
unlink($zipFilePath);
exit;
?>
