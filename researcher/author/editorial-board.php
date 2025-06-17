
<?php
if (!isset($_GET['journal_id'])) {
    die("Error: journal_id is missing in the URL.");
}

$journal_id = $_GET['journal_id'];
include(__DIR__ . "/../../include/db_connect.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$journal = getJournalById($conn, $journal_id);
if (!$journal) {
    die("No journal found with the given ID.");
}

// $journal = $result->fetch_assoc();

$result = getEditorialBoardByJournalId($conn, $journal_id);
if ($result->num_rows == 0) {
    die("No editorial board details found for this journal.");
}
$editorial_data = $result->fetch_assoc();
$editorial_board = htmlspecialchars($editorial_data['editorial_board']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($journal['journal_name']); ?> - Details</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #002147;
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

        .journal-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background-color: #1A3A6C;
            padding: 30px;
            border-bottom: 2px solid #BDC3C7;
            margin-top: 30px;
        }

        .image-container {
            width: 150px;
            height: 150px;
            overflow: hidden;
            border-radius: 10px;
            margin-right: 30px;
            background-color: #1A3A6C;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .journal-info h1 {
            font-size: 36px;
            font-weight: 600;
            color: #ffffff;
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

        .editorial-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .editorial-table th, .editorial-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .editorial-table th { background-color: rgb(80, 110, 159); color: white; font-size: 18px; }
        .position { color: rgb(65, 87, 122); font-weight: bold; }

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
  <a href="publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
        <div class="icons">
            <a href="../../publish.php">Home</a>
            <div class="user-dropdown">
                <button class="dropdown-toggle">For Users ▼</button>
                <div class="dropdown-menu">
                    <a href="../../for_author.php">For Author</a>
                    <a href="../../for_reviewer.php">For Reviewer</a>
                    <a href="../../for_editor.php">For Editor</a>
                </div>
            </div>
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

    <div class="dropdown-container">
        <div class="dropdown">
            <button>Articles & Issues ▼</button>
            <div class="dropdown-content">
                <p><a href="latest_issue.php?journal_id=<?php echo $journal_id; ?>">Latest Issue</a></p>
                <p><a href="issue.php?journal_id=<?php echo $journal_id; ?>">All Issues</a></p>
            </div>
        </div>
        <div class="dropdown">
            <button>About ▼</button>
            <div class="dropdown-content">
                <p><a href="aims_scope.php?journal_id=<?php echo $journal_id; ?>">Aims and Scope</a></p>
                <p><a href="Journal-Insights.php?journal_id=<?php echo $journal_id; ?>">Journal Insights</a></p>
            </div>
        </div>
        <div class="dropdown">
            <button>Publish ▼</button>
            <div class="dropdown-content">
                <?php if ($journal['submission_status'] == 'Accepting Submissions') { ?>
                    <p><a target="blank" href="submit-article.php?journal_id=<?php echo $journal_id; ?>">Submit Your Article</a></p>
                <?php } else { ?>
                    <p>Submissions are currently closed for this journal.</p>
                <?php } ?>
                <p><a href="author-guidelines.php?journal_id=<?php echo $journal_id; ?>">Guide for Authors</a></p>
            </div>
        </div>
    </div>
    <div class="container">
        <h2>Editorial Board</h2>
        <table class="editorial-table">
            <tr>
                <th>Name</th>
                <th>Position</th>
            </tr>
            <?php
            $rows = explode("\n", $editorial_board);
            foreach ($rows as $row) {
                $parts = explode("-", $row, 2);
                $name = trim($parts[0]);
                $position = isset($parts[1]) ? trim($parts[1]) : '';
                echo "<tr><td>$name</td><td class='position'>$position</td></tr>";
            }
            ?>
        </table>
    </div>
    <footer class="site-footer">
        <div class="footer-container">
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
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="../../about-us.php">About Us</a></li>
                    <li><a href="../../contact-us.php">Contact Us</a></li>
                </ul>
            </div>
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
                © <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
            </p>
        </div>
    </footer>
    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</body>
</html>
<?php
$conn->close();
?>