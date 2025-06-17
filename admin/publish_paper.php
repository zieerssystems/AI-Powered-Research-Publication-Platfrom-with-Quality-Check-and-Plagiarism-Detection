<?php 
session_start();

// Include the necessary files
include(__DIR__ . "/../include/db_connect.php");

// Check for POST request and validate 'paper_id'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paper_id'])) {
    $paper_id = intval($_POST['paper_id']); // Get the paper ID from POST

    // Call the function to update the paper status
    if (updatePaperStatus($conn, $paper_id)) {
        // If successful, send a success response
        http_response_code(200);
        echo "Success";
    } else {
        // If there's an error, send a failure response
        http_response_code(500);
        echo "Database update failed.";
    }

    exit();
}

// Handle invalid request
http_response_code(400);
echo "Invalid request.";
exit();
