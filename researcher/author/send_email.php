<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Path to PHPMailer

function sendEmail($to, $subject, $message, $isHTML = false) {
    $mail = new PHPMailer(true);

    // ✅ Detect environment and load config accordingly
    $isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

    if ($isLocalhost) {
        $config = parse_ini_file(__DIR__ . '/../../../../private/pub_config.ini', true);
    } else {
        require_once(__DIR__ . '/../../config_path.php'); // Make sure this defines CONFIG_PATH
        $config = parse_ini_file(CONFIG_PATH, true);
    }

    // ✅ Extract email config
    $emailUser = $config['mail']['username'];
    $emailPass = $config['mail']['password'];
    $emailFromName = $config['mail']['from_name'];

     try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $emailUser;
        $mail->Password   = $emailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($emailUser, $emailFromName);
        $mail->addAddress($to);

        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
