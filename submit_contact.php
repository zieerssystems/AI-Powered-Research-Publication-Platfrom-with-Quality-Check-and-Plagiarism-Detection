<?php
session_start(); // start session

include(__DIR__ . "/include/db_connect.php");

function getMailConfig() {
    $isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

    if ($isLocalhost) {
        $config = parse_ini_file(__DIR__ . '/../../private/pub_config.ini', true);
    } else {
        require_once(__DIR__ . '/../config_path.php'); // defines CONFIG_PATH
        $config = parse_ini_file(CONFIG_PATH, true);
    }

    if (!$config || !isset($config['mail'])) {
        die("Mail config not found or malformed.");
    }

    return $config['mail'];
}

// Get form data safely
$name = htmlspecialchars(trim($_POST['name']));
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($_POST['message']));

// Save to database
$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);
$stmt->execute();
$stmt->close();

function sendContactEmail($to, $from, $subject, $body) {
    $mail_config = getMailConfig();

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $mail_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mail_config['username'];
        $mail->Password = $mail_config['password'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mail_config['port'];

        $mail->setFrom($mail_config['username'], $mail_config['from_name']);
        $mail->addAddress($to);
        $mail->addReplyTo($from);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->isHTML(false); // Plain text

        $mail->send();
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
    }
}

// Compose and send email
$to = "anjuscaria7@gmail.com";
$subject = "New Contact Form Submission from $name";
$emailBody = "Name: $name\nEmail: $email\nMessage:\n$message";

sendContactEmail($to, $email, $subject, $emailBody);
// Set success message in session
$_SESSION['contact_success'] = "Thank you for contacting us!";

// Redirect back to contact page
header("Location: contact-us.php");
exit;
?>
