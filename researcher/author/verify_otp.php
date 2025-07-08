<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: author_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        $_SESSION['loggedin'] = true; // Mark session as logged in
        header("Location: author_dashboard.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f7f9fc; }
        .container {
            width: 300px; margin: 100px auto; background: white;
            padding: 20px; text-align: center; border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        .btn { background-color: #007bff; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; border-radius: 5px; }
        .btn:hover { background-color: #0056b3; }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Enter OTP</h2>
    <form action="" method="POST">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit" class="btn">Verify OTP</button>
    </form>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>

</body>
</html>
