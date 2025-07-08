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
  <title>Collaborating with Reviewers | Zieers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
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
  .logo {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 50px;
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
    .hero-section {
      background-color: #ffffff;
      padding: 3rem 1rem;
      text-align: center;
    }
    .hero-section h1 {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }
    .hero-section p {
      font-size: 1.2rem;
      color: #6c757d;
    }
    .content-section {
      padding: 2rem 1rem;
    }
    .content-section h2 {
      margin-top: 2rem;
      margin-bottom: 1rem;
    }
    .content-section ul {
      list-style-type: disc;
      margin-left: 1.5rem;
    }footer {
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
      <li><a href="../../for_editor.php">Editor</a></li>
      <li>&gt;</li>
      <li>Collabrate</li>
    </ul>
  </div>
  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <h1>Collaborating with Reviewers</h1>
      <p>Learn how to identify, support, and recognize reviewers to ensure a smooth and efficient peer review process.</p>
    </div>
  </section>

  <!-- Content Section -->
  <section class="content-section">
    <div class="container">
      <h2>Peer Review Process</h2>
      <p>At Zieers, the peer review process is crucial in ensuring quality research. Here's how it works:</p>
      <ul>
        <li><strong>Editor Assigns Reviewers:</strong> After receiving a manuscript, the editor selects appropriate reviewers.</li>
        <li><strong>Reviewers Provide Feedback:</strong> Reviewers assess the paper and provide feedback based on the quality of the research.</li>
        <li><strong>Final Decision:</strong> Based on reviewer feedback, the editor makes the final decision on whether the paper will be published or revised.</li>
      </ul>

      <h2>Reviewer Responsibilities</h2>
      <p>As a reviewer, your job is to:</p>
      <ul>
        <li>Critically evaluate the paper's originality, quality, and relevance.</li>
        <li>Provide constructive feedback to help authors improve their work.</li>
        <li>Ensure the review is timely and thorough to meet publication schedules.</li>
      </ul>

      <h2>Feedback Loop</h2>
      <p>After receiving feedback from the reviewer, the editor will:</p>
      <ul>
        <li>Review the comments and suggestions provided by the reviewer.</li>
        <li>Share the feedback with the author for revision.</li>
        <li>Update the manuscript status based on the changes made.</li>
      </ul>

      <h2>Editor’s Final Decision</h2>
      <p>The editor has the final say in the decision-making process:</p>
      <ul>
        <li>They review the revised paper and final comments from reviewers.</li>
        <li>Based on the quality and compliance with guidelines, they accept or reject the paper.</li>
      </ul>

    </div>
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

</body>
</html>
