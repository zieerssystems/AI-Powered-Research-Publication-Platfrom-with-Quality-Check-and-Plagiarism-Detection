<?php
session_start();
include("include/db_connect.php");
$user = null;
if (isset($_SESSION['user_id'])) {
    $user = getUserById($_SESSION['user_id']);
}
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Zieers</title>
   <style>
     body {
            font-family: 'Poppins', sans-serif;
            background-color: #eef2f3;
            color: #333;
        }
        /* Header and Navigation Styling */
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
}

.header-container h1 {
    margin: 0;
    flex: 0 0 auto; /* Keeps Zieers on the left */
}
/* Ensure dropdown appears above banner */
.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 9999; /* Ensure the header is above other elements */
}

nav {
    position: absolute;
    right: 20px; /* or 10px or any padding from right */
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

.contact-section {
    text-align: center;
    padding: 50px;
    background-color: #fff;
}

.contact-section h2 {
    font-size: 30px;
}

.contact-form {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.contact-form label {
    font-size: 14px;
    font-weight: bold;
}

.contact-form input, .contact-form textarea {
    padding: 10px;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.contact-form button {
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.contact-form button:hover {
    background-color: #0056b3;
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
        .hamburger-menu {
    display: none;
    font-size: 30px;
    cursor: pointer;
    color: white;
}

/* Mobile styles */
@media (max-width: 768px) {
    .nav-links {
        display: none;
        flex-direction: column;
        background: #002147;
        position: absolute;
        top: 70px;
        left: 0;
        width: 100%;
        text-align: center;
        padding: 10px 0;
    }

    .nav-links.active {
        display: flex;
    }

    .hamburger-menu {
        display: block;
    }
} /* Dropdown Styles */
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
  <div class="header-container">
    <div class="logo">
  <a href="publish.php">
    <img src="images/logo.png" alt="Zieers Logo" style="height: 50px;">
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
    <section class="contact-section">
        <h2>Contact Us</h2>
        <p>If you have any questions or feedback, please feel free to reach out to us through the form below.</p>
        <form action="submit_contact.php" method="POST" class="contact-form">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Your Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Submit</button>
        </form>
    </section>

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
  function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
  }
</script>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
    <?php if (isset($_SESSION['contact_success'])): ?>
<script>
    alert("<?php echo $_SESSION['contact_success']; ?>");
</script>
<?php unset($_SESSION['contact_success']); ?>
<?php endif; ?>

</body>
</html>
