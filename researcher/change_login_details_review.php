<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

$reviewer_id = $_SESSION['reviewer_id'];
$reviewer = getReviewerDetails($reviewer_id);

$error = "";
$success = "";

// Verify old password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify_old_password'])) {
    $old_password = $_POST['old_password'];
    if (password_verify($old_password, $reviewer['password'])) {
        $_SESSION['verified'] = true;
    } else {
        $error = "❌ Incorrect old password!";
    }
}

// Update password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_credentials']) && isset($_SESSION['verified'])) {
    $new_password_hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    if (updateReviewerPassword($reviewer['user_id'], $new_password_hashed)) {
        mail($reviewer['email'], "Account Updated", "Your password has been changed.", "From: info@zieers.org");
        session_destroy();
        header("Location: reviewer_login.php?msg=Password updated successfully");
        exit();
    } else {
        $error = "❌ Update failed.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Login Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .btn-back {
        display: inline-block;
        margin: 20px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #4b6cb7, #182848);
        color: white;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, #182848, #4b6cb7);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
</style>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<a href="reviewer_dashboard.php" class="btn-back">← Back to Reviewer Dashboard</a>
<div class="card p-4 shadow-sm w-50">
    <h3 class="text-center mb-4">🔑 Change Login Details</h3>

    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

    <!-- Show password verification form if not verified -->
    <?php if (!isset($_SESSION['verified'])): ?>
        <form method="POST">
            <label class="form-label">Enter Old Password</label>
            <input type="password" name="old_password" class="form-control mb-3" required>
            <button type="submit" name="verify_old_password" class="btn btn-primary w-100">Verify</button>
        </form>
    <?php else: ?>
        <!-- Show username & password update form if verified -->
        <form method="POST">
    <label class="form-label">New Password</label>
    <input type="password" name="password" class="form-control mb-3" required>
    <button type="submit" name="update_credentials" class="btn btn-success w-100">Update</button>
</form>

    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="forgot_password_review.php" class="text-decoration-none">Forgot Password?</a>
    </div>
</div>

</body>
</html>
