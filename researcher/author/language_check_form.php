<?php  
session_start();
include(__DIR__ . "/../../include/db_connect.php");
$user = getUserById($_SESSION['user_id']);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["manuscript"])) {
  $uploadDir = "uploads/";
  $fileName = basename($_FILES["manuscript"]["name"]);
  $targetFile = $uploadDir . $fileName;
  $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

  // Only allow .docx files
  if ($fileExtension !== "docx") {
    $_SESSION['status'] = "Only .docx files are allowed. Please upload a valid .docx manuscript.";
  } else {
    if (move_uploaded_file($_FILES["manuscript"]["tmp_name"], $targetFile)) {
      $_SESSION['uploaded_file'] = $targetFile;
      // $_SESSION['status'] = "Manuscript uploaded successfully.";
      header("Location: gemini-edit.php?file=" . urlencode($targetFile)); 
      exit;
    } else {
      $_SESSION['status'] = "There was an error uploading your manuscript.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Language Editing | Zieers</title>
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

    .container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 40px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }
    h1 {
      text-align: center;
      color: #002147;
    }
    p {
      text-align: center;
      color: #555;
      font-size: 16px;
    }
    form {
      margin-top: 30px;
    }
    input[type="file"] {
      display: block;
      margin: 20px auto;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    input[type="submit"] {
      display: block;
      margin: 20px auto;
      background-color: #002147;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    input[type="submit"]:hover {
      background-color: #0056a1;
    }
    .cta {
      text-align: center;
      margin-top: 40px;
    }
    .cta a {
      color: #ffcc00;
      text-decoration: none;
      font-weight: bold;
    }
    .cta a:hover {
      text-decoration: underline;
    }
    .status {
      text-align: center;
      margin-top: 20px;
      color: red;
      font-weight: bold;
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
  <a href="../../publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
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

<div class="breadcrumb-container">
    <ul class="breadcrumb">
      <li><a href="language.php">Language-Editing</a></li>
      <li>&gt;</li>
      <li>language_check</li>
    </ul>
</div>

  <div class="container">
    <h1>Submit Manuscript for Language Editing</h1>
    <p>Upload your research paper below for professional language editing.<br>
    <strong>Only .docx files are allowed.</strong><br>
    Once editing is complete, you'll be asked  download it  the improved manuscript.</p>
    
    <!-- Upload Form -->
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="manuscript" accept=".docx" required />
      <input type="submit" value="Upload and Edit" />
    </form>

    <!-- Show status message -->
    <?php
      if (isset($_SESSION['status'])) {
        echo "<div class='status'>" . $_SESSION['status'] . "</div>";
        unset($_SESSION['status']);
      }
    ?>
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
</body>
</html>
