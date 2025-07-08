<?php
session_start();
if (!isset($_SESSION['otp']) || !isset($_SESSION['email'])) {
    header("Location: reviewer_forgot_password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp'])) {
    if ($_POST['otp'] == $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reviewer_reset_password.php"); // Redirect to reset password page
        exit();
    } else {
        $error = "Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h3 class="text-center">Verify OTP</h3>
            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST">
                <label>Enter OTP:</label>
                <input type="number" name="otp" class="form-control" required>
                <button type="submit" class="btn btn-primary w-100 mt-3">Verify</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
