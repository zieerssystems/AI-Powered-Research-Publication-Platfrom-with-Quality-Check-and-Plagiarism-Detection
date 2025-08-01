<?php
require_once(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php');
require_once(__DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php');
require_once(__DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php');
include(__DIR__ . '/../include/db_connect.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost)
  $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);
else{
  require_once(__DIR__ . '/../config_path.php');
$config = parse_ini_file(CONFIG_PATH, true);
}
// $config = parse_ini_file(__DIR__ . '/../../../private/pub_config.ini', true);

// Now access mail config:
$mail_host = $config['mail']['host'];
$mail_user = $config['mail']['username'];
$mail_pass = $config['mail']['password'];
$mail_port = $config['mail']['port'];
$mail_from = $config['mail']['username'];
$mail_from_name = $config['mail']['from_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Zieers | Email Verification</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      height: 100vh;
      background: #f5f7fa;
    }

    .sidebar {
      background: linear-gradient(135deg, #00264d, #005580);
      color: white;
      width: 280px;
      padding: 40px 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .sidebar h1 {
      font-size: 36px;
      font-weight: bold;
      text-align: center;
      margin-bottom: 10px;
    }

    .sidebar p {
      text-align: center;
      font-size: 14px;
      color: #d9ecff;
    }

    .main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .container {
      background: #ffffff;
      padding: 40px;
      width: 100%;
      max-width: 500px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
    }

    input[type="email"] {
      width: 90%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 20px;
    }

    .btn {
      background: #007bff;
      color: #fff;
      padding: 12px;
      width: 95%;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #0056b3;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }

      .container {
        width: 90%;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h1>Zieers</h1>
    <p>Empowering Research, Enhancing Knowledge</p>
    <p>Constract Upload</p>
  </div>

  <div class="main">
    <div class="container">
      <h2>Email Verification</h2>
      <form method="POST">
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <br>
        <button class="btn" type="submit">Send Verification Code</button>
      </form>
    </div>
  </div>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $row = getReviewerByEmail($conn, $email); // use the new function

    if ($row) {
        $contract_status = $row['contract_status'];

        if ($contract_status == 'not_sent') {
            echo "<script>alert('Your contract has not been sent yet!'); window.location.href='index.php';</script>";
            exit();
        } elseif ($contract_status == 'signed') {
            echo "<script>alert('Your contract is already verified!'); window.location.href='index.php';</script>";
            exit();
        } else {
            $verification_code = rand(1000, 9999);
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['email'] = $email;
                        // Send email with PHPMailer
           $mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $mail_host;
    $mail->SMTPAuth = true;
    $mail->Username = $mail_user;
    $mail->Password = $mail_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $mail_port;

    $mail->setFrom($mail_from, $mail_from_name);
    $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Verification Code for Contract Upload";
                $mail->Body = "Dear Reviewer,<br><br>
                Your verification code is: <b>$verification_code</b><br><br>
                Please enter this code to proceed with the contract upload process.<br><br>
                Regards,<br>Zieers Team";

                $mail->SMTPDebug = 2;  // Show detailed SMTP debug output
$mail->Debugoutput = 'html';

                if ($mail->send()) {
                    echo "<script>
    alert('Verification code sent to your email!');
    window.location.href='verify_code.php?id=" . $row['id'] . "';
</script>";                } else {
                    echo "<script>alert('Email sending failed. Please try again.');</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Email not found in the system!');</script>";
    }
}
?>
