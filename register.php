<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - Zieers</title>
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
    .logo a {
      font-size: 1.8rem;
      color: white;
      text-decoration: none;
      font-weight: bold;
    }
    nav ul {
      display: flex;
      list-style: none;
      gap: 1.5rem;
    }
    nav ul li a {
      color: white;
      text-decoration: none;
    }
    .container {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    .card {
      background: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 450px;
    }
    .card h2 {
      text-align: center;
      color: #1976d2;
      margin-bottom: 1rem;
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
    .toggle-link {
      text-align: center;
      margin-top: 1rem;
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
    .success {
      background-color: #4CAF50;
      color: white;
      padding: 1rem;
      margin: 1rem auto;
      text-align: center;
      border-radius: 5px;
      max-width: 500px;
    }
    .error {
      background-color: #f44336;
      color: white;
      padding: 1rem;
      margin: 1rem auto;
      text-align: center;
      border-radius: 5px;
      max-width: 500px;
    }
     footer {
            background: #002147;
            color: white;
            text-align: center;
            padding: 20px 10px;
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
.logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: #fff;
    }
  </style>
</head>
<body>
  <header>
   <div class="logo">
  <a href="index.php">
    <img src="images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
    <nav>
      <ul>
        <li><a href="publish.php">Home</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </nav>
  </header>
<div style="padding: 1rem 2rem;">
  <?php
  if (isset($_SESSION['success_message'])) {
      echo "<div class='success'>" . $_SESSION['success_message'] . "</div>";
      unset($_SESSION['success_message']);
  }

  if (isset($_SESSION['error_message'])) {
      echo "<div class='error'>" . $_SESSION['error_message'] . "</div>";
      unset($_SESSION['error_message']);
  }
  ?>
</div>
  <div class="container">
    <div class="card">
      <h2>Register</h2>
      <form action="register_process.php" method="POST">
        <input type="text" name="first_name" placeholder="First Name" required />
        <input type="text" name="middle_name" placeholder="Middle Name" />
        <input type="text" name="last_name" placeholder="Last Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <div class="password-container">
          <input type="password" name="password" placeholder="Password" id="regPassword" required />
          <i class="fas fa-eye toggle-eye" onclick="togglePassword('regPassword', this)"></i>
        </div>
        <div class="password-container">
          <input type="password" name="confirm_password" placeholder="Retype Password" id="confirmPassword" required />
          <i class="fas fa-eye toggle-eye" onclick="togglePassword('confirmPassword', this)"></i>
        </div>
        <button type="submit">Register</button>
        <div class="toggle-link">
          Already have an account? <a href="login.php" style="color: #1976d2;">Login</a>
        </div>
      </form>
    </div>
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
  <script>
  document.querySelector("form").addEventListener("submit", function(e) {
    const password = document.getElementById("regPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
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
