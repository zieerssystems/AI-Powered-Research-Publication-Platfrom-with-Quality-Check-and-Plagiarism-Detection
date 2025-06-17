<?php
include(__DIR__ . "/../include/db_connect.php");
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "Unauthorized access!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['field'], $_POST['value'])) {
    $id = intval($_POST['id']);
    $field = $_POST['field'];
    $value = trim($_POST['value']);

    // Call the function to update the journal field
    if (updateJournalField($conn, $id, $field, $value)) {
        echo "success"; // AJAX will check for this response
    } else {
        echo "Error updating field.";
    }

    $conn->close();
} else {
    echo "Invalid request!";
}
?>
