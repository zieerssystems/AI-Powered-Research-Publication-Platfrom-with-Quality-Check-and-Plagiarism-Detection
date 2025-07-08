<?php
require_once(__DIR__ . "/../../vendor/autoload.php");
include(__DIR__ . "/../../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function getMailConfig() {
    $isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

    if ($isLocalhost) {
        $config = parse_ini_file(__DIR__ . '/../../../../private/pub_config.ini', true);
    } else {
        require_once(__DIR__ . '/../../config_path.php'); // defines CONFIG_PATH
        $config = parse_ini_file(CONFIG_PATH, true);
    }

    if (!$config || !isset($config['mail'])) {
        die("Mail config not found or malformed.");
    }

    return $config['mail'];
}

function sendEmail($toEmail, $subject, $body) {
    $mail_config = getMailConfig();
    $mail = new PHPMailer(true);  
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $mail_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mail_config['username'];
        $mail->Password = $mail_config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mail_config['port'];

        // Recipients
        $mail->setFrom($mail_config['username'], $mail_config['from_name']);
        $mail->addAddress($toEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

