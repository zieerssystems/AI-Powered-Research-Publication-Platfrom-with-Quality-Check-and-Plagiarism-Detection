<?php
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['primary_subject'])) {
    $primary_subject = trim($_POST['primary_subject']);

    // Debugging: Log primary_subject
    error_log("Fetching journals for: " . $primary_subject);

    $result = getJournalsBySubject($conn, $primary_subject);

    if ($result) {
        $options = "<option value=''>Select Journal</option>";
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='{$row['id']}'>{$row['journal_name']}</option>";
        }
        echo $options;
        $stmt->close();
    } else {
        echo "<option value=''>Error in query</option>";
    }
}
?>