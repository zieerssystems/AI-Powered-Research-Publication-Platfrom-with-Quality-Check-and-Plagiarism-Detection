<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

$paper_id = $_GET['paper_id'] ?? 0;
if (!$paper_id) die("Invalid Paper ID");

$paper = getPaperDetails($conn, $paper_id);
if (!$paper) die("Paper not found");

$co_authors = getCo_Authors($conn, $paper_id);
$journal_id = $paper['journal_id'];
$vol_issue_options = getVolumeIssueOptions($conn, $journal_id);
$current_volume = $paper['volume'];
$current_issue = $paper['issue'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($paper['title']); ?> | Zieers</title>
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

   
    .main {
      display: flex;
      padding: 20px;
    }
    .left-panel, .right-panel {
      width: 20%;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      margin: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    

    .center-panel {
      flex-grow: 1;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      margin: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 { margin-bottom: 10px; color: #222; }
    .meta, .section p { color: #555; font-size: 15px; margin: 5px 0; }
    .btn { padding: 8px 16px; margin: 10px 8px 10px 0; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    .btn-view { background-color: #007bff; color: white; }
    .btn-download { background-color: #28a745; color: white; }
    .btn-pay { background-color: #ffc107; color: #222; }
    select { padding: 8px; border-radius: 6px; width: 100%; margin-bottom: 10px; }
    .related-paper { border-bottom: 1px solid #ddd; padding: 10px 0; }
    .related-paper strong { color: #007bff; cursor: pointer; }
    .related-meta { font-size: 14px; color: #666; margin-top: 4px; }
    .nav-btns {
  display: flex;
  justify-content: space-between;
  margin-top: 40px;  /* More spacing from content above */
  padding: 20px;
  background-color: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.nav-btns a {
  padding: 10px 18px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  text-decoration: none;
  font-size: 15px;
  color: white;
  background-color: #007bff;
  transition: background-color 0.3s ease;
}

.nav-btns a:hover {
  background-color: #0056b3;
}
.action-buttons {
  margin-top: 30px;
  padding: 20px;
  background: linear-gradient(145deg, #f8f9fc, #e8ebf0);
  border-radius: 12px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
  display: flex;
  gap: 15px;
  justify-content: flex-start;
}
..action-buttons .btn-view {
  flex: 1;
  max-width: 50%;
}

..action-buttons .btn-download {
  flex: 1;
  max-width: 50%;
  text-align: right;
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
  <div class="main">
    <div class="left-panel">
      <h3>Browse by Volume/Issue</h3>
      <select id="volume-select">
      <?php foreach ($vol_issue_options as $opt): ?>
  <option value="<?php echo $opt['volume']; ?>">Volume <?php echo $opt['volume']; ?></option>
<?php endforeach; ?>

      </select>

      <select id="issue-select"></select>

      <button class="btn btn-view" onclick="fetchPapers()">Show Papers</button>

      <div id="volume-issue-results" style="margin-top: 20px;"></div>
    </div>

    <div class="center-panel" id="center-paper">
      <h2><?php echo htmlspecialchars($paper['title']); ?></h2>
      <p class="meta"><strong>Author:</strong> <?php echo $paper['author_fname'] . ' ' . $paper['author_lname']; ?></p>
      <?php if (!empty($co_authors)): ?>
        <p class="meta"><strong>Co-authors:</strong> <?php echo implode(', ', $co_authors); ?></p>
      <?php endif; ?>
      <p class="meta"><strong>DOI:</strong> <?php echo htmlspecialchars($paper['doi']); ?></p>
      <p class="meta"><strong>Volume:</strong> <?php echo $paper['volume']; ?> | <strong>Issue:</strong> <?php echo $paper['issue']; ?></p>
      <p class="meta"><strong>Completed:</strong> <?php echo date("Y-m-d", strtotime($paper['completed_date'])); ?></p>

      <div class="section">
        <p><strong>Abstract:</strong><br><?php echo nl2br(htmlspecialchars($paper['abstract'])); ?></p>
        <p><strong>Keywords:</strong> <?php echo htmlspecialchars($paper['keywords']); ?></p>
      </div>

      <?php
      $filepath = "../../uploads/" . $paper['file_path'];
     $has_access = false;

if ($paper['access_type'] === 'Open Access') {
    $has_access = true;
} elseif (isset($_SESSION['user_id'])) {
    $author_id = $_SESSION['user_id'];
    $has_access = hasUserPaidForPaper($conn, $author_id, $paper_id);
}
 ?>

<?php if ($has_access): ?>
  <div class="action-buttons">
<a class="btn btn-view" href="view_pdf.php?file=<?php echo urlencode($paper['file_path']); ?>" target="_blank">View PDF</a>

<a class="btn btn-download" href="paper_download.php?file=<?php echo urlencode(basename($paper['file_path'])); ?>">Download</a>

  </div>
<?php else: ?>

        <p><strong>This is a subscription-based journal. Payment: ₹<?php echo $paper['reader_fee_amount']; ?></strong></p>
        <button class="btn btn-view" onclick="document.getElementById('payModal').style.display='block'">Get Access</button>
        <div id="payModal" style="display:none; margin-top:20px;">
          <?php if (!isset($_SESSION['user_id'])): ?>
            <form method="post" action="user_auth_or_register.php">
              <input type="hidden" name="redirect_to" value="paper.php?paper_id=<?php echo $paper_id; ?>">
              <input type="text" name="fname" placeholder="First Name" required><br>
              <input type="text" name="lname" placeholder="Last Name" required><br>
              <input type="email" name="email" placeholder="Email" required><br>
              <button class="btn btn-view" type="submit">Login / Register</button>
            </form>
          <?php else: ?>
            <form action="checkout.php" method="post">
  <input type="hidden" name="selected_papers[]" value="<?php echo $paper_id; ?>">
  <button class="btn btn-pay" type="submit">Pay and Access</button>
</form>


          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="right-panel">
    <h3>Related Papers</h3>
    <?php
    $related_papers = getRelatedPapers($conn, $paper['journal_id'], $paper_id);
    while ($rel = $related_papers->fetch_assoc()):
        $co_list = getCoAuthorsForPaper($conn, $rel['id']);
    ?>
      <div class="related-paper" onclick="loadPaper(<?php echo $rel['id']; ?>)">
        <strong><?php echo htmlspecialchars($rel['title']); ?></strong>
        <div class="related-meta">
          <?php echo $rel['first_name'] . ' ' . $rel['last_name']; ?><?php if (!empty($co_list)) echo ' | Co-authors: ' . implode(', ', $co_list); ?> | <?php echo $rel['access_type']; ?> | <?php echo date("Y-m-d", strtotime($rel['completed_date'])); ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
  <?php
$next_paper = getNextPaper($conn, $journal_id, $current_volume, $current_issue);
$prev_paper = getPreviousPaper($conn, $journal_id, $current_volume, $current_issue);
?>


<div class="nav-btns center-panel">
  <?php if ($prev_paper): ?>
    <a class="btn btn-view" href="paper.php?paper_id=<?php echo $prev_paper['id']; ?>">⬅ Previous Paper</a>
  <?php else: ?>
    <button class="btn btn-view" disabled>⬅ No Previous</button>
  <?php endif; ?>

  <?php if ($next_paper): ?>
    <a class="btn btn-view" href="paper.php?paper_id=<?php echo $next_paper['id']; ?>">Next Paper ➡</a>
  <?php else: ?>
    <button class="btn btn-view" disabled>No Next ➡</button>
  <?php endif; ?>
</div>

<script>
const volumeSelect = document.getElementById('volume-select');
const issueSelect = document.getElementById('issue-select');

volumeSelect.addEventListener('change', function () {
  fetch(`fetch_issues.php?volume=${this.value}&journal_id=<?php echo $paper['journal_id']; ?>`)
    .then(response => response.json())
    .then(issues => {
      issueSelect.innerHTML = '';
      issues.forEach(issue => {
        const opt = document.createElement('option');
        opt.value = issue;
        opt.textContent = 'Issue ' + issue;
        issueSelect.appendChild(opt);
      });
    });
});

volumeSelect.dispatchEvent(new Event('change'));

function fetchPapers() {
  const volume = volumeSelect.value;
  const issue = issueSelect.value;
  const journal_id = <?php echo $paper['journal_id']; ?>;

  fetch(`fetch_papers_by_volume_issue.php?volume=${volume}&issue=${issue}&journal_id=${journal_id}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('volume-issue-results').innerHTML = html;
    });
}

function loadPaper(paperId) {
  window.location.href = `paper.php?paper_id=${paperId}`;
}
</script>
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
