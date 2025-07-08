<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

include("include/db_connect.php");  // This now includes both DB connection + the SQL functions

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$sort = $_GET['sort'] ?? '';
$search = $_GET['search'] ?? '';

$total_result = fetchTotalJournals($search);
$total_pages = ceil($total_result / $limit);
$result = fetchJournals_1($limit, $offset, $sort, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Catalog</title>
    <style>
          body {
            font-family: 'Poppins', sans-serif;
            background-color: #eef2f3;
            color: #333;
        }
        /* Header and Navigation Styling */
header {
    background: #002147;
    padding: 20px;
    color: white;
}
.login-btn {
    background-color: #004080;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
}
.login-btn:hover {
    background-color: #0066cc;
}
.header-container h1 {
    margin: 0;
    flex: 0 0 auto; /* Keeps Zieers on the left */
}
/* Ensure dropdown appears above banner */
.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 9999; /* Ensure the header is above other elements */
}

nav {
    position: absolute;
    right: 20px; /* or 10px or any padding from right */
    left: auto;
    transform: none;
}
nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

.nav-links {
    list-style: none;
    display: flex;
}

.nav-links li {
    margin: 0 10px;
}

.nav-links li a {
    color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
       
}

.nav-links li a:hover {
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

   

        .search-bar { width: 70%; padding: 12px 15px; font-size: 16px; border: 1px solid #ccc; border-radius: 8px; margin-top: 20px; }
        .clear-btn { margin-left: 10px; padding: 12px 15px; background: #ccc; border: none; border-radius: 8px; cursor: pointer; }
        .header { display: flex; justify-content: space-between; align-items: center; margin: 20px 0; }

        .journal-actions a {
            display: inline-block;
            margin-top: 10px;
            margin-right: 10px;
            background: none;
            color: #007BFF;
            padding: 0;
            border-radius: 0;
            text-decoration: underline;
        }

        .sort-filter { display: flex; align-items: center; gap: 15px; }
        .sort-filter select { padding: 10px 15px; border: 1px solid #007BFF; background: white; color: #007BFF; border-radius: 8px; }
        .journal-rows { display: grid; grid-template-columns: 1fr; gap: 20px; }
        .journal-row { display: flex; background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden; padding: 20px; align-items: center; }
        .journal-image { flex: 1; }
        .journal-image img {
            height: 120px;
            width: 120px;
            object-fit: cover;
            border-radius: 10px;
        }
        .journal-info { flex: 3; padding-left: 30px; }
        .journal-info h2 { margin: 0 0 10px; font-size: 20px; color: #002147;; }
        .journal-info p {
            color: #002147;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .journal-metrics { display: flex; gap: 20px; font-size: 14px; color: #333; margin: 10px 0; }

        .payment-info {
            margin-top: 10px;
            font-size: 14px;
            color: #002147;;
            background: #f5f5f5;
            border-radius: 8px;
            padding: 10px;
        }
        .toggle-btn {
            cursor: pointer;
            color: #002147;
            text-decoration: underline;
            display: inline-block;
            margin-top: 10px;
        }

        .pagination { text-align: center; margin-top: 30px; }
        .pagination a { margin: 0 5px; text-decoration: none; font-weight: bold; color: #333; }
        .pagination a.active { color: #ff9800; }

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
    <script>
        function onSortChange(value) {
            const url = new URL(window.location);
            url.searchParams.set('sort', value);
            window.location = url;
        }
        function clearSearch() {
            const url = new URL(window.location);
            url.searchParams.delete('search');
            window.location = url;
        }
        function togglePayment(id) {
    const el = document.getElementById('payment-' + id);
    const btn = document.getElementById('btn-' + id);

    if (el.style.display === 'none') {
        const accessType = btn.getAttribute('data-access');
        const authorApc = btn.getAttribute('data-author-apc');
        const readerFee = btn.getAttribute('data-reader-fee');

        let label = '';
        let amount = '';

        if (accessType === 'Open Access') {
            label = 'Article Type – Article Publishing Charge (excl. taxes)';
            amount = authorApc ? `₹ ${authorApc}` : 'Not available';
        } else if (accessType === 'Subscription-Based') {
            label = 'Article Type – Article Reader Charge (excl. taxes)';
            amount = readerFee ? `₹ ${readerFee}` : 'Not available';
        } else {
            label = 'Article Type – Unknown Access Type';
            amount = 'N/A';
        }

        el.innerHTML = `<div><strong>${label}</strong></div><div>${amount}</div>`;
        el.style.display = 'block';
        btn.innerText = 'Show Less';
    } else {
        el.style.display = 'none';
        btn.innerText = 'View Payment Details';
    }
}
</script>
</head>
<body>
<header>
    <div class="container header-container">
         <div class="logo">
  <a href="publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
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
        </nav>
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
<div class="container">
    <h1>Journal Catalog</h1>
    <p>Choose the journal that’s right for you. Browse through over 2,900 Zieers titles across all areas of science.</p>

    <form method="get">
        <input type="text" name="search" class="search-bar" placeholder="Search journals..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
        <button type="submit" class="clear-btn">Search</button>
        <a href="journal.php" class="clear-btn">Clear</a>
    </form>

    <div class="header">
        <div class="sort-filter">
            <select onchange="onSortChange(this.value)">
                <option value="">Sort by Relevance</option>
                <option value="asc" <?php if($sort==='asc') echo 'selected'; ?>>A-Z</option>
                <option value="desc" <?php if($sort==='desc') echo 'selected'; ?>>Z-A</option>
            </select>
        </div>
        <!-- Filter Button -->
        <span id="result-count">Results: <?php echo $total_result; ?></span>
    </div>

    <div class="journal-rows">
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <div class="journal-row">
            <div class="journal-image">
            <img src="admin/<?php echo $row['journal_image']; ?>" alt="Journal Image">
            </div>
            <div class="journal-info">
                <h2><a href="researcher/author/journal_detail.php?journal_id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['journal_name']); ?></a></h2>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <div class="journal-metrics">
                    <span><strong>Impact Factor:</strong> <?php echo htmlspecialchars($row['impact_factor'] ?: '0.0%'); ?></span>
                    <span><strong>CiteScore:</strong> <?php echo htmlspecialchars($row['citescore'] ?: '0.0%'); ?></span>
                    <span><strong>Acceptance Rate:</strong> <?php echo htmlspecialchars($row['acceptance_rate'] ?: '0.0%'); ?>%</span>
                </div>
                <hr>
                <div class="journal-actions">
                    <a href="researcher/author/submit-article.php?journal_id=<?php echo $row['id']; ?>">Submit Paper</a>
                    <a href="researcher/author/author-guidelines.php?journal_id=<?php echo $row['id']; ?>">Author Guidelines</a>
                </div>
                <div class="toggle-btn" onclick="togglePayment(<?php echo $row['id']; ?>)" 
     id="btn-<?php echo $row['id']; ?>" 
     data-access="<?php echo $row['access_type']; ?>"
     data-author-apc="<?php echo $row['author_apc_amount']; ?>"
     data-reader-fee="<?php echo $row['reader_fee_amount']; ?>">
    View Payment Details
</div>

<div class="payment-info" id="payment-<?php echo $row['id']; ?>" style="display:none;"></div>

            </div>
        </div>
        <?php } ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?><a href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>&search=<?php echo urlencode($search); ?>">&laquo; Previous</a><?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?><a href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>&search=<?php echo urlencode($search); ?>">Next &raquo;</a><?php endif; ?>
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
</body>
</html>
