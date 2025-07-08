<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['editor_id'];
$user_id = getEditorUserId($editor_id);

if (!$user_id) {
    die("❌ Editor not found.");
}

$user = getEditorUserDetails($user_id);

$error = "";
$success = "";

// Step 3: Verify old password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify_old_password'])) {
    $old_password = $_POST['old_password'];
    if (password_verify($old_password, $user['password'])) {
        $_SESSION['verified'] = true;
    } else {
        $error = "❌ Incorrect old password!";
    }
}

// Step 4: Update password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_credentials']) && isset($_SESSION['verified'])) {
    $new_password_hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (updateEditorPassword($user_id, $new_password_hashed)) {
        mail($user['email'], "Account Updated", "Your login password has been changed.", "From: info@zieers.org");
        session_destroy();
        header("Location: editor_login.php?msg=Login details updated successfully");
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
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<div class="card p-4 shadow-sm w-50">
    <h3 class="text-center mb-4">🔐 Change Password</h3>
    <a href="editor_dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

    <!-- Old Password Form -->
    <?php if (!isset($_SESSION['verified'])): ?>
        <form method="POST">
            <label class="form-label">Enter Old Password</label>
            <input type="password" name="old_password" class="form-control mb-3" required>
            <button type="submit" name="verify_old_password" class="btn btn-primary w-100">Verify</button>
        </form>
    <?php else: ?>
        <!-- New Password Form -->
        <form method="POST">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control mb-3" required>
            <button type="submit" name="update_credentials" class="btn btn-success w-100">Update Password</button>
        </form>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
    </div>
</div>
</body>
</html>
