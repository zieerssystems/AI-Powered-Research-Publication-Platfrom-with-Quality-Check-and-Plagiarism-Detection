<?php
session_start();
include('../../include/db_connect.php');

$email = '';
$step = 1;
$error = '';
$note = "Note: Changing this password will affect your entire platform login.";

// Reset session if user wants to start over
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// If email & user_id already exist in session (from prior submission)
if (isset($_SESSION['user_id']) && isset($_SESSION['email'])) {
    $step = 2;
    $email = $_SESSION['email'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_email'])) {
        $email = trim($_POST['email']);
        $_SESSION['email'] = $email;

        $user_id = getUserIdByEmail($conn, $email);

        if ($user_id && isReviewer($conn, $user_id)) {
            $_SESSION['user_id'] = $user_id;
            $step = 2;
        } else {
            $error = "Email not found or not a reviewer.";
            $step = 1;
        }

    } elseif (isset($_POST['reset_password'])) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $error = "Passwords do not match!";
            $step = 2;
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters!";
            $step = 2;
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $user_id = $_SESSION['user_id'];

            if (updateUserPassword_2($conn, $user_id, $hashed_password)) {
                session_destroy();
                header("Location: reviewer_login.php?success=Password updated successfully!");
                exit();
            } else {
                $error = "Failed to update password.";
                $step = 2;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        header {
            background: #002147;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-links a {
            color: #ECF0F1;
            text-decoration: none;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #002147;
            list-style: none;
            padding: 0;
            margin: 0;
            z-index: 100;
            border-radius: 5px;
        }

        .dropdown-menu li a {
            display: block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu li a:hover {
            background: rgb(2, 51, 107);
        }

        footer {
            background: #002147;
            color: white;
            text-align: center;
            padding: 20px 10px;
        }

        footer p {
            cursor: pointer;
        }

        footer p:hover {
            text-decoration: underline;
        }

        .site-footer {
            background-color: #002147;
            color: white;
            padding: 40px 10%;
            font-family: 'Poppins', sans-serif;
        }

        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 30px;
        }

        .footer-column {
            flex: 1;
            min-width: 250px;
        }

        .footer-column h3,
        .footer-column h4 {
            margin-bottom: 15px;
            color: #ffffff;
        }

        .footer-column p,
        .footer-column a,
        .footer-column li {
            font-size: 14px;
            color: #ccc;
            line-height: 1.6;
            text-decoration: none;
        }

        .footer-column a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .footer-column ul {
            list-style: none;
            padding-left: 0;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 40px;
            border-top: 1px solid #444;
            padding-top: 20px;
            font-size: 13px;
            color: #aaa;
        }

        .social-link {
            display: flex;
            align-items: center;
            color: #ccc;
            text-decoration: none;
            margin-top: 10px;
        }

        .social-link:hover {
            color: white;
            text-decoration: underline;
        }

        .social-icon {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }
    </style>
</head>
<body class="bg-light">
<header>
    <div class="logo">
  <a href="index.php">
    <img src="images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
    <nav class="nav-links">
        <a href="../../publish.php">Home</a>
        <a href="help.php">Help</a>
        <div class="dropdown">
            <a href="#">For Users ▼</a>
            <ul class="dropdown-menu">
                <li><a href="../../for_author.php">For Author</a></li>
                <li><a href="../../for_reviewer.php">For Reviewer</a></li>
                <li><a href="../../for_editor.php">For Editor</a></li>
            </ul>
        </div>
    </nav>
</header>
<div style="margin: 20px 0 0 20px;">
    <button onclick="history.back()" style="
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
    ">
        ← Back
    </button>
</div>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h3 class="text-center">Reset Password</h3>
            <?php if (!empty($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <?php if ($step === 1): ?>
                <form method="POST">
                    <label class="form-label">Enter Your Reviewer Email:</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                    <button type="submit" name="check_email" class="btn btn-primary w-100 mt-3">Verify Email</button>
                </form>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
    <label class="form-label">New Password:</label>
    <div class="input-group">
        <input type="password" name="password" class="form-control" id="password" required minlength="6">
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
            <i class="bi bi-eye"></i>
        </button>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Confirm Password:</label>
    <div class="input-group">
        <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password', this)">
            <i class="bi bi-eye"></i>
        </button>
    </div>
</div>


                    <button type="submit" name="reset_password" class="btn btn-success w-100 mt-3">Update Password</button>
                    <p class="text-muted mt-2" style="font-size: 13px;"><?= $note ?></p>
                </form>
                <a href="?reset=1" class="btn btn-link mt-2">Use another email</a>
            <?php endif; ?>
        </div>
    </div>
</div>


<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-column">
            <h3>Zieers</h3>
            <p><strong>Email:</strong> <a href="mailto:support@zieers.com">support@zieers.com</a></p>
            <p><strong>Phone:</strong> +91-9341059619</p>
            <p><strong>Address:</strong><br>
                Zieers Systems Pvt Ltd,<br>
                5BC-938, 1st Block, Hennur Road,<br>
                2nd Cross Rd, Babusabpalya, Kalyan Nagar,<br>
                Bengaluru, Karnataka 560043
            </p>
        </div>

        <div class="footer-column">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="../../about-us.php">About Us</a></li>
                <li><a href="../../contact-us.php">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Legal</h4>
            <ul>
                <li><a href="../../privacy_policy.php">Privacy Policy</a></li>
            </ul>
            <a href="https://www.linkedin.com/company/your-company-name" target="_blank" class="social-link">
                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/linkedin/linkedin-original.svg" alt="LinkedIn" class="social-icon">
            </a>
        </div>
    </div>

    <div class="footer-bottom">
        <p onclick="window.open('https://www.zieers.com/', '_blank');">
            &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
        </p>
    </div>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}
</script>
<script>
document.querySelector("form").addEventListener("submit", function(e) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

    if (!passwordRegex.test(password)) {
      alert("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.");
      e.preventDefault();
    } else if (password !== confirmPassword) {
      alert("Passwords do not match.");
      e.preventDefault();
    }
  });
</script>
</body>
</html>
