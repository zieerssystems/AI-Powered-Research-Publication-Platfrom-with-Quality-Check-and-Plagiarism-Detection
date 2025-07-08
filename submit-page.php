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
    <title>Submit Your Paper</title>
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
        .banner {
    background: url('images/ed.png') no-repeat;
    background-position: 75% 30%; /* Shift image to the right more */
    background-size: cover;
    height: 400px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Align content to left */
    padding-left: 60px;
    color: white;
    text-align: left;
    overflow: hidden;
}

.banner::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.4); /* dark overlay for better readability */
    z-index: 0;
}

.banner-content {
    position: relative;
    z-index: 1;
    max-width: 700px;
    background: rgba(0, 0, 0, 0.3); /* semi-transparent background */
    padding: 20px 30px;
    border-radius: 12px;
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    animation: fadeInLeft 1.2s ease-out;
}

.banner-content h1 {
    font-size: 40px;
    margin-bottom: 12px;
    text-shadow: 1px 1px 3px #000;
}

.banner-content p {
    font-size: 18px;
    line-height: 1.5;
    text-shadow: 1px 1px 2px #000;
}

/* Fade-in from left animation */
@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
 .publishing-guide {
            display: flex;
            flex-wrap: wrap;
            padding: 60px 10%;
            background: white;
        }
        .guide-left, .guide-right {
            flex: 1;
            min-width: 300px;
            padding: 20px;
        }
        .guide-left h1 {
            font-size: 28px;
            color: #002147;
        }
        .steps-nav {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            padding: 20px 10%;
             background-color:rgb(22, 50, 83);
        }
        .step {
            font-weight: 500;
            padding: 10px 15px;
            background: #f0f0f0;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .step:hover {
            background: #dbeafe;
        }
        .step-content {
            background: white;
            margin: 20px 2%;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .step-content h2 {
            color: #002147;
        }
        .info-box {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 20px;
            margin-top: 20px;
            border-radius: 6px;
        }
        .btn {
            display: inline-block;
             background-color: #002147;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            margin-top: 10px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #002147;
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

        @media (max-width: 768px) {
            header, .publishing-guide, .steps-nav, .step-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
         <div class="logo">
    <a href="publish.php">
      <img src="images/logo.png" alt="Zieers Logo">
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
        <li><a href="for_reviewer.php">For Reviewer</a></li>
        <li><a href="for_editor.php">For Editor</a></li>
    </ul>
</li>
 <li class="dropdown">
    <a href="#"><?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
    <ul class="dropdown-menu">
        <li><a href="profile.php">View Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</li>
            </ul>
            <!-- <a href="publish-with-us.php" class="publish-btn">Publish with Us</a> -->
        </nav>
    </div>
</header>



<div class="banner">
    <div class="banner-overlay"></div>
    <div class="banner-content">
        <h1>Publish with zieers</h1>
        <p>
        Learn about the publication process and how to submit your manuscript.
         This tutorial will help you find the right journal and maximize the chance to be published.</p>
    </div>
</div>


<!-- Right side: Guide Content -->
<div class="publishing-guide">
    <div class="guide-left">
        <h1>Your step-by-step guide to publishing with Zieers</h1>
    </div>
    <div class="guide-right">
        <p>
            Every year, we accept and publish more than 470,000 journal articles, so you are in safe hands.
            Publishing in an zieers journal starts with finding the right journal for your paper.
            We have tools, resources, and services to help you at each stage of the publication journey to
            enable you to research, write, publish, promote, and track your article.
            Let us help you make the most out of your next publication!
        </p>
    </div>
</div>



    </div>
    <!-- Steps Navigation -->
    <div class="steps-nav">
        <div class="step" onclick="scrollToStep('step1')">1. Find a journal</div>
        <div class="step" onclick="scrollToStep('step2')">2. Prepare your paper</div>
        <div class="step" onclick="scrollToStep('step3')">3. Submit and revise</div>
        <div class="step" onclick="scrollToStep('step4')">4. Track your paper</div>
        <div class="step" onclick="scrollToStep('step5')">5. Share and promote</div>
    </div>

    <!-- Steps Content -->
    <div class="container">
        <div id="step1" class="step-content">
            <h2>step 1.Find a journal</h2>
            <br>
            <br>
            <p>Find out the journals that could be best suited for publishing your research. For a comprehensive list of zieers journals check our<a href="journal.php"> Journal Catalog</a>
            You can also match your manuscript using the JournalFinder tool, then learn more about each journal.
             You can <a href="researcher/author/login-to-editorial-manager.php">You can find information about how to log in to each journal’s editorial system here.</a>.</p>
             <br>


             <br>
            <div class="info-box">
                <h1>JournalFinder</h1>
                <br>
                
                <br>
                <p>Search the world's leading source of academic journals for a 
                    list of recommended journals that best match your research paper.</p>
                <a href="researcher/author/journalfinder.php" class="btn">Find a matching journal</a>
            </div>
        </div>

        <div id="step2" class="step-content">
            <h2>step 2.Prepare your paper for submission</h2>
            <br>

            <br>
            <p>Download our <a target="blank" href="uploads/guide.pdf"> Get Published Quick Guide</a>, which 
            outlines the essential steps in preparing a paper. (This is also available in 
Chinese
). </p>
<br>


<br>
<p>It is very important that 
you stick to the specific "guide for authors" of the journal to which you are
 submitting. This can be found on the journal's home page.
 You can find information about the publishing process in the 
understanding the publishing process opens</a>
 guide. It covers topics such as authors' 
 rights, ethics and plagiarism, and journal and article metrics.</p>
 
 <br>

 <br>
 <p>
 If you have research data to share, make sure you read the guide
  for authors to find out which options the journal offers to share research data with your article.
 </p>
 <a href="researcher/author/prepare-your-paper-for-submission.php" class="btn">Read more</a>
        </div>

        <div id="step3" class="step-content">
            <h2>step 3.Submit and revise</h2>
            <br>

            <br>
            <p>You can submit to most zieers journals using our online systems. 
                 The system you use will depend on the journal to which you submit.
                  You can access the relevant submission system via the "submit your paper" 
                  link on the zieers.com journal homepage of your chosen journal.
<br>

<br>
                  </p><p>Alternatively, if you have been invited to submit to a journal, 
follow the instructions provided to you.</p>
<br>

<br>
<p>
Once submitted, your paper will be considered by the 
editor and if it passes initial screening, 
it will be sent for peer review by experts in your field.
 If deemed unsuitable for publication in your chosen journal, the editor may suggest you transfer your 
submission to a more suitable journal, via an article transfer service.<a href="researcher/author/submit-and-revise.php" class="btn">Read more</a>
        </div>

        <div id="step4" class="step-content">
    <h2>step 4.Track Your Paper</h2>
    <br>

    <p>
        Authors receive a secure dashboard upon registration and email OTP verification. This personalized space allows you to manage every step of your publishing journey with Zieers. Once logged in, you can view the status of all your manuscripts—submitted, under review, revised, accepted, or rejected—in real time.
    </p>
    <br>

    <p>
        After submission, your manuscript appears in your dashboard with a unique reference number. You’ll be notified via email whenever there is an update, including reviewer feedback, revision requests, or editorial decisions. All resubmissions are handled through the same platform, ensuring seamless communication and control.
    </p>
    <br>

    <p>
        Once your paper is accepted, its publishing status—such as in production, in press, or published—will be visible in your dashboard. Even if you miss a notification, you can always log in to track updates, access final decisions, and download published versions of your paper.
    </p>
    <br>
</div>


        <div id="step5" class="step-content">
            <h2>step 5.Share and promote</h2>
            <br>

            <br>
            <p>Now that your article is published, 
                you can promote it to achieve a bigger 
                impact for your research. Sharing research,
                
                accomplishments and ambitions with a wider 
                audience makes you more visible in your field.
                 This helps you get cited more, enabling you to 
                 cultivate a stronger reputation, promote your research and move forward in your career.
</p>
<br>

<br>
<p>After publication, celebrate and get noticed.</p>
            <!-- <a href="#" class="btn">Read more</a> -->
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
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
    <script>
    function toggleMenu() {
        document.querySelector(".nav-links").classList.toggle("active");
    }
</script>
    <script>
        function scrollToStep(stepId) {
            document.getElementById(stepId).scrollIntoView({ behavior: "smooth", block: "start" });
        }
    </script>

</body>
</html>
