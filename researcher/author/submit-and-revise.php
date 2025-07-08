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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Submit and Revise | Zieers</title>
  <link rel="stylesheet" href="../../styles.css">
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
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 50px;
    }

    nav ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
      gap: 20px;
    }

    nav ul li {
      position: relative;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 10px 15px;
      transition: background-color 0.3s ease;
    }

    nav ul li a:hover {
      background-color: transparent;
    }

    /* Dropdown Menu */
    .dropdown-menu {
      display: none;
      position: absolute;
      background-color: #002147;
      list-style: none;
      padding: 0;
      margin: 0;
      z-index: 100;
      border-radius: 5px;
      top: 100%;
      left: 0;
    }

    .dropdown:hover .dropdown-menu {
      display: block;
    }

    .dropdown-menu li a {
      display: block;
      padding: 10px 20px;
      color: white;
      text-decoration: none;
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

    .main-content {
      background: #fff;
      padding: 40px 30px;
      margin: 30px auto;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    .container {
      width: 85%;
      max-width: 1200px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    h2 {
      color: #1e3a8a;
    }

    p, li {
      font-size: 17px;
      line-height: 1.8;
    }

    ul {
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
  <div class="logo">
    <a href="../../publish.php">
      <img src="../../images/logo.png" alt="Zieers Logo">
    </a>
  </div>
  <div class="hamburger-menu" onclick="toggleMenu()">&#9776;</div>
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
</header>

<div class="breadcrumb-container">
    <ul class="breadcrumb">
      <li><a href="../../submit-page.php">Publish with Zieers</a></li>
      <li>&gt;</li>
      <li>Submit and Revise</li>
    </ul>
  </div>
</div>

<div class="container">
  <div class="main-content">
    <h1>Submit and Revise</h1>

    <h2>How to Submit</h2>
    <p>You can submit to most Zieers journals using our secure online system. Follow the "Submit Your Paper" link on your journal’s homepage to access the system.</p>
    <p>If you’re a new user, register first. Returning authors can log in directly. Once logged in, the system will guide you through the entire submission process.</p>
    <p>After submission, you can login into your dashboard to track the manuscript status. Revisions post-peer review are also submitted through this system.</p>

    <h2>Editorial Screening and Plagiarism Check</h2>
    <p>After submission, your manuscript undergoes an initial review by our editorial board to ensure it aligns with the journal’s scope and quality expectations. If it passes this screening, it proceeds to a plagiarism check powered by AI-based tools to ensure originality and ethical integrity.</p>

    <h2>Peer Review and Revisions</h2>
    <p>Once your manuscript clears editorial and plagiarism checks, it is assigned to peer reviewers. They provide constructive feedback. Revisions can be submitted through your dashboard, and they may be re-evaluated for completeness and clarity.</p>

    <h2>Final Decision and Additional Services</h2>
    <p>After peer review, the editor makes a final decision. Accepted manuscripts move forward for publication. You may choose open access, deposit data on trusted platforms, or use our Article Transfer Service to redirect submissions with review history.</p>
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
    const nav = document.querySelector('.nav-links');
    nav.classList.toggle('active');
  }
</script>

</body>
</html>
