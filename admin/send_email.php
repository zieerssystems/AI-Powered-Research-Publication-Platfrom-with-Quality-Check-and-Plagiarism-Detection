<?php
require_once(__DIR__ . "/../vendor/autoload.php");
include(__DIR__ . "/../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load config based on host
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost) {
    $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
} else {
    require_once(__DIR__ . '/../config_path.php');
    $config = parse_ini_file(CONFIG_PATH, true);
}

$mailConfig = $config['mail'] ?? [];

if (empty($mailConfig['username']) || empty($mailConfig['password'])) {
    die(json_encode(["status" => "error", "message" => "Mail configuration is incomplete."]));
}

// Accept only POST with file
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['contract'])) {
    die(json_encode(["status" => "error", "message" => "Invalid request."]));
}

// Identify user role
if (isset($_POST['editor_id'])) {
    $person_id = intval($_POST['editor_id']);
    $role = "editor";
    $redirect_page = "editor_details.php";
} elseif (isset($_POST['id'])) {
    $person_id = intval($_POST['id']);
    $role = "reviewer";
    $redirect_page = "reviewer_details.php";
}
else {
    die(json_encode(["status" => "error", "message" => "Missing reviewer or editor ID."]));
}

// Get person details
$person = getPersonById($conn, $role, $person_id);
if (!$person) {
    die(json_encode(["status" => "error", "message" => ucfirst($role) . " not found."]));
}

// Upload the file
$upload_dir = __DIR__ . "/contracts/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}

$uploaded_file = $_FILES['contract'];
$filename = basename($uploaded_file['name']);
$target_path = $upload_dir . "{$role}_{$person_id}_" . time() . "_" . $filename;

if (!move_uploaded_file($uploaded_file['tmp_name'], $target_path)) {
    die(json_encode(["status" => "error", "message" => "Failed to save uploaded file."]));
}

// Send email
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
    $mail->addAddress($person['email'], "{$person['first_name']} {$person['last_name']}");
    $mail->Subject = ucfirst($role) . " Contract - Zieers System Pvt Ltd";

    $mail->Body = "Dear {$person['first_name']} {$person['last_name']},\n\n"
        . "Please find attached your contract as a " . ucfirst($role) . " with Zieers System Pvt Ltd.\n\n"
        . "Kindly review, sign, and upload the contract within the next 2 days using the following link:\n"
        . ($role === "reviewer"
            ? "https://publish.zieerseducations.com/my_publication_site/researcher/verify_reviewer.php"
            : "https://publish.zieerseducations.com/my_publication_site/editor/verify_editor.php")
        . "\n\nRegards,\nZieers HR Team";

    $mail->addAttachment($target_path);

    if ($mail->send()) {
        updateContract($conn, $role, $person_id);
        header("Location: $redirect_page");
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Email sending failed."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
