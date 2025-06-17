<?php 
session_start();
include('../../include/db_connect.php');

if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['email'])) {
    header("Location: editor_reset_password.php");
    exit();
}

$email = $_SESSION['email'];

$result = getUserByEmail($conn, $email);
$user = $result->fetch_assoc();

if (!$user) {
    $error = "User not found!";
} else {
    $user_id = $user['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
        } elseif (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            if (updateUserPassword_3($conn, $hashed_password, $user_id)) {
                session_destroy();
                header("Location: editor_login.php?success=Password updated successfully!");
                exit();
            } else {
                $error = "Failed to update password!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h3 class="text-center">Reset Password</h3>
            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <label>New Password:</label>
                <input type="password" name="password" class="form-control" required minlength="6">

                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control" required>

                <button type="submit" class="btn btn-success w-100 mt-3">Update Password</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>