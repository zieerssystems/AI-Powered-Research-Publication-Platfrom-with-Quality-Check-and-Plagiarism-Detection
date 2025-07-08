<?php 
session_start();
include(__DIR__ . "/../../include/db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Journal Finder</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
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


    .dropdown {
      position: relative;
      display: inline-block;
      margin-left: 20px;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f9f9f9;
      min-width: 160px;
      z-index: 1;
    }
    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }
    .dropdown-content a:hover {background-color: #f1f1f1;}
    .dropdown:hover .dropdown-content {display: block;}
    main {
      display: flex;
      padding: 40px;
      gap: 40px;
    }
    .filter-box {
      width: 25%;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }
    .filter-box h2 {
      font-size: 18px;
      margin-bottom: 15px;
      color: #0073e6;
    }
    .range-group {
      margin-top: 10px;
    }
    .range-group label {
      display: block;
      margin-bottom: 8px;
    }
    .range-group input[type=range] {
      width: 100%;
    }
    .range-value {
      font-weight: bold;
      color: #333;
    }
    .results-box {
      flex: 1;
    }
    .search-section {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }
    .search-section h1 {
      font-size: 22px;
      color: #0073e6;
      margin-bottom: 10px;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    textarea {
      padding: 10px;
      resize: vertical;
      min-height: 120px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button[type="submit"] {
      background-color: #0073e6;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }
    .journal-card {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 0 12px rgba(0,0,0,0.05);
      display: flex;
      gap: 20px;
    }
    .journal-card img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
    }
    .journal-details {
      flex: 1;
    }
    .journal-title {
      font-size: 18px;
      font-weight: bold;
      color: #0073e6;
    }
    .journal-info {
      font-size: 14px;
      margin: 5px 0;
    }
    .action-btn {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 14px;
      background-color: #0073e6;
      color: white;
      text-decoration: none;
      border-radius: 6px;
    }
    .details {
      display: none;
      margin-top: 10px;
      font-size: 14px;
    }
    .more-info-btn {
      cursor: pointer;
      color: #0073e6;
      font-size: 14px;
      text-decoration: underline;
      background: none;
      border: none;
      margin-top: 5px;
    }
    .nav-left {
  display: flex;
  align-items: center;
  justify-content: flex-start; /* Aligns items to the left */
}

.nav-left a {
  margin-left: 20px; /* Keeps spacing between links */
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
    <div class="container header-container">
  <div class="logo">
  <a href="../../publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
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
    <a href="#"><?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
    <ul class="dropdown-menu">
        <li><a href="../../profile.php">View Profile</a></li>
        <li><a href="../../logout.php">Logout</a></li>
    </ul>
</li>
            </ul>
            <!-- <a href="publish-with-us.php" class="publish-btn">Publish with Us</a> -->
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
<!-- <div class="breadcrumb-container">
    <ul class="breadcrumb">
      <li><a href="../../submit-page.php">Submit Manuscripts</a></li>
      <li>&gt;</li>
      <li>journal Finder</li>
    </ul>
  </div> -->

  <main>
    <div class="filter-box">
      <h2>Filter by Acceptance Rate</h2>
      <div class="range-group">
        <label for="acceptanceRange">Min Acceptance Rate: <span id="rangeValue">1</span></label>
        <input type="range" id="acceptanceRange" min="1" max="10" value="1" />
      </div>
      <h2>Filter by CiteScore</h2>
      <div class="range-group">
        <label for="citescoreRange">Min CiteScore: <span id="citescoreValue">1</span></label>
        <input type="range" id="citescoreRange" min="1" max="10" value="1" />
      </div>
      <h2>Filter by Impact Factor</h2>
      <div class="range-group">
        <label for="impactRange">Min Impact Factor: <span id="impactValue">1</span></label>
        <input type="range" id="impactRange" min="1" max="10" value="1" />
      </div>
    </div>

    <div class="results-box">
      <div class="search-section">
        <h1>Find the Right Journal</h1>
        <form id="journal-form">
          <select id="search-type" name="search-type">
            <option value="keyword">Keyword</option>
            <option value="abstract">Abstract</option>
          </select>
          <textarea id="search-input" name="search-input" placeholder="Enter keyword or abstract..."></textarea>
          <button type="submit">Find Journal</button>
        </form>
      </div>

      <div id="results-section"></div>
    </div>
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
  <script>
    const form = document.getElementById('journal-form');
    const range = document.getElementById('acceptanceRange');
    const rangeValue = document.getElementById('rangeValue');
    const citescoreRange = document.getElementById('citescoreRange');
    const impactRange = document.getElementById('impactRange');
    const citescoreValue = document.getElementById('citescoreValue');
    const impactValue = document.getElementById('impactValue');
    const resultsSection = document.getElementById('results-section');

    range.addEventListener('input', () => {
      rangeValue.textContent = range.value;
      form.dispatchEvent(new Event('submit'));
    });

    citescoreRange.addEventListener('input', () => {
      citescoreValue.textContent = citescoreRange.value;
      form.dispatchEvent(new Event('submit'));
    });

    impactRange.addEventListener('input', () => {
      impactValue.textContent = impactRange.value;
      form.dispatchEvent(new Event('submit'));
    });

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      const formData = new FormData(form);
      const searchType = formData.get('search-type');
      const searchInput = formData.get('search-input').trim();
      const minAcceptance = range.value;

      resultsSection.innerHTML = '';

      if (searchType === 'abstract' && searchInput.length < 250) {
        resultsSection.innerHTML = `<p>Please enter a longer abstract (minimum 250 characters).</p>`;
        return;
      }

      if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
        resultsSection.innerHTML = `<p>You need to log in first to find the right journal.</p>`;
        return;
      }

      try {
        const response = await fetch('search.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            type: searchType,
            input: searchInput,
            acceptance: minAcceptance,
            citescore: citescoreRange.value,
            impact_factor: impactRange.value
          })
        });

        const journals = await response.json();

        if (!journals.length || journals.error) {
          resultsSection.innerHTML = `<p>${journals.error || 'No journals found matching the criteria.'}</p>`;
          return;
        }

        journals.forEach(journal => {
          const card = document.createElement('div');
          card.className = 'journal-card';
          card.innerHTML = `
            <img src="/admin/${journal.journal_image}" alt="Journal Image" />
            <div class="journal-details">
              <div class="journal-title">${journal.journal_name}</div>
              <div class="journal-info"><strong>CiteScore:</strong> ${journal.citescore || 'N/A'}</div>
              <div class="journal-info"><strong>Impact Factor:</strong> ${journal.impact_factor || 'N/A'}</div>
              <div class="journal-info"><strong>Acceptance Rate:</strong> ${journal.acceptance_rate || 'N/A'}</div>
              <button class="more-info-btn" onclick="toggleDetails('${journal.id}')">More About Journal</button>
              <div class="details" id="details-${journal.id}">
                <div><strong>ISSN:</strong> ${journal.issn}</div>
                <div><strong>Scope:</strong> ${journal.scope}</div>
                <div><strong>Last Issue Paper Title:</strong> ${journal.last_issue_title || 'N/A'}</div>
              </div>
              <a href="journal_detail.php?journal_id=${journal.id}" class="action-btn">Visit Journal Page</a>
            </div>
          `;
          resultsSection.appendChild(card);
        });
      } catch (err) {
        resultsSection.innerHTML = `<p style="color:red;">Failed to fetch journal data. Please try again later.</p>`;
      }
    });

    function toggleDetails(id) {
      const details = document.getElementById(`details-${id}`);
      details.style.display = details.style.display === 'block' ? 'none' : 'block';
    }
  </script>
</body>
</html>
