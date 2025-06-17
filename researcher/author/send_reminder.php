<?php
include('../../include/db_connect.php');
require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$editor_id = $_POST['editor_id'];
$task_id = $_POST['task_id'];

// Get editor and task info using the function from db_connect.php
$data = fetchEditorAndTaskInfo($conn, $task_id);

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com'; // Replace with your email
    $mail->Password = 'your_app_password'; // Replace with your App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your_email@gmail.com', 'Zieers');
    $mail->addAddress($data['email'], $data['first_name']);
    $mail->Subject = "⏰ Task Reminder: " . $data['paper_title'];
    $mail->Body = "Dear {$data['first_name']},\n\n" .
                  "This is a reminder for your task: {$data['task_type']} on paper \"{$data['paper_title']}\".\n" .
                  "Deadline: {$data['deadline']}.\n\n" .
                  "Please complete it as soon as possible.\n\n" .
                  "Regards,\nChief Editor";

    $mail->send();

    // Update reminder_sent field using the function from db_connect.php
    updateReminderSent($conn, $task_id);

    header("Location: task_monitor.php?reminder_sent=1");
    exit;
} catch (Exception $e) {
    echo "Error sending reminder: " . $mail->ErrorInfo;
}
?>
