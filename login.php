<?php
session_start();

// If user is already logged in, redirect to dashboard or desired page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Zieers</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
    body {
      background: linear-gradient(to bottom right, #e3f2fd, #ffffff);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .navbar {
      background: #002147;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
    }
    .navbar a.logo {
      font-size: 1.8rem;
      font-weight: bold;
      text-decoration: none;
      color: white;
    }
    .navbar a.register-btn {
      background: white;
      color: #1976d2;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      text-decoration: none;
      font-weight: bold;
    }
    .navbar a.register-btn:hover {
      background: #e3f2fd;
    }

    .card {
      background: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      margin: auto;
      margin-top: 2rem;
    }
    .card h2 {
      margin-bottom: 1rem;
      text-align: center;
      color: #1976d2;
    }
    .card form {
      display: flex;
      flex-direction: column;
    }
    .card input {
      padding: 0.8rem;
      margin: 0.5rem 0;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
    }
    .card button {
      background: #1976d2;
      color: #fff;
      padding: 0.8rem;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      margin-top: 1rem;
    }
    .card button:hover {
      background: #1565c0;
    }
    .password-container {
      position: relative;
    }
    .password-container input {
      width: 100%;
      padding-right: 2.5rem;
    }
    .toggle-eye {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #888;
      cursor: pointer;
    }
    .toggle-link {
      color: #1976d2;
      text-align: center;
      margin-top: 1rem;
      cursor: pointer;
    }
    .toggle-link:hover {
      text-decoration: underline;
    }
    .success {
      background-color: #4CAF50;
      color: white;
      padding: 1rem;
      margin: 1rem auto;
      text-align: center;
      border-radius: 5px;
      width: 100%;
      max-width: 400px;
    }
    .error {
      background-color: #f44336;
      color: white;
      padding: 1rem;
      margin: 1rem auto;
      text-align: center;
      border-radius: 5px;
      width: 100%;
      max-width: 400px;
    }
     footer {
      background: #002147;
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: auto;
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
<body>

  <nav class="navbar">
   <div class="logo">
  <a href="publish.php">
    <img src="images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>

    <a href="register.php" class="register-btn">Register</a>
  </nav>
<?php
// Display success/error messages from session (positioned below navbar)
if (isset($_SESSION['success_message'])) {
    echo "<div class='success'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo "<div class='error'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}
?>

  <div class="card" id="loginForm">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form action="login_process.php" method="POST">
      <input type="email" name="email" placeholder="Email" required />
      <div class="password-container">
        <input type="password" name="password" placeholder="Password" id="loginPassword" required />
        <i class="fas fa-eye toggle-eye" onclick="togglePassword('loginPassword', this)"></i>
      </div>
      <button type="submit">Login</button>
      <div class="toggle-link" onclick="window.location.href='register.php'">
        Don't have an account? Register
        </div>
         <div class="toggle-link" onclick="window.location.href='forgot_password.php'">
          Forgot Password?
      </div>
    </form>
  </div>

  <footer class="site-footer">
  <div class="footer-container">
    <!-- Contact Info -->
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

    <!-- Quick Links -->
    <div class="footer-column">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="about-us.php">About Us</a></li>
        <li><a href="contact-us.php">Contact Us</a></li>
      </ul>
    </div>

    <!-- Legal + LinkedIn -->
    <div class="footer-column">
      <h4>Legal</h4>
      <ul>
        <li><a href="privacy_policy.php">Privacy Policy</a></li>
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
    function togglePassword(inputId, icon) {
      const input = document.getElementById(inputId);
      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }
  </script>
</body>
</html>
