<?php
session_start();
require 'include/db_connect.php';

if (!isset($_SESSION['otp']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($entered_otp != $_SESSION['otp']) {
        $error = "Invalid OTP!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        $query = "UPDATE users SET password=? WHERE email=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $hashed_password, $email);
        if ($stmt->execute()) {
           $success = "Password updated successfully! Redirecting to login...";
unset($_SESSION['otp']);
unset($_SESSION['reset_email']);
echo "<script>
    setTimeout(function() {
        window.location.href = 'login.php';
    }, 3000); // Redirects after 3 seconds
</script>";

        } else {
            $error = "Error updating password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Zieers - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background: linear-gradient(to bottom right, #e3f2fd, #ffffff);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background: #002147;
      color: white;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: #fff;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 1.5rem;
    }

    nav ul li {
      cursor: pointer;
      font-weight: 500;
    }

    nav ul li a {
      text-decoration: none;
      color: white;
    }

    nav ul li a:hover {
      color: #90caf9;
    }

    .container {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    footer {
      background: #002147;
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: auto;
    }
.dropdown {
  position: relative;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: white;
  min-width: 160px;
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1;
  left: -80px; /* Move dropdown to the left */
  top: 100%;   /* Place it below the name */
  border-radius: 5px;
}


    .dropdown:hover .dropdown-content {
      display: block;
    }

    .dropdown-content li {
      padding: 10px;
    }

    .dropdown-content li a {
      color: #333;
      text-decoration: none;
    }

    .dropdown-content li a:hover {
      color: #1976d2;
    }
    .otp-form {
  background: #fff;
  padding: 2rem 3rem;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 450px;
}

.otp-form h2 {
  text-align: center;
  color: #002147;
  margin-bottom: 1.5rem;
}

.otp-form label {
  display: block;
  margin-top: 1rem;
  margin-bottom: 0.3rem;
  color: #333;
}

.otp-form input[type="text"],
.otp-form input[type="password"] {
  width: 100%;
  padding: 0.7rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

.otp-form button {
  width: 100%;
  background-color: #002147;
  color: white;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  margin-top: 1.5rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.otp-form button:hover {
  background-color: #003c7a;
}

.otp-form .error {
  color: red;
  font-size: 0.9rem;
  margin-top: 1rem;
  text-align: center;
}

.otp-form .success {
  color: green;
  font-size: 0.9rem;
  margin-top: 1rem;
  text-align: center;
}

  </style>
</head>

<body>
  <header>
    <div class="logo">
  <a href="index.php">
    <img src="images/zieers_logo_org.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>

    <nav>
      <ul>
        <li><a href="publish.php">Publish with Us</a></li>
        <li><a href="#">Internship</a></li>
        <li><a href="#">Course</a></li>
        <?php if (isset($_SESSION['first_name'])): ?>
  <li class="dropdown">
    <span><?php echo htmlspecialchars($_SESSION['first_name']); ?> <i class="fas fa-caret-down"></i></span>
    <ul class="dropdown-content">
      <li><a href="profile.php">View Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </li>
<?php else: ?>
  <li>
    <a href="login_choice.php" class="btn btn-success">Login</a>
    <!-- <a href="login.php?redirect=<?php echo urlencode($_SERVER['PHP_SELF']); ?>">Login</a> -->
  </li>
<?php endif; ?>

      </ul>
    </nav>
  </header>

<div class="container">
  <div class="otp-form">
    <form method="POST" id="resetForm">
      <h2>Verify OTP & Reset Password</h2>

      <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>

      <label for="otp">OTP</label>
      <input type="text" id="otp" name="otp" placeholder="Enter OTP" required>

     <div style="position: relative;">
  <input type="password" id="new_password" name="new_password" placeholder="New Password" disabled required>
  <i class="fas fa-eye toggle-password" toggle="#new_password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
</div>

<div style="position: relative;">
  <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" disabled required>
  <i class="fas fa-eye toggle-password" toggle="#confirm_password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
</div>

      <button type="submit">Reset Password</button>
    </form>
  </div>
</div>


  <!-- Footer -->
  <footer>
   <p onclick="window.open('https://www.zieers.com/', '_blank');">
    &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
</p>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
<script>
  // Enable password fields once OTP is filled
  document.getElementById('otp').addEventListener('input', function () {
    const otpVal = this.value.trim();
    const newPass = document.getElementById('new_password');
    const confirmPass = document.getElementById('confirm_password');

    if (otpVal.length >= 4) {
      newPass.disabled = false;
      confirmPass.disabled = false;
    } else {
      newPass.disabled = true;
      confirmPass.disabled = true;
    }
  });
</script>
<script>
  // Show/hide password
  document.querySelectorAll(".toggle-password").forEach(function(eye) {
    eye.addEventListener("click", function () {
      const input = document.querySelector(this.getAttribute("toggle"));
      const type = input.getAttribute("type") === "password" ? "text" : "password";
      input.setAttribute("type", type);
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  });

  // Password validation
  document.getElementById("resetForm").addEventListener("submit", function(e) {
    const password = document.getElementById("new_password").value;
    const confirm = document.getElementById("confirm_password").value;

    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!regex.test(password)) {
      e.preventDefault();
      alert("Password must be at least 8 characters long and include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.");
      return false;
    }

    if (password !== confirm) {
      e.preventDefault();
      alert("Passwords do not match.");
      return false;
    }
  });
</script>

</body>
</html>

