<?php
include("../../include/db_connect.php");

$volume = $_GET['volume'] ?? 0;
$journal_id = $_GET['journal_id'] ?? 0;

$result = getPublishedIssues($conn, $volume, $journal_id);

$issues = [];
while ($row = $result->fetch_assoc()) {
    $issues[] = $row['issue'];
}

header('Content-Type: application/json');
echo json_encode($issues);
?>