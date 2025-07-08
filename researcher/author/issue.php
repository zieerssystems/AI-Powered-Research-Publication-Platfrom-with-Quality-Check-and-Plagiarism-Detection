
<?php
include(__DIR__ . "/../../include/db_connect.php");

// Step 1: Check if journal_id is set in the query string
if (!isset($_GET['journal_id'])) {
    die("Error: journal_id is missing in the URL.");
}

// Get the journal_id from the URL query parameter
$journal_id = $_GET['journal_id'];

// Step 3: Fetch journal details using the journal_id
$journal = getJournalById($conn, $journal_id);
if (!$journal) {
    die("No journal found with the given ID.");
}

// Fetch issues grouped by year and volume
$issuesByYearVolume = getIssuesByJournalId($conn, $journal_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($journal['journal_name']); ?> - Details</title>
    <link rel="stylesheet" href="my_publication_site/styles.css"> <!-- Correct path for the CSS file -->
    <style>
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9; /* Light Gray Background */
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #002147; /* Darker Gray */
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ECF0F1;
        }

        .icons a {
            color: #ECF0F1;
            text-decoration: none;
            margin-left: 20px;
            font-size: 16px;
        }

        /* Journal Header Section */
        .journal-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background-color: #1A3A6C; /* Dark Blue Background */
            padding: 30px;
            border-bottom: 2px solid #BDC3C7;
            margin-top: 30px;
        }

        .image-container {
            width: 150px;
            height: 150px;
            overflow: hidden;
            border-radius: 10px;
            margin-right: 30px; /* Space between image and text */
            background-color: #1A3A6C; /* Dark Blue Background for Image */
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .journal-info h1 {
            font-size: 36px;
            font-weight: 600;
            color: #ffffff; /* White Text for Journal Name */
            margin: 0;
        }


/* Dropdown Menu Styling */
.dropdown-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 10px;
}

.dropdown {
    position: relative;
    display: inline-block;
    margin: 0 15px;
}

.dropdown button {
    background-color: transparent;
    color: #000000; /* Black text */
    padding: 12px 25px;
    font-size: 16px;
    border: none;
    cursor: pointer;
}

.dropdown button:hover {
    background-color: #f4f4f4; /* Light grey hover effect */
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #fff;
    min-width: 180px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
    border-radius: 5px;
}

.dropdown-content p {
    margin: 0;
    padding: 12px 16px;
    color: #000000; /* Ensure black text */
    text-decoration: none; /* No underline */
    display: block;
}

.dropdown-content p:hover {
    background-color: #f4f4f4; /* Light grey on hover */
}

.dropdown:hover .dropdown-content {
    display: block;
}

/* Remove underline from links inside the dropdown */
.dropdown-content p a {
    text-decoration: none; /* No underlines */
    color: #000000; /* Black text color */
}

.dropdown-content p:hover {
    background-color: #f4f4f4; /* Light grey on hover */
}

.dropdown:hover .dropdown-content {
    display: block;
}
.user-dropdown {
    position: relative;
    display: inline-block;
    font-family: 'Poppins', sans-serif;
}

.dropdown-toggle {
    background-color: #002147;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s ease;
}

.dropdown-menu {
    display: none;
    position: absolute;
    background-color:  fff;
    min-width: 160px;
    right: 0;
    z-index: 1;
    overflow: hidden;
}

.dropdown-menu a {
    background-color: #002147;
    color: fff;
    padding: 12px 16px;
    display: block;
    text-decoration: none;
    font-size: 14px;
}
.dropdown-menu a:hover {
    background-color: #004080;
}

/* Show menu on hover */
.user-dropdown:hover .dropdown-menu {
    display: block;
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

.search-box {
    display: flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 30px;
    padding: 5px;
}

.search-box input {
    border: none;
    outline: none;
    padding: 15px;
}

.search-box button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

        /* Journal Information Table */
        .journal-info-table {
            width: 100%;
            max-width: 900px;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .journal-info-table th,
        .journal-info-table td {
            padding: 15px;
            border: 1px solid #BDC3C7;
            text-align: left;
        }

        .journal-info-table th {
            background-color: #f4f4f4;
            font-weight: 600;
            color: #34495E;
        }

        /* Show More Button */
        .show-more-btn {
            color: #3498db;
            cursor: pointer;
            text-decoration: underline;
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .show-more-btn:hover {
            text-decoration: none;
        }

        #full-info {
            display: none;
            padding: 20px;
            background-color: #f9f9f9;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }.journal-metrix {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .journal-description {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .insight-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .insight-btn:hover {
            background-color: #2980b9;
        }

        .insight-container {
            max-width: 900px;
            margin: 20px auto;
            background-color: #d0e7ff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: none;
            text-align: center;
        }

        .insight-container table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .insight-container th, .insight-container td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .insight-container th {
            background-color: #3498db;
            color: white;
        }
        @keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-20px); }
}

.slide-down {
    animation: slideDown 0.5s ease-out;
}

.slide-up {
    animation: slideUp 0.5s ease-in;
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
     <div class="logo">
  <a href="../../publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
    <div class="icons">
        <a href="../../publish.php">Home</a>
<div class="user-dropdown">
    <button class="dropdown-toggle">For Users ▼</a></button>
    <div class="dropdown-menu">
        <a href="../../for_author.php">For Author</a>
        <a href="../../for_reviewer.php">For Reviewer</a>
        <a href="../../for_editor.php">For Editor</a>
    </div>
</div>
        <!-- User Dropdown Logic -->
     <?php if (isset($_SESSION['user_id'])): ?>
<div class="user-dropdown">
    <button class="dropdown-toggle"><?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</button>
    <div class="dropdown-menu">
        <a href="../../profile.php">View Profile</a>
        <a href="../../logout.php">Logout</a>
    </div>
</div>
<?php endif; ?>

        <a href="journal-listing.php?journal_id=<?php echo $journal_id; ?>">Journals</a>
        <a href="help.php">Help</a>
    </div>
</header>
<div style="margin: 20px 0 0 20px;">
    <button onclick="history.back()" style="
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
    ">
        ← Back
    </button>
</div>

    <!-- Journal Header Section -->
    <div class="journal-header">
        <!-- Display Journal Image -->
        <div class="image-container">
            <?php 
                // Step 6: Get the image path and display the image
                $imagePath = htmlspecialchars($journal['journal_image']);  // Sanitize the image path

                // Correct the path for the image
                $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . "/admin/" . $imagePath; // Full path on server

                // Check if the file exists in the directory and display it
                if (file_exists($fullImagePath)) {
                    // Display image with correct path
                    echo '<img src="/admin/' . $imagePath . '" alt="Journal Image">';
                } else {
                    // Fallback message if the image does not exist
                    echo '<span>No Image Available</span>';
                }
            ?>
        </div>

        <!-- Journal Info -->
        <div class="journal-info">
            <h1><?php echo htmlspecialchars($journal['journal_name']); ?></h1>
        </div>
    </div>

    <!-- Dropdown Menu for Articles & Issues, About, Publish -->
    <div class="dropdown-container">
        <div class="dropdown">
            <button>Articles & Issues ▼</button>
            <div class="dropdown-content">
            <p><a href="latest_issue.php?journal_id=<?php echo $journal_id; ?>">Latest Issue</a></p>
            </div>
        </div>

        <div class="dropdown">
            <button>About ▼</button>
            <div class="dropdown-content">
                <p><a href="aims_scope.php?journal_id=<?php echo $journal_id; ?>">Aims and Scope</a></p>
                <p><a href="editorial-board.php?journal_id=<?php echo $journal_id; ?>">Editorial Board</a></p>
                <p><a href="Journal-Insights.php?journal_id=<?php echo $journal_id; ?>">Journal Insights</a></p>
            </div>
        </div>

        <div class="dropdown">
            <button>Publish ▼</button>
            <div class="dropdown-content">
            <?php if ($journal['submission_status'] == 'Accepting Submissions') { ?>
            <p><a href="submit-article.php?journal_id=<?php echo $journal_id; ?>">Submit Your Article</a></p>
        <?php } else { ?>
            <p>Submissions are currently closed for this journal.</p>
        <?php } ?>
        <p><a href="author-guidelines.php?journal_id=<?php echo $journal_id; ?>">Guide for Authors</a></p>
        </div>
        </div>
      
    </div>
   <div class="volume-dropdowns" style="max-width: 800px; margin: 30px auto;">
        <h2 style="text-align: center;">All Issues</h2>
        <?php foreach ($issuesByYearVolume as $year => $volumes): ?>
            <div class="year-group">
                <h3 style="color: #1A3A6C;"><?php echo $year; ?></h3>
                <?php foreach ($volumes as $volume => $issues): ?>
                    <div class="dropdown-wrapper-vol">
                        <button class="dropdown-toggle-main">Volume <?php echo $volume; ?> ▼</button>
                        <div class="dropdown-content-vol">
                            <?php foreach ($issues as $issue): ?>
                                <p>
                                    <a href="latest_issue.php?journal_id=<?= $journal_id ?>&volume=<?= $volume ?>&issue=<?= $issue ?>">
                                     Issue <?= $issue ?>
                                    </a>
                                </p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
<style>
    .dropdown-wrapper-vol {
        margin-bottom: 10px;
    }
    .dropdown-toggle-main {
        background-color:rgb(35, 94, 188);
        color: #fff;
        padding: 10px 15px;
        border: none;
        width: 100%;
        text-align: left;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
    }

    .dropdown-content-vol {
        display: none;
        background-color: #f0f0f0;
        padding: 10px 20px;
        border-radius: 0 0 5px 5px;
    }

    .dropdown-content-vol p {
        margin: 5px 0;
        color: #333;
    }
</style>
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
    document.querySelectorAll('.dropdown-toggle-main').forEach(button => {
        button.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const isVisible = content.style.display === 'block';
            content.style.display = isVisible ? 'none' : 'block';
            this.textContent = isVisible ?
                this.textContent.replace('▲', '▼') :
                this.textContent.replace('▼', '▲');
        });
    });
</script>


</body>
</html>