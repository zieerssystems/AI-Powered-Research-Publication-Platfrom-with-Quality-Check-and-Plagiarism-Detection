<?php
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "alert('Unauthorized access!'); window.location.href='admin-login.php';";
    exit();
}

include("../include/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editor_id'])) {
    $editor_id = intval($_POST['editor_id']);

    // Update both contract_status to 'signed' and registration_status to 'approved'
    $query = "UPDATE editors SET contract_status = 'signed', registration_status = 'approved' WHERE editor_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $editor_id);

    if ($stmt->execute()) {
        echo "alert('Contract verified successfully!');";

        // Optional: You can call the send verification email function here
        // sendVerificationEmail($editor_id);

    } else {
        echo "<script>alert('Error verifying contract. Please try again.');</scripalert>";
    }
}
?>
