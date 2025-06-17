<?php
session_start(); // start session

include(__DIR__ . "/include/db_connect.php");

// Get form data safely
$name = htmlspecialchars(trim($_POST['name']));
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($_POST['message']));

// Save to database
$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);
$stmt->execute();
$stmt->close();

// Send email
$to = "anjuscaria7@gmail.com";  // correct email
$subject = "New Contact Form Submission from $name";
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8";
$emailBody = "Name: $name\nEmail: $email\nMessage:\n$message";
mail($to, $subject, $emailBody, $headers);

// Set success message in session
$_SESSION['contact_success'] = "Thank you for contacting us!";

// Redirect back to contact page
header("Location: contact-us.php");
exit;
?>
