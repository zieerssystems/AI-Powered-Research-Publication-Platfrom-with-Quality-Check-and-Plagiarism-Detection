<?php
require_once(__DIR__ . "/../../vendor/autoload.php");
include(__DIR__ . "/../../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function sendEmail($toEmail, $subject, $body) {
    $mail = new PHPMailer(true);  
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Specify the SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'anjuscaria7@gmail.com'; // Your email address
        $mail->Password = 'dlvr dkbu sdob fqfu'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipi
        $mail->setFrom('anjuscaria7@gmail.com','Zieers System Pvt Ltd');
        $mail->addAddress($toEmail); // Add recipient email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
