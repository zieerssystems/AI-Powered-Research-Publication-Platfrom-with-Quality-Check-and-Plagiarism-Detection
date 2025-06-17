<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure you run `composer require phpmailer/phpmailer`

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'anjuscaria7@gmail.com'; 
        $mail->Password = 'dlvr dkbu sdob fqfu'; 
        $mail->SMTPSecure = 'tls'; 
        $mail->Port = 587;

        $mail->setFrom('anjuscaria7@gmail.com', 'Zieers');
        $mail->addAddress($to);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
