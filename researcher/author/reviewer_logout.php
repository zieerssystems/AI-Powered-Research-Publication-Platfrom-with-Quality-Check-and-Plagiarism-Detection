<?php
session_start();

// Unset only reviewer-related session variables
unset($_SESSION['reviewer_id']);
unset($_SESSION['reviewer_logged_in']);

// Optional: unset role if set
unset($_SESSION['role']); 

// Redirect back to reviewer login
header("Location: reviewer_login.php?logged_out=1");
exit();
?>
