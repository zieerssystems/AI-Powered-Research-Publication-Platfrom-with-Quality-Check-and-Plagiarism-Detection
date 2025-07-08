<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php"); // Database connection
require __DIR__ . '/../../vendor/autoload.php'; // PHPMailer

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


if (!isset($_SESSION['author_id'])) {
    die("Error: Unauthorized access. Please log in.");
}

$author_id = $_SESSION['author_id'];
$journal_id = $_SESSION['journal_id'];
$author_name = $_SESSION['first_name'] . " " . $_SESSION['last_name'];
$author_email = $_SESSION['author_email'];

// Instead of storing primary name/email in session, store other information
$_SESSION['author_details'] = [
    'affiliation' => $_SESSION['author_affiliation'] ?? 'Unknown Affiliation',
    'contact' => $_SESSION['author_contact'] ?? 'No contact provided',
    'research_interest' => $_SESSION['author_research_interest'] ?? 'Not specified'
];

$_SESSION['errors'] = []; // Store errors

// Validate required fields
if (empty($_POST['paper_title']) || empty($_POST['paper_abstract']) || empty($_POST['paper_keywords'])) {
    $_SESSION['errors']['general'] = "All fields (Title, Abstract, Keywords) are required.";
}

// Function to validate file uploads
function validateFile($file, $allowed_types, $field_name, $field_label) {
    if ($file['error'] == UPLOAD_ERR_NO_FILE) {
        $_SESSION['errors'][$field_name] = "$field_label is required.";
        return false;
    }

    if ($file['error'] == UPLOAD_ERR_OK) {
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['errors'][$field_name] = "$field_label must be a " . implode(", ", $allowed_types) . " file.";
            return false;
        }
        return true;
    }
    return false;
}

$paper_file_valid = validateFile($_FILES['paper_file'], ['pdf'], 'paper_file', 'Paper File');
$cover_letter_valid = validateFile($_FILES['cover_letter'], ['pdf'], 'cover_letter', 'Cover Letter');
$copyright_valid = validateFile($_FILES['copyright_agreement'], ['pdf'], 'copyright_agreement', 'Copyright Agreement');

if (!empty($_SESSION['errors'])) {
    header("Location: paper_submit.php"); // Redirect back if errors exist
    exit;
}

// File upload function
function uploadFile($file) {
    $new_file_name = uniqid() . "_" . basename($file['name']);
    $upload_path = __DIR__ . "/../../uploads/" . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $upload_path;
    }
    return null;
}

// Upload paper file and check for duplicate
$paper_file_path = uploadFile($_FILES['paper_file']);
$file_hash = hash_file('sha256', $paper_file_path);

// **Check if the file already exists in the database (Duplicate Prevention)**
if (isDuplicatePaper($conn, $file_hash)) {
    $_SESSION['errors']['paper_file'] = "Duplicate file detected. This paper has already been submitted.";
    unlink($paper_file_path); // Delete the uploaded file
    header("Location: paper_submit.php");
    exit;
}

// Upload other files
$cover_letter_path = uploadFile($_FILES['cover_letter']);
$copyright_agreement_path = uploadFile($_FILES['copyright_agreement']);
$supplementary_files = [];

// Upload supplementary files (optional)
if (!empty($_FILES['supplementary_files']['name'][0])) {
    foreach ($_FILES['supplementary_files']['tmp_name'] as $key => $tmp_name) {
        $supplementary_files[] = uploadFile([ 
            'name' => $_FILES['supplementary_files']['name'][$key], 
            'tmp_name' => $tmp_name, 
            'error' => $_FILES['supplementary_files']['error'][$key] 
        ]);
    }
}
$supplementary_files_str = implode(",", $supplementary_files);

// Insert paper into database
$paper_id = insertPaper($conn, $journal_id, $author_id, $_POST['paper_title'], $_POST['paper_abstract'], $_POST['paper_keywords'], $paper_file_path, $file_hash, $cover_letter_path, $copyright_agreement_path, $supplementary_files_str);

if (!$paper_id) {
    $_SESSION['errors']['general'] = "Paper submission failed.";
    header("Location: paper_submit.php");
    exit;
}

// Send Email to Primary Author
$mail_config = getMailConfig();

$mail = new PHPMailer(true);


$mail->isSMTP();
$mail->Host = $mail_config['host'];
$mail->SMTPAuth = true;
$mail->Username = $mail_config['username'];
$mail->Password = $mail_config['password'];
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = $mail_config['port'];


try {
    // Email to Primary Author
    $mail->setFrom($mail_config['username'], $mail_config['from_name']);
    $mail->addAddress($author_email, $author_name);
    $mail->Subject = "Paper Submission Confirmation";
    $mail->Body = "Dear {$author_name},\n\nYour paper titled '{$_POST['paper_title']}' has been successfully submitted.\n\nThank you for your submission!\n\nRegards,\nJournal Team";
    $mail->send();
} catch (Exception $e) {
    $_SESSION['errors']['email'] = "Error sending email to primary author: " . $mail->ErrorInfo;
    header("Location: paper_submit.php");
    exit;
}

// Insert Secondary Author (if exists)
if (!empty($_POST['second_author'])) {
    $second_author = [
        'name' => $_POST['second_author'],
        'email' => $_POST['second_author_email'],
        'affiliation_type' => $_POST['second_author_affiliation_type']
    ];
    insertPaperAuthor($conn, $paper_id, $second_author);
    
    // Send email to Secondary Author
    try {
        $mail->clearAddresses();
        $mail->addAddress($second_author['email'], $second_author['name']);
        $mail->Subject = "You have been added as a Secondary Author";
        $mail->Body = "Dear {$second_author['name']},\n\nYou have been added as a **Secondary Author** for the paper titled '{$_POST['paper_title']}' submitted by {$author_name}.\n\nThank you!\n\nRegards,\nJournal Team";
        $mail->send();
    } catch (Exception $e) {
        $_SESSION['errors']['email'] = "Error sending email to secondary author: " . $mail->ErrorInfo;
    }
}

// Insert Co-Authors (if exists)
foreach ($_POST as $key => $value) {
    if (strpos($key, 'author_') === 0 && strpos($key, '_name') !== false) {
        $num = str_replace(['author_', '_name'], '', $key);
        if (!empty($value)) {
            $co_author_name = $value;
            $co_author_email = $_POST["author_{$num}_email"] ?? '';
            $affiliation_type = $_POST["author_{$num}_affiliation_type"] ?? 'Co-Author';

            // Insert the co-author into paper_authors table
            insertPaperAuthor($conn, $paper_id, [
                'name' => $co_author_name,
                'email' => $co_author_email,
                'affiliation_type' => $affiliation_type
            ]);
            
            // Send email to Co-Author
            try {
                $mail->clearAddresses();
                $mail->addAddress($co_author_email, $co_author_name);
                if ($affiliation_type === "Secondary") {
                    $mail->Subject = "You have been added as a Secondary Author";
                    $mail->Body = "Dear {$co_author_name},\n\nYou have been added as a **Secondary Author** for the paper titled '{$_POST['paper_title']}' submitted by {$author_name}.\n\nThank you!\n\nRegards,\nJournal Team";
                } else {
                    $mail->Subject = "You have been added as a Co-Author";
                    $mail->Body = "Dear {$co_author_name},\n\nYou have been added as a **Co-Author** for the paper titled '{$_POST['paper_title']}' submitted by {$author_name}.\n\nThank you!\n\nRegards,\nJournal Team";
                }
                $mail->send();
            } catch (Exception $e) {
                $_SESSION['errors']['email'] = "Error sending email to co-author: " . $mail->ErrorInfo;
            }
        }
    }
}

// After inserting the paper and sending email to the authors:

// Now check and send email to Chief Editor (if team assigned)
$team_id = getEditorialTeamId($conn, $journal_id);

    if ($team_id) {
    $chiefEditor = getChiefEditorInfo($conn, $team_id);

    if ($chiefEditor) {
        $chief_email = $chiefEditor['email'];
        $chief_name = $chiefEditor['first_name'] . ' ' . $chiefEditor['last_name'];
        $journal_name = getJournalName($conn, $journal_id);

        // Send email to Chief Editor
        try {
            $mail->clearAddresses();
            $mail->addAddress($chief_email, $chief_name);
            $mail->Subject = "New Paper Submitted to {$journal_name}";
            $mail->Body = "Dear {$chief_name},\n\nA new paper titled '{$_POST['paper_title']}' has been submitted to '{$journal_name}'.\n\nPlease review it in the system.\n\nThank you.\n\nRegards,\nJournal System";
            $mail->send();
        } catch (Exception $e) {
            $_SESSION['errors']['chief_editor_email'] = "Error sending email to chief editor: " . $mail->ErrorInfo;
        }
    }
}

// else if no team assigned, just do nothing (no error, no email)

// ✅ **Redirect on success**
header("Location: author_dash_login.php?success=1");
exit;
?>
