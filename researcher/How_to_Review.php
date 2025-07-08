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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>How to Review | Zieers</title>
  <style>
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
.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.header-container h1 {
    margin: 0;
    font-size: 24px;
    color: white;
}
nav {
    position: static; /* Reset absolute positioning */
    transform: none;  /* Reset transform */
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
/* Dropdown Menu */
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
.breadcrumb-container {
      background: #e6ecf0;
      padding: 10px 0;
    }

    .breadcrumb {
      list-style: none;
      display: flex;
      gap: 8px;
      font-size: 14px;
    }

    .breadcrumb li a {
      color: #002147;
      text-decoration: none;
    }
    h1, h2, h3 {
        font-family: 'Poppins', sans-serif;
        color: #002147;
    }
    .container {
        width: 90%;
        margin: auto;
        max-width: 1200px;
    }
    /* Mobile Nav */
    .hamburger-menu {
        display: none;
        font-size: 30px;
        cursor: pointer;
        color: white;
    }
    @media (max-width: 768px) {
        .nav-links {
            display: none;
            flex-direction: column;
            background: #002147;
            position: absolute;
            top: 60px;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            z-index: 1000;
        }
        .nav-links.active {
            display: flex;
        }
        .hamburger-menu {
            display: block;
            z-index: 1100;
        }
    }
    
    .inline {
  display: flex;
  justify-content: center;
  margin: 20px auto; /* centers it horizontally */
  width: 100%;
}


.inline nav {
  background: #ddd;
  padding: 10px 20px;
  display: flex;
  gap: 15px;
  font-weight: bold;
  border-radius: 6px;
  max-width: 100%;
  flex-wrap: wrap; /* allows wrap on smaller screens */
}


.inline nav a {
  color: #002244;
  text-decoration: none;
}

ul {
  list-style-type: disc;
  padding-left: 20px;
}

    .section {
      margin-bottom: 40px;
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
        } .site-footer {
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
.main h1{
  text-align: center;
}
  .logo {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 50px;
    }

  </style>
</head>
<body>

<header>
    <div class="header-container">
         <div class="logo">
    <a href="../../publish.php">
      <img src="../../images/logo.png" alt="Zieers Logo">
    </a>
  </div>
        <nav>
            <ul class="nav-links">
                <li><a href="../../publish.php">Home</a></li>
                <li><a href="../../services.php">Services</a></li>
                 <li class="dropdown">
    <a href="#">For Users ▼</a>
    <ul class="dropdown-menu">
        <li><a href="../../for_author.php">For Author</a></li>
        <li><a href="../../for_reviewer.php">For Reviewer</a></li>
        <li><a href="../../for_editor.php">For Editor</a></li>
    </ul>
</li>
<li class="dropdown">
    <a href="#">👤 <?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
    <ul class="dropdown-menu">
        <li><a href="../../profile.php">View Profile</a></li>
        <li><a href="../../logout.php">Logout</a></li>
    </ul>
</li>
</ul>
</nav>
</div>
</header>
<div class="breadcrumb-container">
    <ul class="breadcrumb">
      <li><a href="../../for_reviewer.php">Reviewer</a></li>
      <li>&gt;</li>
      <li>How To Review</li>
    </ul>
  </div>
<div class="main">
  <h1>How To Review</h1>
</div>

<div class="inline">
<nav>
  <a href="#before">Before You Begin |</a>
  <a href="#managing">Managing Your Review |</a>
  <a href="#structuring">Structuring Your Review |</a>
  <a href="#after">After Review </a>
</nav>
</div>

<div class="container">

  <div class="section" id="before">
    <h2>1. Before You Begin</h2>
    <ul>
      <li>Check if the article fits your expertise. Accept only if you can deliver a quality review.</li>
      <li>Disclose any conflict of interest to the editor.</li>
      <li>Ensure you can meet the review deadline.</li>
      <li>Explore reviewer training like Zieers' Peer Review Academy (coming soon).</li>
    </ul>
  </div>

  <div class="section" id="managing">
    <h2>2. Managing Your Review</h2>
    <p>Manuscripts are confidential. Do not share or upload them to external tools like AI systems.</p>
    <p>You will access your review through Zieers' submission platform (Editorial Manager equivalent).</p>
    <p>Ensure you check for any journal-specific review formats.</p>
  </div>

  <div class="section" id="structuring">
    <h2>3. Structuring Your Review</h2>
    <ul>
      <li>Importance of the research question</li>
      <li>Originality and contribution</li>
      <li>Methodology soundness</li>
      <li>Writing quality and data interpretation</li>
      <li>Statistical rigor and reproducibility</li>
    </ul>
    <p>Also assess gender reporting if applicable, following SAGER guidelines.</p>
  </div>

  <div class="section" id="after">
    <h2>4. After the Review</h2>
    <p>Submit your feedback through Zieers' Editorial Panel. Be clear, constructive, and respectful in comments.</p>
    <p>Do not disclose manuscript content or decisions publicly or on social platforms.</p>
  </div>

  <!-- <div class="section" id="tools">
    <h2>5. Tools & Resources</h2>
    <ul>
      <li><a href="#">Zieers Reviewer Guidelines</a></li>
      <li><a href="#">Reviewer Dashboard Login</a></li>
      <li><a href="#">Zieers Peer Review Course</a></li>
    </ul>
  </div>
<!--  -->
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
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
<script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }
</script>

</body>
</html>
