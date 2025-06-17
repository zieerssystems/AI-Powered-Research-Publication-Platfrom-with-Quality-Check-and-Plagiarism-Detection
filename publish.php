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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Publish with Us</title>
    <link rel="stylesheet" href="styles.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />

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

.banner {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f4f7;
    z-index: 1; /* Ensure the banner is below the header */
}
 .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: #fff;
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
        .right-image {
            width: 45%;
            transition: transform 0.8s ease-in-out, opacity 0.8s ease-in-out;
            opacity: 0;
            transform: translateY(50px) scale(1.1);
        }
        .left-content {
            width: 50%;
            transition: opacity 0.8s ease-in-out, transform 0.8s ease-in-out;
            opacity: 0;
            transform: translateX(-50px);
            font-size: 18px;
        }
        .publish-content.visible .right-image {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .publish-content.visible .left-content {
            opacity: 1;
            transform: translateX(0);
        }
        .welcome-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 50px 10%;
  background: #f4f4f4;
  color: #1a1a1a;
  gap: 40px;
  flex-wrap: wrap;
}
.welcome-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 50px 10%;
  background: #f4f4f4;
  color: #002147;
  gap: 40px;
  flex-wrap: wrap;
 
}

.welcome-left, .welcome-right {
  flex: 1;
  min-width: 300px;
}

        /* .banner {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f4f7;
} */

.banner-image {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.85);
    z-index: 1;
    animation: melt 10s ease-in-out infinite alternate;
}

/* Light overlay blending with background */
.overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, rgba(255,255,255,0.4), rgba(255,255,255,0.2));
    mix-blend-mode: lighten;
    z-index: 2;
}

/* Sliding and jumping text */
.banner-text {
    position: relative;
    z-index: 3;
    font-size: 2rem;
    color: #002147;
    font-weight: bold;
    text-shadow: 1px 1px 5px rgba(255,255,255,0.7);
}

        .slider {
            position: relative;
            width: 300px;
            height: 250px;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .slide img {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            object-fit: cover;
        }

        .active {
            opacity: 1;
        }

        /* Navigation Buttons */
        .prev, .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 20px;
            padding: 8px;
            border-radius: 50%;
        }

        .prev {
            left: 10px;
        }

        .next {
            right: 10px;
        }

        .prev:hover, .next:hover {
            background-color: rgba(255, 255, 255, 0.8);
            color: black;
        }
        .sections {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  padding: 60px 8%;
  background: linear-gradient(to right, #e0eafc, #cfdef3);
  border-radius: 12px;
  margin-top: 40px;
}

.card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* Enhanced shadow effect */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  flex: 1 1 calc(50% - 30px); /* 2 per row with 30px gap */
  max-width: calc(50% - 30px); /* ensures it doesn’t stretch more */
  text-align: left;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
}

.card h3 a {
  color: #002147;
  text-decoration: none;
  font-size: 20px;
  position: relative;
  display: inline-block;
  padding-right: 18px;
  transition: color 0.3s ease;
}

.card h3 a::after {
  content: "→";
  position: absolute;
  right: 0;
  top: 0;
  font-size: 18px;
  opacity: 1; /* Always visible */
  transform: translateX(0); /* No initial offset */
  transition: transform 0.3s ease, color 0.3s ease;
}


.card h3 a:hover {
  text-decoration: underline;
  color: #004080;
}

.card h3 a:hover::after {
  opacity: 1;
  transform: translateX(0);
}

.card p {
  font-size: 15px;
  color: #333;
  margin-top: 10px;
}@media (max-width: 768px) {
  .card {
    flex: 1 1 100%;
    max-width: 100%;
  }
}
/* Container for both image and content */
.publish-content {
    display: flex;
    align-items: center; /* Vertically center content and image */
    justify-content: space-between; /* Distribute space between image and content */
    gap: 20px; /* Space between image and text */
}

/* Image styles */
.right-image { 
    width: 45%;
    max-width: 400px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Content styles */
.left-content {
    max-width: 50%;
    font-size: 18px;
}

/* Optional: Additional space around the section */
.publish-content {
    padding: 20px;
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
        /* Dropdown Styles */
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
    
    <!-- Header -->
    <header>
  <div class="header-container">
     <div class="logo">
  <a href="index.php">
    <img src="images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
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

    <!-- Banner Section -->
    <div class="banner">
    <img src="images/new5.jpg" alt="Researchers publishing in Zieers" class="banner-image" loading="lazy">

    <div class="overlay"></div>
    <div class="banner-text">Empowering Researchers Worldwide</div>
</div>

<!-- Welcome Section -->
<section class="welcome-section">
  <div class="welcome-left">
    <h1>Welcome to Zieers</h1>
  </div>
  <div class="welcome-right">
    <p>Thank you for considering Zieers as your publishing partner. Whether you're a journal or book author, we've got you covered.</p>
  </div>
</section>

<!-- Updated Content Cards Section -->
<section class="sections">
  <div class="card">
    <h3>
        <a href="submit-page.php">Submit Your Paper</a>
    </h3>
    <p>Begin your publication journey with our intuitive submission process. Upload your research, track progress in real time, and get expert support at every stage—from submission to final decision.</p>
  </div>
  
  <div class="card">
    <h3>
        <a href="journal.php">Explore Journals</a>
    </h3>
    <p>Discover a wide range of high-quality, peer-reviewed journals across multiple disciplines. Find the perfect journal for your research using our smart Journal Finder tool.</p>
  </div>
  
  <div class="card">
    <h3>
        <a href="for_reviewer.php">Reviewer Resources</a>
    </h3>
    <p>Gain access to essential guidelines, review templates, and tools designed to help reviewers provide constructive and timely evaluations. Your contribution is vital to maintaining scholarly excellence.</p>
  </div>
  
  <div class="card">
    <h3>
        <a href="for_editor.php">Editor Guidelines</a>
    </h3>
    <p>Manage manuscripts with ease using our streamlined editorial dashboard. Set review timelines, assign reviewers, and oversee the decision-making process efficiently and transparently.</p>
  </div>
</section>

  <!-- Publish Section -->
    <div class="publish-content" id="publish-section">
        <img src="images/lap1.jpg" alt="Journal Publishing" class="right-image">
        <div class="left-content">
            <div class="section-title"><h2>JOURNAL PUBLICATION</h2></div>
            <br>
            <br>
            <p class="publish-text">Discover our step-by-step guide for publishing in a Zieers journal.</p>
            <p class="publish-text">For a comprehensive list of journals, check our Journal Catalog.</p>
            <p class="publish-text">Ready to submit to a journal? Find the journal homepage on <a href="researcher/author/journal-listing.php">ScienceDirect</a> and click "Submit your article".</p>
        </div>
    </div>

    <!-- JavaScript for Smooth Scroll and Animation -->
    <script>
        document.addEventListener("scroll", function() {
            let publishSection = document.getElementById("publish-section");
            let position = publishSection.getBoundingClientRect().top;
            let screenPosition = window.innerHeight / 1.2;
            if (position < screenPosition) {
                publishSection.classList.add("visible");
            }
        });
    </script>
    <!-- <footer>
  <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; padding: 20px;">
    <a href="about-us.php" style="color: #ffffff; text-decoration: none; font-weight: 500;">About</a>
    <a href="contact-us.php" style="color: #ffffff; text-decoration: none; font-weight: 500;">Contact</a>
    <a href="privacy_policy.php" style="color: #ffffff; text-decoration: none; font-weight: 500;">Privacy & Policy</a>
  </div>
 <p onclick="window.open('https://www.zieers.com/', '_blank');">
    &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
</p>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
<!-- </script> -->
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
</body>
</html>
