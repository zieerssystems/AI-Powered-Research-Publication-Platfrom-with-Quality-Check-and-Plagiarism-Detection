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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>For Reviewer - Zieers</title>
    
    <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Poppins', sans-serif;
    background-color: #eef2f3;
    color: #333;
}

h1, h2, h3 {
    font-family: 'Poppins', sans-serif;
}

.container {
    width: 90%;
    margin: auto;
    max-width: 1200px;
}

/* Header */
header {
    background: #002147;
    color: white;
    padding: 15px 0;
}

.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    width: 100%;
    flex-wrap: wrap;
}

.logo {
    font-size: 1.8rem;
    font-weight: bold;
    color: #fff;
    display: flex;
    align-items: center;
}

.logo img {
    height: 50px;
}

nav {
    display: flex;
    align-items: center;
}

nav ul {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 20px;
}

.nav-links li a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    padding: 10px 20px;
    transition: background-color 0.3s ease;
}

.nav-links li a:hover {
    background-color: transparent;
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

    .banner {
        position: relative;
        z-index: 1;
    }
}

        /* Banner Section */
        .banner {
            position: relative;
            width: 100%;
            height: 75vh;
           background: linear-gradient(to right, rgba(0, 33, 71, 0.8), rgba(0, 33, 71, 0.6)), url('images/new6.jpg') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }
        .banner-content {
            background: rgba(73, 120, 177, 0.6);
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            animation: slideText 3s infinite alternate;
        }
        @keyframes slideText {
            0% { transform: translateY(-10px); opacity: 0.8; }
            50% { transform: translateY(5px); opacity: 1; }
            100% { transform: translateY(-10px); opacity: 0.8; }
        }
        .banner-content h2 {
            font-size: 42px;
            font-weight: 700;
            line-height: 1.3;
        }
        .banner-content p {
            font-size: 18px;
            margin-top: 10px;
        }
        .register-btn {
            display: inline-block;
            background-color: #ffcc00;
            color: #002147;
            font-weight: bold;
            padding: 12px 25px;
            margin-top: 20px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .register-btn:hover {
            background-color: #ffaa00;
        }

        /* Zoom Image Slider */
        .banner-image {
            width: 45%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .banner-image img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: translate(-50%, -50%) scale(1);
            opacity: 0;
            transition: transform 3s ease-in-out, opacity 1s ease-in-out;
        }
        .banner-image img.active {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1.08);
        }

        @keyframes slideText {
            0% { transform: translateY(-10px); opacity: 0.8; }
            50% { transform: translateY(5px); opacity: 1; }
            100% { transform: translateY(-10px); opacity: 0.8; }
        }
       /* Image Styling */
        .reviewer-img {
            width: 100%;
            max-width: 500px;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }

        /* Section Spacing */
        .container > div {
            margin-bottom: 60px;
        }
        h2 {
    font-size: 28px; /* Increase size */
    font-weight: 700;
    color: #002147;
    margin-bottom: 15px;
}

        p {
            font-size: 17px;
            line-height: 1.7;
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

 </style>
</head>
<body>
<header>
    <div class="header-container">
         <div class="logo">
  <a href="../../publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
        <div class="hamburger-menu" onclick="toggleMenu()">&#9776;</div>
        <nav>
            <ul class="nav-links">
                <li><a href="publish.php">Home</a></li>
                <li><a href="services.php">Services</a></li>
                <li class="dropdown">
    <a href="#">For Users ▼</a>
    <ul class="dropdown-menu">
        <li><a href="for_author.php">For Author</a></li>
        <li><a href="for_editor.php">For Editor</a></li>
    </ul>
</li>
<li><a href="journal.php">Journal Catalog</a></li>
                <li><a href="researcher/author/reviewer_login.php">Reviewer Dashboard</a></li>
                <li class="dropdown">
    <a href="#"><?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
    <ul class="dropdown-menu">
        <li><a href="profile.php">View Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</li>
            </ul>
        </nav>
    </div>
</header>

<!-- Updated Banner Section -->
<div class="banner">
    <div class="banner-content">
        <h2>Be A Reviewer</h2>
        <p>Welcome to Reviewer Hub — your source of information, guidance and support for reviewing with Zieers.<br>
        Volunteer and organize your reviews, claim certificates and activate your complimentary access.</p>
        <a href="researcher/author/reviewer_login.php" class="register-btn">Login Into Your Personal Reviewer Hub</a>
    </div>
</div>

<!-- Updated Welcome Section with Image Zoom -->
<div class="container" style="padding-top: 40px;">
    <div style="display: flex; gap: 40px; align-items: center; flex-wrap: wrap; margin-bottom: 60px;">
        <!-- Left: Heading -->
        <div style="flex: 1;">
            <h2>Welcome to Reviewer Hub</h2>
        </div>

        <!-- Right: Paragraph + Zoom Image -->
        <div style="flex: 1; position: relative; overflow: hidden; border-radius: 10px;">
            <p style="font-size: 16px; line-height: 1.6; margin-top: 20px;">
            Zieers is an emerging publication platform designed to empower both new and experienced reviewers. No prior reviewing experience is required — we welcome your willingness to contribute to the scholarly community.
            <br><br>
                Our Reviewer Hub is designed to support your indispensable contribution to the manuscript evaluation process by providing you with tools, information, and guidance tailored to enhance your reviewing journey.
            </p>
        </div>
    </div>
    <!-- Peer Review Process Section -->
    <div style="display: flex; gap: 40px; align-items: center; flex-wrap: wrap; margin-bottom: 60px;">
        <div style="flex: 1;">
            <h2 style="margin-bottom: 15px;">The Peer Review Process</h2>
            <p style="font-size: 16px; line-height: 1.6;">
                The peer review system exists to validate academic work, helps to improve the quality of published research, and increases networking possibilities within research communities. <br><br>
                Read more to find out about how the system works and the different types of peer review that you may encounter.
            </p>
            <a href="researcher/author/Peer_Review.php" class="register-btn">Learn More</a>
        </div>
        <div style="flex: 1;">
        <img src="images/new2.jpg" alt="Reviewer Info" class="reviewer-img">
        </div>
    </div>

    <!-- Role of Reviewer Section -->
    <div style="display: flex; gap: 40px; align-items: center; flex-wrap: wrap; margin-bottom: 60px;">
        <div style="flex: 1;">
            <img src="images/new6.jpg" alt="Reviewer Role" style="width: 100%; max-width: 500px; height: auto; border-radius: 10px;">
        </div>
        <div style="flex: 1;">
            <h2 style="margin-bottom: 15px;">The Role of a Reviewer</h2>
            <p style="font-size: 16px; line-height: 1.6;">
                Peer review — and reviewers — are at the heart of the academic publishing process. Find out why reviewers perform this vital role, how they are recognized and how you can volunteer to review yourself. <br><br>
                You can also find information and resources on diversity, equity and inclusion.
            </p>
            <a href="researcher/author/Reviewer_Role.php" class="register-btn">Learn More</a>
        </div>
    </div>
  
    <!-- Effective Peer Reviewing Guide Section -->
    <div style="margin-bottom: 60px;">
        <h2 style="margin-bottom: 15px;">A Guide to Effective Peer Reviewing</h2>
        <p style="font-size: 16px; line-height: 1.6;">
            Peer review is an incredibly important process. Let us guide you through each stage and offer you a selection of resources, tips and tricks to make your reviewing experience as positive as it can be. <br><br>

            <strong>Before you begin:</strong> Before you accept or decline an invitation to review, there are a number of questions to consider. <br><br>

            <strong>Managing your review:</strong> When you sit down to write your review, make sure you familiarize yourself with any journal-specific guidelines. <br><br>

            <strong>Structuring your review:</strong> Your review will help the editor decide whether or not to publish the article. It will also aid the author and allow them to improve their manuscript. <br><br>

            <strong>After your review:</strong> Once you have delivered your review, you might want to make use of Zieers’ Reviewer Hub to ensure that you receive credit for your work. <br><br>

            <strong>Tools and resources:</strong> We have a number of high quality resources to help you on your reviewing journey including courses, articles and guidelines.
        </p>
        <a href="researcher/author/How_to_Review.php" class="register-btn">Learn More</a>
    </div>

    <!-- Reviewers' Update Section -->
    <div style="display: flex; gap: 40px; align-items: center; flex-wrap: wrap; margin-bottom: 60px;">
        <div style="flex: 1;">
            <h2 style="margin-bottom: 15px;">Editorial Process & Reviewers' Update</h2>
            <p style="font-size: 16px; line-height: 1.6;">
                Keep in touch with Reviewers' Update. <br><br>
                See the latest stories and advice and find out about industry developments, policies and initiatives of interest to our reviewers.
            </p>
        </div>
        <div style="flex: 1;">
        <img src="images/new6.jpg" alt="Reviewer Info" class="reviewer-img">
        </div>
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
  function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
  }
</script>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
        </div>
    </footer>


<script>
        document.addEventListener("DOMContentLoaded", function () {
            let images = document.querySelectorAll(".banner-image img");
            let index = 0;
            
            function slideImages() {
                images.forEach((img, i) => img.classList.remove("active"));
                images[index].classList.add("active");
                index = (index + 1) % images.length;
            }
            
            setInterval(slideImages, 3000);
            slideImages();
        });
    </script>
    <script>
    function toggleMenu() {
        document.querySelector(".nav-links").classList.toggle("active");
    }
</script>
</body>
</html>
