<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['chief_editor_id'];
$editor = getEditorDetail($editor_id);

$error = "";
$success = "";
$showFields = false;

// Verify old password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_old_password'])) {
    $old_password = $_POST['old_password'];
    if (password_verify($old_password, $editor['password'])) {
        $showFields = true;
        $_SESSION['verified'] = true;
    } else {
        $error = "❌ Incorrect old password!";
    }
}

// Update credentials
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_credentials']) && isset($_SESSION['verified'])) {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_id = getUserIdByEditorId($editor_id);

    if ($user_id && updateUserPassword($user_id, $new_password)) {
        mail($editor['email'], "Account Updated", "Your login details have changed.", "From: info@zieers.org");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Login Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<div class="card p-4 shadow-sm w-50">
    <h3 class="text-center mb-4">🔑 Change Login Details</h3>
    <a href="chief-dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>
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
        <!-- Only show new password field -->
<form method="POST">
    <label class="form-label">New Password</label>
    <input type="password" name="password" class="form-control mb-3" required>

    <button type="submit" name="update_credentials" class="btn btn-success w-100">Update</button>
</form>

    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
    </div>
</div>

</body>
</html>
