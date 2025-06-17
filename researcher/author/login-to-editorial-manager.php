<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];  // Store the current page
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Submit Your Paper</title>
  <link rel="stylesheet" href="../../styles.css">
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
.header-container {
display: flex;
align-items: center;
justify-content: space-between;
position: relative;
}

.header-container h1 {
    margin: 0;
    flex: 0 0 auto; 
    justify-content: space-between;/* Keeps Zieers on the left */
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
    
    .content-box {
      background-color: #ffffff;
      margin: 30px auto;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      width: 90%;
      max-width: 800px;
    }

    .main-heading {
      font-size: 26px;
      font-weight: 600;
      color: #002b5c;
      margin-bottom: 20px;
    }

    .submission-steps {
      list-style: none;
      padding-left: 0;
    }

    .submission-steps li {
      padding-left: 25px;
      position: relative;
      font-size: 16px;
      margin-bottom: 10px;
      color: #333;
    }

    .submission-steps li::before {
      content: "•";
      color: #0073e6;
      position: absolute;
      left: 0;
      font-size: 22px;
      top: 0;
    }

    .sub-heading {
      font-size: 22px;
      font-weight: 600;
      margin-bottom: 15px;
      color: #002b5c;
    }

    .content-box p {
      font-size: 15px;
      line-height: 1.6;
      color: #444;
    }

    .btn {
      display: inline-block;
      background-color: #0073e6;
      color: white;
      padding: 10px 16px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 500;
      transition: 0.3s;
    }

    .btn:hover {
      background-color: #005bb5;
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
.logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: #fff;
    }
  </style>
</head>
<body>

<header>
  <div class="container header-container">
    <div class="logo">
  <a href="../../publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
    <nav>
      <ul class="nav-links">
      <li><a href="../../publish.php">Home</a></li>
                <li><a href="../../services.php">Services</a></li>
                <li class="dropdown">
    <a href="#">For Users ▼</a>
    <ul class="dropdown-menu">
        <li><a href="for_author.php">For Author</a></li>
        <li><a href="for_reviewer.php">For Reviewer</a></li>
        <li><a href="for_editor.php">For Editor</a></li>
    </ul>
</li>
 <li class="dropdown">
    <a href="#">👤 <?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
    <ul class="dropdown-menu">
        <li><a href="profile.php">View Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</li>
      </ul>
    </nav>
  </div>
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
<!-- <div class="breadcrumb-container">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="../../index.php">Home</a></li>
      <li>&gt;</li>
      <li><a href="../">Researcher</a></li>
      <li>&gt;</li>
      <li><a href="./">Author</a></li>
      <li>&gt;</li>
      <li>Log in to Editorial Manager</li>
    </ul>
  </div>
</div> -->

<div class="content-box">
  <h1 class="main-heading">Register as Author</h1>
  <p>You can submit to most Zieers journals using an online system. The system you use will depend on the journal.</p>

  <ul class="submission-steps">
    <li>Follow the "Submit Your Paper" link on your <a href="journal-listing.php">journal homepage</a>.</li>
    <li>You’ll be taken to the relevant system and will be prompted to log in.</li>
    <li>If you’re using the system for the first time, follow the instructions to register.</li>
    <li>If you’re returning, log in with same email and password you used for register this platform .</li>
    <li>Once you’re in the system, you will be guided through the submission process.</li>
    <li>When you complete your submission, you’ll receive an email .Then using your email access Author dashboard.</li>
  </ul>

  <p>If you need to submit a revised paper as a result of the peer review process, you will also do this in the system.</p>
</div>

<div class="content-box">
  <h2 class="sub-heading">JournalFinder</h2>
  <p>Search the world’s leading source of academic journals to find the best match for your research. You can search using your abstract, keywords, or other details.</p>
  <a href="journalfinder.php" class="btn">Go to Journal Finder</a>
</div>
<!-- Paste this just before the closing </body> tag -->

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
        <li><a href="../../about-us.php">About Us</a></li>
        <li><a href="../../contact-us.php">Contact Us</a></li>
      </ul>
    </div>

    <!-- Legal + LinkedIn -->
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
  function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
  }
</script>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>
