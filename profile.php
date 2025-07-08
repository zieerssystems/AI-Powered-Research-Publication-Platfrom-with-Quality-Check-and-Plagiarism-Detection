<?php
session_start();
include("include/db_connect.php");

$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = getUserProfile($user_id, $conn);
    $user = $result;
    $_SESSION['first_name'] = $result['first_name']; // Used in header
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Profile - Zieers</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    /* Your existing CSS here (same as you provided) */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #eef2f3;
      color: #333;
    }
    header {
      background: #002147;
      padding: 20px;
      color: white;
    }
    .login-btn {
      background-color: #004080;
      color: white;
      padding: 8px 16px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
    }
    .login-btn:hover {
      background-color: #0066cc;
    }
    .header-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
      z-index: 9999;
    }
    .header-container h1 {
      margin: 0;
      flex: 0 0 auto;
    }
    nav {
      position: absolute;
      right: 20px;
      left: auto;
      transform: none;
    }
    nav ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    nav ul li {
      margin-right: 20px;
    }
    nav ul li a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }
    nav ul li a:hover {
      background-color: #004080;
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
    background-color: #004080;
}
    
    .container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }
    .profile {
      background: #ffffff;
      padding: 2rem;
      border-radius: 1rem;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
    }
    .profile h2 {
      color: #002147;
      margin-bottom: 1rem;
    }
    .profile p {
      font-size: 1.1rem;
      margin: 0.5rem 0;
    }
    .radio-group {
      margin-top: 2rem;
    }
    .radio-group button {
      margin: 0.5rem;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border: none;
      background: #002147;
      color: white;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .radio-group button:hover {
      background-color: #0056b3;
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
  <!-- Header -->
  <header>
    <div class="header-container">
      <div class="logo">
  <a href="../../publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
      <nav>
        <ul>
          <li><a href="publish.php">Home</a></li>
          <li><a href="services.php">Service</a></li>
          <li class="dropdown">
    <a href="#">For Users ▼</a>
    <ul class="dropdown-menu">
        <li><a href="for_author.php">For Author</a></li>
        <li><a href="for_reviewer.php">For Reviewer</a></li>
        <li><a href="for_editor.php">For Editor</a></li>
    </ul>
</li>
        <?php if ($user): ?>
  <li class="dropdown">
    <span class="welcome-text"><?php echo htmlspecialchars($user['first_name']); ?>▼</span>
    <ul class="dropdown-menu">
      <li><a href="profile.php">View Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </li>
<?php else: ?>
  <li><a class="login-btn" href="login.php">Login</a></li>
<?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>
<br>
<br>
<button onclick="history.back()">
        ← Back
    </button>
  <!-- Profile Content -->
  <div class="container">
    <div class="profile">
      <h2>Your Profile</h2>

      <?php if ($user): ?>
        <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

        <div class="radio-group">
          <button onclick="handleRoleClick('editor')">Be an Editor</button>
          <button onclick="handleRoleClick('reviewer')">Be a Reviewer</button>
          <button onclick="handleRoleClick('author')">Be an Author</button>
        </div>
      <?php else: ?>
        <p>Please <a href="login.php">login</a> to view your profile and access dashboards.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer -->
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
          <li><a href="about-us.php">About Us</a></li>
          <li><a href="contact-us.php">Contact Us</a></li>
        </ul>
      </div>

      <div class="footer-column">
        <h4>Legal</h4>
        <ul>
          <li><a href="privacy_policy.php">Privacy Policy</a></li>
        </ul>
        <a href="https://www.linkedin.com/company/your-company-name" target="_blank" class="social-link">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/linkedin/linkedin-original.svg" alt="LinkedIn" class="social-icon" />
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

    function handleRoleClick(role) {
      <?php if ($user): ?>
        // Redirect logged-in users to respective dashboards
        if (role === 'editor') {
          window.location.href = 'researcher/author/editor_dashboard.php';
        } else if (role === 'reviewer') {
          window.location.href = 'researcher/author/reviewer_dashboard.php';
        } else if (role === 'author') {
          window.location.href = 'researcher/author/author_dashboard.php';
        }
      <?php else: ?>
        // If not logged in, redirect to login page
        window.location.href = 'login.php';
      <?php endif; ?>
    }
  </script>
</body>
</html>
