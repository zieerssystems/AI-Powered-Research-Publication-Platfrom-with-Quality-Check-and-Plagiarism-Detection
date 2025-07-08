<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php"); // Include your DB connection

// if (isset($_SESSION['author_id'])) {
//     $author_id = $_SESSION['author_id'];

//     // Update last logout time
//     $stmt = $conn->prepare("UPDATE author  WHERE id = ?");
//     $stmt->bind_param("i", $author_id);
//     $stmt->execute();
//     $stmt->close();
// }

// Destroy the session
session_unset();
session_destroy();

// Prevent back button access after logout
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: author_dash_login.php");
exit();
?>
