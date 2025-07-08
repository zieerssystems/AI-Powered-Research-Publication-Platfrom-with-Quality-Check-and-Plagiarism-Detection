<?php 
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    die("Unauthorized access.");
}

include("../include/db_connect.php");
require_once("../vendor/autoload.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost)
  $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
else{
  require_once(__DIR__ . '/../config_path.php');
$config = parse_ini_file(CONFIG_PATH, true);
}

// $config = parse_ini_file(__DIR__ . "/../../../private/pub_config.ini", true);
if (!$config || !isset($config['mail']['username'], $config['mail']['password'])) {
    die("Mail configuration error.");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role']; // 'editor' or 'reviewer'
    $request_message = trim($_POST['issue_message']); // Admin's reupload request message
    $contract_file = $_POST['contract_file'];

    if (empty($request_message)) {
        die("Error: Please provide a reason for the reupload request.");
    }

    // Determine the correct table based on the role
    if ($role === 'editor') {
        $table = "editors";
        $id_column = "editor_id";
        $upload_link = "/../editor/upload_editor_contract.php?editor_id=$user_id";
    } else {
        $table = "reviewers";
        $id_column = "id";
        $upload_link = "/../reviewer/upload_reviewer_contract.php?id=$user_id";
    }

    // Fetch user details using function
    $result = getUserDetailsByRole($conn, $table, $id_column, $user_id);
    if ($row = $result->fetch_assoc()) {
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];
    } else {
        die("Error: User not found.");
    }

    // Update contract_status to 'reupload' using function
    if (!updateContractStatus($conn, $table, $id_column, $user_id)) {
        die("Error: Failed to update contract status.");
    }

    // Path to the contract file
    $contract_path = "../admin/contracts/signed/" . $contract_file;
    if (!file_exists($contract_path)) {
        die("Error: Contract file not found.");
    }

    // Send email to the user
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $config['mail']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['mail']['username'];
        $mail->Password = $config['mail']['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['mail']['port'];

        $mail->setFrom($config['mail']['username'], $config['mail']['from_name']);
        $mail->addAddress($email, "$first_name $last_name");
        // Attach the contract file
        $mail->addAttachment($contract_path, basename($contract_file));

        $mail->isHTML(true);
        $mail->Subject = "Request for Contract Reupload";

        $mail->Body = "Dear $first_name,<br><br>
            Your contract requires reuploading due to the following reason provided by the admin:<br>
            <blockquote><strong>$request_message</strong></blockquote>
            <br>
            Please upload a new signed contract using the link below:<br>
            <a href='$upload_link' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;'>Reupload Contract</a><br><br>
            If you have any questions, please contact us at adzieers@gmail.com.<br><br>
            Regards,<br>Zieers Admin";

        if ($mail->send()) {
            echo "Reupload request sent successfully!";
        } else {
            echo "Email sending failed.";
        }
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
