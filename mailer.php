<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure you run `composer require phpmailer/phpmailer`

function sendMail($to, $subject, $body) {
    // ✅ Check environment and load config
    $isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

    if ($isLocalhost) {
        $config = parse_ini_file(__DIR__ . "/../../private/pub_config.ini", true);
    } else {
        require_once(__DIR__ . '/config_path.php'); // This should define CONFIG_PATH
        $config = parse_ini_file(CONFIG_PATH, true);
    }
    if (!$config || !isset($config['mail'])) {
        return false;
    }

    $mail = new PHPMailer(true);

 try {
        $mail->isSMTP();
        $mail->Host = $config['mail']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['mail']['username'];
        $mail->Password = $config['mail']['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['mail']['port'];

        $mail->setFrom($config['mail']['username'], $config['mail']['from_name']);
        $mail->addAddress($to);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
