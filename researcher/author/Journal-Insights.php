
<?php
// Step 1: Check if journal_id is set in the query string
if (!isset($_GET['journal_id'])) {
    die("Error: journal_id is missing in the URL.");
}

$journal_id = $_GET['journal_id'];

// Step 2: Database connection
include(__DIR__ . "/../../include/db_connect.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 3: Fetch journal details
$journal = getJournalById($conn, $journal_id);
if (!$journal) {
    die("No journal found with the given ID.");
}

// Step 4: Fetch editorial board details
$editorial_data = getEditorialBoard($conn, $journal_id);
$editorial_board = htmlspecialchars($editorial_data['editorial_board']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($journal['journal_name']); ?> - Details</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Correct path for the CSS file -->
    <style>
         /* Basic page styling */
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
 /* Journal Insights Section */
 .journal-insights-container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f1f6fc; /* Light blue background */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .journal-insights-container h1 {
            font-size: 28px;
            color: #1A3A6C;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Scrollable Table */
        .insights-table-container {
            overflow-y: auto;
            max-height: 400px; /* Scroll if content exceeds height */
            padding: 10px;
            background: #ffffff;
            border-radius: 8px;
        }

        .insights-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        .insights-table th, .insights-table td {
            padding: 12px 15px;
            text-align: left;
        }

        .insights-table th {
            background-color:rgb(35, 89, 174);
            color: white;
            font-size: 16px;
            text-transform: uppercase;
        }

        .insights-table tr:nth-child(even) {
            background-color: #f4f4f4; /* Light grey rows */
        }

        .insights-table tr:hover {
            background-color: #e8f0ff; /* Light blue hover effect */
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
                <p><a href="issue.php?journal_id=<?php echo $journal_id; ?>">All Issues</a></p>
            </div>
        </div>
        <div class="dropdown">
            <button>About ▼</button>
            <div class="dropdown-content">
                <p><a href="aims_scope.php?journal_id=<?php echo $journal_id; ?>">Aims and Scope</a></p>
                <p><a href="editorial-board.php?journal_id=<?php echo $journal_id; ?>">Editorial Board</a></p>
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

    <!-- Journal Insights Section -->
    <div class="journal-insights-container">
        <h1>Journal Insights - <?php echo htmlspecialchars($journal['journal_name']); ?></h1>
        <div class="insights-table-container">
            <table class="insights-table">
                <tr>
                    <th>Field</th>
                    <th>Details</th>
                </tr>
                <tr><td>ID</td><td><?php echo htmlspecialchars($journal['id']); ?></td></tr>
                <tr><td>Journal Name</td><td><?php echo htmlspecialchars($journal['journal_name']); ?></td></tr>
                <tr><td>Primary Subject</td><td><?php echo htmlspecialchars($journal['primary_subject']); ?></td></tr>
                <tr><td>Secondary Subject</td><td><?php echo htmlspecialchars($journal['secondary_subject']); ?></td></tr>
                <tr><td>Description</td><td><?php echo htmlspecialchars($journal['description']); ?></td></tr>
                <tr><td>Publisher</td><td><?php echo htmlspecialchars($journal['publisher']); ?></td></tr>
                <tr><td>ISSN</td><td><?php echo htmlspecialchars($journal['issn']); ?></td></tr>
                <tr><td>Access Type</td><td><?php echo htmlspecialchars($journal['access_type']); ?></td></tr>
                <tr><td>Submission Status</td><td><?php echo htmlspecialchars($journal['submission_status']); ?></td></tr>
                <tr><td>Created At</td><td><?php echo htmlspecialchars($journal['created_at']); ?></td></tr>
                <tr><td>Journal Abbreviation</td><td><?php echo htmlspecialchars($journal['journal_abbreviation']); ?></td></tr>
                <tr><td>Editorial Board</td><td><?php echo $editorial_board; ?></td></tr>
                <tr><td>Country</td><td><?php echo htmlspecialchars($journal['country']); ?></td></tr>
                <tr><td>Publication Frequency</td><td><?php echo htmlspecialchars($journal['publication_frequency']); ?></td></tr>
                <tr><td>Indexing Info</td><td><?php echo htmlspecialchars($journal['indexing_info']); ?></td></tr>
                <tr><td>Scope</td><td><?php echo htmlspecialchars($journal['scope']); ?></td></tr>
                <tr><td>Author Guidelines</td><td><?php echo htmlspecialchars($journal['author_guidelines']); ?></td></tr>
                <tr><td>Review Process</td><td><?php echo htmlspecialchars($journal['review_process']); ?></td></tr>
                <tr><td>Impact Factor</td><td><?php echo !empty($journal['impact_factor']) ? htmlspecialchars($journal['impact_factor']).'%' : '—%'; ?></td></tr>
                <tr><td>CiteScore</td><td><?php echo !empty($journal['citescore']) ? htmlspecialchars($journal['citescore']).'%' : '—%'; ?></td></tr>
                <tr><td>Acceptance Rate</td><td><?php echo !empty($journal['acceptance_rate']) ? htmlspecialchars($journal['acceptance_rate']).'%' : '—%'; ?></td></tr>
            </table>
        </div>
    </div>

    <footer class="site-footer">
        <!-- Footer content here -->
    </footer>

    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</body>
</html>
<?php
$conn->close();
?>