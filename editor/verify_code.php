<?php 
session_start();
if (!isset($_GET['editor_id']) || empty($_GET['editor_id'])) {
    die("Error: Editor ID is missing.");
}
$editor_id = intval($_GET['editor_id']);

if (!isset($_SESSION['verification_code']) || !isset($_SESSION['email'])) {
    echo "<script>alert('Unauthorized access! Please verify first.'); window.location.href='verify_editor.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_code = $_POST['code'];
    if ($user_code == $_SESSION['verification_code']) {
        unset($_SESSION['verification_code']); // Clear session
        echo "<script>
            alert('Verification successful! Redirecting...');
            window.location.href='upload_editor_contract.php?editor_id=" . $editor_id . "';
        </script>";
        exit();
    } else {
        echo "<script>alert('Invalid code! Try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify Code - Zieers</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      min-height: 100vh;
      background: #f0f2f5;
    }

    .sidebar {
      width: 300px;
      background: linear-gradient(135deg, #00264d, #005580);
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 40px 20px;
    }

    .sidebar h1 {
      font-size: 32px;
      text-align: center;
      margin-bottom: 10px;
    }

    .sidebar p {
      text-align: center;
      font-size: 14px;
      color: #d1e7ff;
      margin-top: 10px;
    }

    .main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .container {
      width: 100%;
      max-width: 450px;
      background: white;
      padding: 35px 25px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .container h2 {
      margin-bottom: 15px;
      color: #222;
    }

    .container p {
      font-size: 14px;
      color: #555;
      margin-bottom: 25px;
    }

    input[type="text"] {
      width: 90%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 16px;
    }

    .btn {
      width: 95%;
      padding: 12px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn:hover {
      background: #0056b3;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }
      .main {
        width: 100%;
        padding: 30px 15px;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h1>Zieers</h1>
    <p>Empowering Research, Enhancing Knowledge</p>
    <p><strong>Contract Upload</strong></p>
  </div>

  <div class="main">
    <div class="container">
      <h2>Email Verification</h2>
      <p>Please enter the verification code sent to your email.</p>
      <form method="POST">
        <input type="text" name="code" placeholder="Enter 4-digit code" required />
        <br />
        <button class="btn" type="submit">Verify</button>
      </form>
    </div>
  </div>
</body>
</html>
