<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // Include PHPMailer
include(__DIR__ . "/../../include/db_connect.php");

$today = date("Y-m-d");
$two_days_before = date("Y-m-d", strtotime("-2 days"));
$one_day_before = date("Y-m-d", strtotime("-1 days"));

// Fetch review reminders using the function from db_connect.php
$reminders = fetchReviewReminders($conn, $two_days_before, $one_day_before, $today);

while ($row = $reminders->fetch_assoc()) {
    $email = $row['email'];
    $title = $row['title'];
    $deadline = $row['deadline'];
    $subject = "";
    $message = "";

    if ($deadline == $two_days_before) {
        $subject = "📌 Reminder: Upcoming Manuscript Review";
        $message = "Dear Reviewer,<br><br>The manuscript <strong>\"$title\"</strong> is due for review on <strong>$deadline</strong>.<br>Please submit your review on time.<br><br>Regards,<br>Editorial Team";
    } elseif ($deadline == $one_day_before) {
        $subject = "⚠️ Urgent: Review Due Tomorrow!";
        $message = "Dear Reviewer,<br><br>The manuscript <strong>\"$title\"</strong> is due tomorrow.<br>Kindly submit your review as soon as possible.<br><br>Regards,<br>Editorial Team";
    } elseif ($deadline < $today) {
        $subject = "🚨 Overdue: Manuscript Review";
        $message = "Dear Reviewer,<br><br>The manuscript <strong>\"$title\"</strong> is overdue.<br>Please submit your review immediately or contact the editor.<br><br>Regards,<br>Editorial Team";
    }

    // Send Email via SMTP
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your SMTP provider
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Your email
        $mail->Password = 'your_app_password'; // Use an App Password for security
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('your_email@gmail.com', 'Editorial Team');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $message;

        $mail->send();
        echo "Reminder sent to $email\n";
    } catch (Exception $e) {
        echo "Failed to send email to $email. Error: {$mail->ErrorInfo}\n";
    }
}
?>
