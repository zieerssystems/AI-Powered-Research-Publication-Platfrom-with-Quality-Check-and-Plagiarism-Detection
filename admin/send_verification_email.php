<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
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
        $subject = "";
        $body = "";

        if ($role == 'Chief Editor') {
            $papers = fetchPapersForChiefEditor($conn, $editorId);

            $subject = "You have been assigned as Chief Editor";
            $body = "
                <p>Dear $editor_name,</p>
                <p>Congratulations! You have been assigned as Chief Editor for the team '$team_name'.</p>
                <p>You will now manage journals and papers submitted to this team. The following papers are currently pending in your journal(s):</p>
                <ul>";

            foreach ($papers as $paper) {
                $body .= "<li><strong>Journal:</strong> {$paper['journal_name']}, <strong>Paper Title:</strong> {$paper['title']}, <strong>Submission Date:</strong> {$paper['date']}</li>";
            }

            $body .= "</ul>
                <p>As Chief Editor, you can now access the Chief Editor Dashboard using your Username and Password.</p>
                <p>Best regards,</p>
                <p>Zieers Team</p>";

        } else {
            $journals = fetchJournalsForTeam($conn, $team_id);

            $subject = "You have been assigned to a team as an Editor";
            $body = "
                <p>Dear $editor_name,</p>
                <p>You have been assigned to the editorial team '$team_name' as an Editor.</p>
                <p>You will manage and assign tasks related to journals submitted to this team. The following journals are part of your team:</p>
                <ul>";

            foreach ($journals as $journal) {
                $body .= "<li><strong>Journal:</strong> {$journal['journal_name']}</li>";
            }

            $body .= "</ul>
                <p>You can now access the Editor Dashboard to manage tasks on time.</p>
                <p>Best regards,</p>
                <p>Your Editorial Team</p>";
        }

        // Load SMTP config from pub_config.ini
        $isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
        if ($isLocalhost) {
            $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
        } else {
            require_once(__DIR__ . '/../config_path.php');
            $config = parse_ini_file(CONFIG_PATH, true);
        }

        $mailConfig = $config['mail'] ?? [];

        if (empty($mailConfig['username']) || empty($mailConfig['password'])) {
            echo "Missing SMTP configuration!";
            exit;
        }

        // Send the email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $mailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mailConfig['username'];
            $mail->Password = $mailConfig['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $mailConfig['port'];

            $mail->setFrom($mailConfig['username'], $mailConfig['from_name']);
            $mail->addAddress($editor_email, $editor_name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            if ($mail->send()) {
                echo "Email has been sent to $editor_name";
            } else {
                echo "Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            echo "Email could not be sent. Exception: " . $e->getMessage();
        }
    } else {
        echo "Editor not found.";
    }
}
?>
