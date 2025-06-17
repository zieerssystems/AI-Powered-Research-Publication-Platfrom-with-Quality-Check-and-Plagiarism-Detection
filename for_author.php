<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];  
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>For Authors - Zieers</title>
    
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

        .banner {
            position: relative;
            width: 100%;
            height: 75vh;
            background: url('images/new6.jpg') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }
        .banner-content {
            background: rgba(0, 33, 71, 0.6);
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
            font-size: 32px;
            font-weight: bold;
        }
        .author-hub-container {
            display: flex;
            align-items: flex-start;
            background: white;
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }
        .author-hub-content {
            display: flex;
            width: 100%;
        }
        .left {
        width: 50%;
        text-align: center; /* Center the content horizontally */
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

        .left h2 {
            font-size: 34px;
            font-weight: bold;
            color: #002147;
            margin-bottom: 20px;
        }
        .right {
            width: 70%;
        }
        .right p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }
        @media (max-width: 768px) {
            .author-hub-content {
                flex-direction: column;
                text-align: left;
            }
            .left, .right {
                width: 100%;
            }
        }
        .journal-authors-container {
    margin: 40px 0 40px 30px; /* only left space */
    font-family: 'Segoe UI', sans-serif;
}

.journal-authors-container h2 {
    color: #1e3a8a;
    font-size: 24px;
    margin-top: 20px;
}

.journal-authors-container p {
    font-size: 16px;
    line-height: 1.6;
    color: #333;
}

.submit-btn,
.button-link {
    display: inline-block;
    margin: 15px 10px 20px 0;
    padding: 12px 20px;
    background: #1e3a8a;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
}

.submit-btn:hover,
.button-link:hover {
    background: #334fc0;
}

.journal-authors-container {
    margin: 40px 0 40px 30px;
    font-family: 'Segoe UI', sans-serif;
}

.journal-authors-container h2 {
    color: #1e3a8a;
    font-size: 24px;
    margin-top: 20px;
}

.journal-authors-container p {
    font-size: 16px;
    line-height: 1.6;
    color: #333;
}

.submit-btn,
.button-link {
    display: inline-block;
    margin: 15px 10px 20px 0;
    padding: 12px 20px;
    background-color: #ffcc00;
    color: #002147;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
}

.submit-btn:hover,
.button-link:hover {
    background: #334fc0;
}

.author-services-container {
    display: flex;
    flex-wrap: wrap;
    margin: 20px 30px;
    gap: 20px;
    align-items: center; /* Center vertically */
}

.author-services-container .text-content {
    flex: 1 1 60%;
}

.author-services-container .image-content {
        flex: 1 1 35%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
.author-services-container img {
    width: 100%;         /* Increase width to full container */
    max-width: 400px;    /* You can tweak this value */
    height: 180px;       /* Set a specific height to reduce vertical space */
    object-fit: cover;   /* Ensures it fills area proportionally */
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

html {
    scroll-behavior: smooth;
}

.nav-links-inline {
    display: flex;
    justify-content: center;
    gap: 30px;
    padding: 15px;
    background: #f0f4ff;
    font-family: 'Segoe UI', sans-serif;
    border-radius: 8px;
    margin: 20px auto;
    flex-wrap: wrap;
}

.nav-links-inline a {
    text-decoration: none;
    color: #1e3a8a;
    font-weight: bold;
    padding: 10px 15px;
    transition: background 0.3s, color 0.3s;
    border-radius: 6px;
}

.nav-links-inline a:hover {
    background: #1e3a8a;
    color: white;
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
    <div class="header-container">
          <div class="logo">
  <a href="publish.php">
    <img src="images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
        <div class="hamburger-menu" onclick="toggleMenu()">&#9776;</div>
        <nav>
            <ul class="nav-links">
                <li><a href="publish.php">Home</a></li>
                <li><a href="researcher/author/language.php">LanguageEditing</a></li>
                <li class="dropdown">
    <a href="#">For Users ▼</a>
    <ul class="dropdown-menu">
        <li><a href="for_reviewer.php">For Reviewer</a></li>
        <li><a href="for_editor.php">For Editor</a></li>
    </ul>
</li>
<!-- <li><a href="journal.php">Journal Catalog</a></li> -->
                <li><a href="researcher/author/author_dash_login.php">Author Dashboard</a></li>
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

    <div class="banner">
        <div class="banner-content">
            <h2>Instructions for Authors</h2>
        </div>
    </div>
    <div class="author-hub-container">
    <div class="author-hub-content">
        <div class="left">
            <h2>Welcome to the Author Hub</h2>
        </div>
        <div class="right">
            <p>With a commitment to supporting researchers
                 and scholars, our Author Hub is your gateway to a
                  collection of carefully curated resources designed
                   to assist you at every stage of your publication journey.
                    Whether you're preparing your first manuscript, navigating
                     the peer-review process, or looking to maximize the impact of your research, 
                     we provide the guidance and tools you need to succeed. Our goal is to ensure a seamless and transparent publishing experience, helping you share your work with the global academic community.</p>
        </div>
    </div>
</div>
<!-- Navigation Bar -->
<div class="nav-links-inline">
    <a href="#journal-authors">For Journal Authors</a>
    <a href="#tools-resources">Tools & Resources</a>
    <a href="#author-services">Author Services</a>
    <a href="#journal-issue">Journal Issue</a>
</div>

<!-- Content Sections -->
<div id="journal-authors" class="journal-authors-container">
    <h2>For Journal Authors</h2>
    <p>
        Welcome to Zieers, a new and innovative platform committed to supporting researchers and scholars in publishing high-quality research.
    </p>
    <p>
        Publishing with Zieers starts with finding the right  
        <a href="researcher/author/journalfinder.php">journal for your paper</a>. We offer tools, resources, and services to guide you through every stage—preparing your paper, submission, revision, tracking, and sharing your research.
    </p>
    <p>We are here to ensure a smooth and transparent publishing experience for you.</p>
    <br>
    <a href="submit-page.php" class="submit-btn">Submit Your Paper</a>
</div>

<div id="tools-resources" class="journal-authors-container">
    <h2>Tools & Resources</h2>
    <p>Do you want to plan, organize, publish, and promote your work without losing focus? With Zieers’ author resources, you can.</p>
    <br>
    <p>Get effective, actionable support throughout the research cycle, from concept through publication to promotion.</p>
    <a href="guide.pdf" class="button-link" target="_blank">View Our Guide</a>
</div>

<div id="author-services" class="journal-authors-container">
    <h2>Author Services</h2>
    <div class="author-services-container">
        <div class="text-content">
            <p>
                With Zieers Author Services, researchers are supported throughout the publication process, with a wide range of products and services that help them improve their articles before submission. 
            <a href="researcher/author/language.php" class="submit-btn">Language Editing</a>
       
            </p>
             </div>
        <div class="image-content">
            <img src="images/re1.jpg" alt="Author Services Image">
        </div>
    </div>
</div>

<div id="journal-issue" class="journal-authors-container">
    <h2>Journal Issue</h2>
    <p>
        If you are an Author wishing to obtain a printed copy of the journal issue, or you require a printed copy for research, review, or to add to your library, the process is easy.
    </p>
    <p>
        You will now be able to order issues by visiting the <a href="researcher/author/journal-listing.php">dedicated journal issue link</a>.
    </p>
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

</body>
</html>
