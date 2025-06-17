<?php
require 'db_connect.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Check if the token matches the user's email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND token = ? AND is_verified = 0");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Update user status to verified
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, token = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            echo "Email verified successfully! You can now log in.";
        } else {
            echo "Error updating verification status.";
        }
    } else {
        echo "Invalid or expired verification link.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
