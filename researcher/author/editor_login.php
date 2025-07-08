<?php
session_start();
// $error = isset($_SESSION['error']) ? $_SESSION['error'] : "";
// unset($_SESSION['error']

// Redirect if already logged in
if (isset($_SESSION['editor_id']) && isset($_SESSION['editor_role'])) {
    if ($_SESSION['editor_role'] === 'Chief Editor') {
        header("Location: chief-dashboard.php");
        exit;
    } elseif ($_SESSION['editor_role'] === 'Editor') {
        header("Location: editor_dashboard.php");
        exit;
    }
}

if (isset($_SESSION['error'])) {
    echo "<p style='color:red; font-weight:bold;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']); // Clear it after showing once
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #002147, #004080);
            color: white;
            text-align: center;
            padding: 50px;
        }
        .container {
            width: 350px;
            margin: auto;
            padding: 20px;
            background: white;
            color: black;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            font-size: 18px;
        }
        .header a {
            text-decoration: none;
            color: white;
            margin: 0 10px;
        }
        .input-box {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #002147;
            color: white;
            border: none;
            cursor: pointer;
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
            display: none;
            margin-top: 20px;
        }
        .password-wrapper {
  position: relative;
  width: 100%;
}
.password-wrapper input[type="password"],
.password-wrapper input[type="text"] {
  width: 100%;
  padding-right: 40px; /* space for icon */
}
.password-wrapper .toggle-password {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  cursor: pointer;
  width: 24px;
  height: 24px;
  fill: #555;
  user-select: none;
}
.password-wrapper .toggle-password:hover {
  fill: #000;
}
    </style>
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
    <div>Editor Login</div>
    <div>
        <a href="/../../publish.php">Home</a> 
        <a href="javascript:history.back()">← Back</a>
    </div>
</div>

<div class="container">
    <h2>Editor Login</h2>
    <form action="process_editor_login.php" method="POST">
        <input type="text" class="input-box" name="email" placeholder="Enter your Email" required>
       <div class="password-wrapper">
    <input type="password" class="input-box" id="password" name="password" placeholder="Password" required>
    <svg class="toggle-password" id="togglePassword" viewBox="0 0 24 24">
      <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 
               5 2.24 5 5-2.24 5-5 5z"/>
      <circle cx="12" cy="12" r="2.5"/>
    </svg>
</div>
        <button type="submit" class="btn">Login</button>
    </form>
    <p style="font-size: 14px; margin-top: 10px;">
        Please enter your registered email and password to access your account.
         which you used for register in this platform  <br> first You have to register as editor by click register buttton below
        < <a href="article_register.php?role=Editor">Register</a>
        If you experience issues, use the forgot password option below.
    </p>
    <a href="editor_forgot_password.php">Forgot Password?</a>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

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
        </div> 
    </div> -->
</div>
<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', () => {
    // Toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // Optionally toggle icon color or fill here
    togglePassword.style.fill = type === 'password' ? '#555' : '#004080';
});
</script>
</body>
</html>
