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
  <title>Peer Review Process | Zieers</title>
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
    flex: 0 0 auto; 
    justify-content: space-between;/* Keeps Zieers on the left */
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

    .hero {
      background-color:rgb(5, 51, 103);
      color: white;
      text-align: center;
      padding: 50px 20px;
    }

    h1 {
      font-size: 2.5em;
      margin-bottom: 10px;
    }

    h2 {
      font-size: 1.8em;
      margin-bottom: 10px;
      color:rgb(41, 41, 110);
    }

    section {
      background-color: #fff;
      margin: 30px auto;
      padding: 30px;
      width: 90%;
      max-width: 900px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      border-radius: 8px;
    }

    ul, ol {
      margin-left: 20px;
    }

    a {
      color: #0077b6;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
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
      <li>Peer Review</li>
    </ul>
  </div>
<main>
  <section class="hero">
    <h1>Understanding Peer Review at Zieers</h1>
    <p>At Zieers, peer review ensures that every publication upholds the highest standards of academic integrity and scientific value.</p>
  </section>

  <section>
    <h2>What is Peer Review?</h2>
    <p>Peer review is the process through which submitted research manuscripts are evaluated by experts in the same field. This rigorous evaluation ensures that the paper is original, technically sound, and valuable to the research community. It plays a vital role in safeguarding the credibility and quality of scientific literature.</p>
  </section>

  <section>
    <h2>Zieers Peer Review Workflow</h2>
    <p>
      Once a manuscript is submitted to Zieers, it undergoes an initial editorial screening by our editorial board. This includes checking for scope relevance, basic structure, and an automated plagiarism detection check. If it passes this phase, the paper is assigned to qualified peer reviewers for detailed review.
    </p>
    <p>
      Reviewers evaluate the methodology, originality, clarity, and impact of the research. They submit feedback and recommendations, which may include acceptance, revision, or rejection. If revisions are requested, the feedback is sent to the author via their dashboard, where they can revise and resubmit the manuscript for further consideration.
    </p>
  </section>

  <section>
    <h2>Final Decision & Publishing</h2>
    <p>
      The final decision on the manuscript is made by the editor after considering reviewer feedback and any revisions submitted by the author. Once accepted, the paper moves into production and is published under open access licensing options. Authors may also benefit from integrated research data submission and the Zieers Article Transfer Service for better publishing matches.
    </p>
  </section>

  <section>
    <h2>Track Your Manuscript</h2>
    <p>
      Authors are provided with a secure dashboard after OTP verification through email. Within this dashboard, they can:
    </p>
    <ul>
      <li>Submit new manuscripts</li>
      <li>Track submission status in real-time</li>
      <li>Access reviewer feedback and revision requests</li>
      <li>Resubmit revised manuscripts</li>
      <li>View the final editorial decision</li>
      <li>Check accepted, rejected, and published paper history</li>
    </ul>
    <p>
      All updates and status changes are notified through email and can also be monitored directly through the author dashboard.
    </p>
  </section>
</main>

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
