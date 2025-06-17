<?php
require_once(__DIR__ . "/../vendor/autoload.php");
include(__DIR__ . "/../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Validate GET parameters
if (!isset($_GET['id']) && !isset($_GET['user_id'])) {
    die(json_encode(["status" => "error", "message" => "Invalid Request: Missing ID"]));
}

if (isset($_GET['id'])) {
    $person_id = intval($_GET['id']);
    $role = "user";  // Or any role based on your application (e.g., reviewer, editor, etc.)
    $query = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $redirect_page = "user_details.php";  // Redirect page for user
} elseif (isset($_GET['user_id'])) {
    $person_id = intval($_GET['user_id']);
    $role = "admin";
    $query = $conn->prepare("SELECT first_name, last_name, email FROM admins WHERE admin_id = ?");
    $redirect_page = "admin_details.php";  // Redirect page for admin
}

$query->bind_param("i", $person_id);
$query->execute();
$result = $query->get_result();
$person = $result->fetch_assoc();

if (!$person) {
    die(json_encode(["status" => "error", "message" => ucfirst($role) . " not found."]));
}

// ✅ Generate contract or any other file (if needed) or handle any other processing.
$file_path = __DIR__ . "/files/contract_{$role}_{$person_id}.pdf";
if (!file_exists($file_path)) {
    die(json_encode(["status" => "error", "message" => "File generation failed."]));
}

// ✅ Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'anjuscaria7@gmail.com';  // Your Gmail email
    $mail->Password = 'dlvr dkbu sdob fqfu';  // Replace with actual App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('anjuscaria7@gmail.com', 'Your Company Name');
    $mail->addAddress($person['email'], "{$person['first_name']} {$person['last_name']}");
    $mail->Subject = ucfirst($role) . " Contract - Your Company Name";

    $mail->Body = "Dear {$person['first_name']},\n\n"
            . "Please find attached your contract as a " . ucfirst($role) . ".\n\n"
            . "You are required to sign and upload the contract within 2 days.\n\n"
            . "Best Regards,\nYour Company HR Team";
    
    $mail->addAttachment($file_path);  // Attach the generated contract or file

    if ($mail->send()) {
        // ✅ Update contract status in the database (if applicable)
        if ($role === "user") {
            $update_query = $conn->prepare("UPDATE users SET contract_status = 'sent' WHERE id = ?");
        } else {
            $update_query = $conn->prepare("UPDATE admins SET contract_status = 'sent' WHERE admin_id = ?");
        }
        $update_query->bind_param("i", $person_id);
        $update_query->execute();

        // ✅ Redirect to the correct details page
        header("Location: $redirect_page");
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Email not sent."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
