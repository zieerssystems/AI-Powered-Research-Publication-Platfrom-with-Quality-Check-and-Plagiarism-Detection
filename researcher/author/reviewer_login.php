<?php
session_start();
if (isset($_SESSION['reviewer_logged_in']) && $_SESSION['reviewer_logged_in'] === true) {
    header("Location: reviewer_dashboard.php");
    exit();
}

$error = isset($_GET['error']) ? $_GET['error'] : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Login</title>
    <style>
        * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #002147, #004080);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
}

.container {
    width: 100%;
    max-width: 400px;
    padding: 30px 20px;
    background: white;
    color: black;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    text-align: center;
}

.header {
    position: absolute;
    top: 10px;
    left: 20px;
    right: 20px;
    display: flex;
    justify-content: space-between;
    font-size: 18px;
}

.header a {
    text-decoration: none;
    color: white;
    margin: 0 10px;
}

.input-box {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

.btn {
    width: 100%;
    padding: 12px;
    background: #002147;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
    border-radius: 6px;
}

.btn:hover {
    background: #004080;
}

.error {
    color: red;
    margin-top: 10px;
}

.forgot-password {
    color: #002147;
    cursor: pointer;
    display: block;
    margin-top: 10px;
}

#forgot-password-form {
    margin-top: 20px;
}
.password-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-wrapper .toggle-password {
    position: absolute;
    right: 15px;
    cursor: pointer;
    user-select: none;
    font-size: 18px;
    color: #666;
}

    </style>
    <script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    passwordField.type = passwordField.type === "password" ? "text" : "password";
}
</script>

    <script>
        function showForgotPassword() {
            document.getElementById('forgot-password-form').style.display = 'block';
        }
        function showNewPasswordFields() {
            document.getElementById('verification-section').style.display = 'block';
        }
    </script>
</head>
<body>

<div class="header">
    <div>Reviewer Login</div>
    <div>
       <button onclick="history.back()">
        ← Back
    </button>
     <a href="../../publish.php">Home</a>
        <a href="article_register.php?role=Reviewer">Register</a>

    </div>
</div>

<div class="container">
    <h2>Reviewer Login</h2>
    <form action="process_reviewer_login.php" method="POST">
        <input type="text" class="input-box" name="username" placeholder="Username" required>
        <div class="password-wrapper">
    <input type="password" class="input-box" name="password" id="password" placeholder="Password" required>
    <span class="toggle-password" onclick="togglePassword()">
        👁️
    </span>
</div>

        <button type="submit" class="btn">Login</button>
    </form>

    <p style="font-size: 14px; margin-top: 10px;">
        Please enter your registered email and password to access your account.
        which you used for register in this platform <br>
        If you experience issues, use the forgot password option below.
    </p>
    <a href="reviewer_forgot_password.php">Forgot Password?</a>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <!-- <span class="forgot-password" onclick="showForgotPassword()">Forgot Password?</span> -->

    <!-- Forgot Password Form -->
    <!-- <div id="forgot-password-form">
        <h3>Reset Password</h3>
        <form action="send_verification.php" method="POST">
            <input type="email" class="input-box" name="email" placeholder="Enter your registered email" required>
            <button type="button" class="btn" onclick="showNewPasswordFields()">Send Verification Code</button>
        </form> -->

        <!-- Verification Code & New Password -->
        <!-- <div id="verification-section" style="display: none;">
            <form action="reset_password.php" method="POST">
                <input type="text" class="input-box" name="verification_code" placeholder="Enter verification code" required>
                <input type="password" class="input-box" name="new_password" placeholder="Enter new password" required>
                <input type="password" class="input-box" name="confirm_password" placeholder="Confirm new password" required>
                <button type="submit" class="btn">Reset Password</button>
            </form>
        </div>  -->
    </div>
</div>

</body>
</html>
