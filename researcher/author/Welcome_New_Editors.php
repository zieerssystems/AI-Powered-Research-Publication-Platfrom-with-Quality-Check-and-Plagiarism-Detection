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
  <title>Welcome for New Editors | Zieers</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
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

    /* Hero Section */
    .hero-section {
      background: #fff;
      text-align: center;
      padding: 3rem 1rem;
    }

    .hero-section h1 {
      font-size: 2.5rem;
      color: #002147;
      margin-bottom: 1rem;
    }

    .hero-section p {
      color: #555;
      max-width: 800px;
      margin: 0 auto;
    }

    /* Service Links */
    .service-links {
      display: flex;
      justify-content: center;
      gap: 20px;
      padding: 2rem 1rem;
      flex-wrap: wrap;
    }

    .service-links a {
      background: #007bff;
      color: #fff;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 1rem;
      transition: background 0.3s;
    }

    .service-links a:hover {
      background: #0056b3;
    }

    /* Content Section */
    .content-section {
      width: 90%;
      max-width: 1000px;
      margin: auto;
      padding: 2rem 0;
    }

    .content-section h2 {
      color: #002147;
      margin-top: 2rem;
    }

    .content-section ul {
      padding-left: 1.5rem;
      margin-top: 1rem;
    }

    .content-section ul li {
      margin-bottom: 10px;
    }

    /* Footer */
    footer {
      background: #002147;
      color: white;
      text-align: center;
      padding: 2rem 1rem;
    }

    footer a {
      color: #ccc;
      text-decoration: none;
      margin: 0 10px;
    }

    footer a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      nav ul {
        flex-direction: column;
        align-items: flex-end;
      }

      .header-container {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }

      .service-links {
        flex-direction: column;
        align-items: center;
      }
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
      <li>Welcome_New_Editors</li>
    </ul>
  </div>

<!-- Hero Section -->
<section class="hero-section">
  <h1>Welcome to Zieers!</h1>
  <p>As a journal editor, you play a vital role in the academic publishing process. At Zieers, we honor your editorial contributions and support you every step of the way.</p>
</section>

<!-- Main Content -->
<section class="content-section">
  <h2>Getting Started</h2>
  <p>Embarking on your editorial journey can be both exciting and challenging. To assist you, we've developed the Editor Hub, a comprehensive resource tailored to your needs. Here, you'll find guidance on your role, insights into publishing processes, and support tools to help you succeed.</p>

  <h2>Resources</h2>
  <ul>
    <li><strong>Editor Hub:</strong> Your go-to platform for editorial resources and tools.</li>
    <li><strong>Editor Essentials Training:</strong> A course designed to equip you with the necessary skills and knowledge.</li>
    <li><strong>Editorial Board Guidelines:</strong> Understand the roles and responsibilities within the editorial team.</li>
    <li><strong>Reviewer Management:</strong> Best practices for engaging and supporting reviewers.</li>
    <li><strong>Metrics & Analytics:</strong> Tools to track the impact and performance of your journal.</li>
  </ul>

  <h2>Stay Connected</h2>
  <p>Join our community of editors to share experiences, seek advice, and stay updated on the latest developments in academic publishing. Subscribe to our newsletter and participate in our forums to engage with peers and industry experts.</p>

  <h2>Help & Support</h2>
  <p>If you have any questions or need assistance, our support team is here to help. Visit our Help Center or contact as publishing representative for personalized support.</p>
</section>

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
