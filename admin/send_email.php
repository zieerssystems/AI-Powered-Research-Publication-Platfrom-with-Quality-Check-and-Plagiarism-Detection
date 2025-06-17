<?php
require_once(__DIR__ . "/../vendor/autoload.php");
include(__DIR__ . "/../include/db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Validate GET parameters
if (!isset($_GET['id']) && !isset($_GET['editor_id'])) {
    die(json_encode(["status" => "error", "message" => "Invalid Request: Missing ID"]));
}

if (isset($_GET['id'])) {
    $person_id = intval($_GET['id']);
    $role = "reviewer";
    $redirect_page = "reviewer_details.php";
} else {
    $person_id = intval($_GET['editor_id']);
    $role = "editor";
    $redirect_page = "editor_details.php";
}

$person = getPersonById($conn, $role, $person_id);
if (!$person) {
    die(json_encode(["status" => "error", "message" => ucfirst($role) . " not found."]));
}
// if (!$person) {
//     die(json_encode(["status" => "error", "message" => ucfirst($role) . " not found."]));
// }

// Generate contract
$contract_url = "http://localhost/my_publication_site/admin/contracts/generate_contract.php?" . ($role === "reviewer" ? "id=" . $person_id : "editor_id=" . $person_id);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $contract_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Check file existence
$contract_path = __DIR__ . "/contracts/contract_{$role}_{$person_id}.pdf";
if (!file_exists($contract_path)) {
    die(json_encode(["status" => "error", "message" => "Failed to generate contract file for $role."]));
}


// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'anjuscaria7@gmail.com';
    $mail->Password = 'dlvr dkbu sdob fqfu';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('anjuscaria7@gmail.com', 'Zieers System Pvt Ltd');
    $mail->addAddress($person['email'], "{$person['first_name']} {$person['last_name']}");
    $mail->Subject = ucfirst($role) . " Contract - Zieers System Pvt Ltd";

    $payment_message = ($role === "reviewer")
        ? (($person['payment_type'] === "paid") ? "This contract includes payment terms." : "This is a voluntary position.")
        : (($person['editor_payment_type'] === "paid") ? "This contract includes payment terms." : "This is an unpaid position.");

    $mail->Body = "Dear {$person['first_name']} {$person['last_name']},\n\n"
        . "I hope this email finds you well.\n\n"
        . "Please find attached your contract as a " . ucfirst($role) . " with Zieers System Pvt Ltd.\n\n"
        . "$payment_message\n\n"
        . "To proceed, kindly review, sign, and upload the contract within the next 2 days using the following link:\n"
        . ($role === "reviewer"
            ? "http://localhost/my_publication_site/researcher/verify_reviewer.php"
            : "http://localhost/my_publication_site/editor/verify_editor.php")
        . "\n\nIf you have any questions or require further clarification, feel free to reach out.\n\n"
        . "Thank you for your attention to this matter.\n\n"
        . "Best regards,\n"
        . "Zieers HR Team\n"
        . "Zieers System Pvt Ltd";

    $mail->addAttachment($contract_path);

    if ($mail->send()) {
        updateContract($conn, $role, $person_id); // ⬅ Use helper function
        header("Location: $redirect_page");
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Email not sent."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
