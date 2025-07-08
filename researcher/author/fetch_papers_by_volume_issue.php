<?php
include("../../include/db_connect.php");

$volume = $_GET['volume'] ?? 0;
$issue = $_GET['issue'] ?? 0;
$journal_id = $_GET['journal_id'] ?? 0;

$result = getPublishedPapers_1($conn, $volume, $issue, $journal_id);

while ($row = $result->fetch_assoc()) {
    echo "<div class='related-paper' onclick='loadPaper({$row['id']})'>";
    echo "<strong>" . htmlspecialchars($row['title']) . "</strong>";
    echo "<div class='related-meta'>" . $row['first_name'] . " " . $row['last_name'] . " | " . date("Y-m-d", strtotime($row['completed_date'])) . "</div>";
    echo "</div>";
}
?>