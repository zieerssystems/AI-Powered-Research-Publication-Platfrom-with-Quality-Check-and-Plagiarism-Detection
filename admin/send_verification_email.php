<?php
// Include the necessary PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Include this line if you're using Composer
include("../include/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $editorId = $_POST['editor_id'];

    // Fetch the editor's details
    $editor = fetchEditorDetails($conn, $editorId);

    if ($editor) {
        $team_id = $editor['team_id'];
        $role = $editor['role'];
        $editor_email = $editor['email'];
        $editor_name = $editor['first_name'] . ' ' . $editor['last_name'];

        // Fetch Team Details
        $team = fetchTeamDetails($conn, $team_id);
        $team_name = $team['team_name'];

        $papers = [];

        if ($role == 'Chief Editor') {
            // Fetch papers associated with this Chief Editor's team
            $papers = fetchPapersForChiefEditor($conn, $editorId);

            // Prepare the email body for the Chief Editor
            $subject = "You have been assigned as Chief Editor";
            $body = "
                <p>Dear $editor_name,</p>
                <p>Congratulations! You have been assigned as Chief Editor for the team '$team_name'.</p>
                <p>You will now manage journals and papers submitted to this team. The following papers are currently pending in your journal(s):</p>
                <ul>";

            // List all papers with title and submission date
            foreach ($papers as $paper) {
                $body .= "<li><strong>Journal:</strong> {$paper['journal_name']}, <strong>Paper Title:</strong> {$paper['title']}, <strong>Submission Date:</strong> {$paper['date']}</li>";
            }

            $body .= "</ul>
                <p>As Chief Editor, you can now access the Chief Editor Dashboard using your Username and Password.</p>
                <p>Best regards,</p>
                <p>Zieers Team</p>";
        } else {
            // For other roles (non-Chief Editors), fetch journals for their team
            $journals = fetchJournalsForTeam($conn, $team_id);

            $body = "
                <p>Dear $editor_name,</p>
                <p>You have been assigned to the editorial team '$team_name' as an Editor.</p>
                <p>You will manage and assign tasks related to journals submitted to this team. The following journals are part of your team:</p>
                <ul>";

            // List all journals
            foreach ($journals as $journal) {
                $body .= "<li><strong>Journal:</strong> {$journal['journal_name']}</li>";
            }

            $body .= "</ul>
                <p>You can now access the Editor Dashboard to manage tasks on time.</p>
                <p>Best regards,</p>
                <p>Your Editorial Team</p>";

            $subject = "You have been assigned to a team as an Editor";
        }

        // Send the email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com';
            $mail->Password = 'your_app_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('your_email@gmail.com', 'Your Editorial Team');
            $mail->addAddress($editor_email, $editor_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Send the email
            $mail->send();
            echo 'Email has been sent to ' . $editor_name;
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Editor not found.";
    }
}
?>
