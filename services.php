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
    <title>Services - Zieers</title>
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


        /* Styling for Publish Section */
        .publish-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 50px 10%;
            overflow: hidden;
            background: white;
            border-radius: 10px;
            box-shadow: linear-gradient(135deg, rgb(76, 82, 93), rgb(202, 211, 220));
        }
        /* Hide hamburger by default */
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
}

        .services-header {
            background:white
            color: white;
            padding: 60px 20px;
            text-align: center;
        }
        .services-header h2 {
            font-size: 36px;
            font-weight: bold;
        }
        .services-container {
            padding: 40px 20px;
        }
        .service-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(231, 225, 225, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .service-box:hover {
            transform: scale(1.05);
        }
        .service-box img {
            width: 45%;
            height: 200px;
            object-fit: cover;
            border-radius: 20px;
            transition: transform 0.3s ease;
        }
        .service-box img:hover {
            transform: scale(1.05);
            transform-origin: center center;
        }
        .service-box .service-content {
            width: 50%;
        }
        .service-title {
            font-size: 22px;
            font-weight: bold;
            color: #2980b9;
            margin-bottom: 10px;
        }
        .service-description {
            font-size: 16px;
            color: #555;
        }
        .service-description a {
            color:rgb(88, 124, 147);
            text-decoration: none;
            font-weight: bold;
        }
        .service-description a:hover {
            color:rgb(58, 87, 194);
        }
        
      /* Footer Styling */
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
     <!-- Header -->
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

    <!-- Services Header Section -->
    <div class="services-header">
        <h2>Our Services</h2>
        <h3>Discover how Zieers makes the publishing process easy and efficient for authors, editors, and reviewers.</p>
        </h3></div>

    <!-- Services Section -->
    <div class="services-container">
        <!-- Service 1: Dashboard Tracking -->
        <div class="service-box">
            <img src="images/new7.jpg" alt="Dashboard Tracking">
            <div class="service-content">
                <div class="service-title">Dashboard Tracking</div>
                <div class="service-description">
                    <p>Track your submissions and progress with our easy-to-use dashboard. Stay updated on your paper's review status and feedback.</p>
                </div>
            </div>
        </div>

        <!-- Service 2: Paper Submission -->
        <div class="service-box">
            <div class="service-content">
                <div class="service-title">Paper Submission</div>
                <div class="service-description">
                    <p>Submit your paper in a few simple steps. Our streamlined submission process makes it easy to upload, track, and revise your manuscripts.</p>
                </div>
            </div>
            <img src="images/new6.jpg" alt="Paper Submission">
        </div>

        <!-- Service 3: Peer Review Process -->
        <div class="service-box">
            <img src="images/new2.jpg" alt="Peer Review">
            <div class="service-content">
                <div class="service-title">Peer Review</div>
                <div class="service-description">
                    <p>Our peer review system ensures that your paper undergoes a rigorous evaluation process, ensuring high standards for academic publications.</p>
                </div>
            </div>
        </div>

        <!-- Service 4: Editorial Support -->
        <div class="service-box">
            <div class="service-content">
                <div class="service-title">Editorial Support</div>
                <div class="service-description">
                    <p>Our expert editorial team is here to assist you with proofreading, formatting, and enhancing your manuscript for submission.</p>
                </div>
            </div>
            <img src="images/new4.jpg" alt="Editorial Support">
        </div>

        <!-- Service 5: Publication Analytics -->
        <div class="service-box">
            <img src="images/new8.jpg" alt="Publication Analytics">
            <div class="service-content">
                <div class="service-title">Publication Analytics</div>
                <div class="service-description">
                    <p>Get insights into the performance of your publications with our detailed analytics tool, tracking readership and citations.</p>
                </div>
            </div>
        </div>

        <!-- Service 6: Open Access Support -->
        <div class="service-box">
            <div class="service-content">
                <div class="service-title">Open Access Support</div>
                <div class="service-description">
                    <p>We provide options for open access publishing to ensure your work is easily accessible and widely read by the academic community.</p>
                </div>
            </div>
            <img src="images/new3.jpg" alt="Open Access">
        </div>
    </div>

    <!-- Footer -->
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
        document.querySelector(".nav-links").classList.toggle("active");
    }
</script>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>
