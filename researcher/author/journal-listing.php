<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];  // Store the current page
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . "/../../include/db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publications</title>
    <link rel="stylesheet" href="styles.css">
    <style>
         body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9; /* Light Gray Background */
            margin: 0;
            padding: 0;
        }

       header {
  background: #002147;
  color: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Keep logo on left */
.logo {
  font-size: 28px;
  font-weight: bold;
  color: #ECF0F1;
}

/* Container for the links on right */
.nav-links {
  display: flex;
  align-items: center;
  gap: 20px; /* space between links */
}

/* Style links */
.nav-links a {
  color: #ECF0F1;
  text-decoration: none;
  font-size: 16px;
  cursor: pointer;
}

/* Dropdown container */
.dropdown {
  position: relative;
}

/* Dropdown toggle link */
.dropdown > a.user-name {
  color: #ECF0F1;
  text-decoration: none;
  cursor: pointer;
  font-size: 16px;
}

/* Dropdown menu - initially hidden */
.dropdown-menu {
  display: none;
  position: absolute;
  background-color: #002147;
  list-style: none;
  padding: 0;
  margin: 0;
  top: 100%;
  left: 0;
  border-radius: 5px;
  min-width: 150px;
  z-index: 100;
}

/* Dropdown menu visible on hover */
.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-menu li a {
  display: block;
  padding: 10px 20px;
  color: white;
  text-decoration: none;
}

.dropdown-menu li a:hover {
  background: #004080;
}

        /* Journal Header Section */
        .journal-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
             background: #002147; /* Dark Blue Background */
            padding: 30px;
            border-bottom: 2px solid #BDC3C7;
            margin-top: 30px;
        }

        /* Layout */
        .container {
            display: flex;
            padding: 20px;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: white;
            padding: 15px;
            border-right: 1px solid #ddd;
        }

        .sidebar h2, .sidebar h3 {
            font-size: 16px;
            margin-top: 20px;
        }

        .sidebar label {
            display: block;
            margin: 5px 0;
        }

        .sidebar select {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }

        .sidebar input[type="checkbox"] {
            margin-right: 5px;
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
     background: #002147;
}
        /* Main Content */
        main {
            flex-grow: 1;
            padding: 20px;
            text-align: center;
        }

        /* Search Bar */
        .search-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-container input {
            width: 50%;
            padding: 10px;
            font-size: 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .publication-list ul {
            list-style: none;
            padding: 0;
        }

        .publication-list ul li {
            padding: 5px 0;
        }

        .publication-list ul li a {
            color: #0073e6;
            text-decoration: none;
        }

        .publication-list ul li a:hover {
            text-decoration: underline;
        }

        /* Alphabet Navigation */
        .alphabet-nav {
            width: 50px;
            text-align: center;
        }

        .alphabet-nav ul {
            list-style: none;
            padding: 0;
        }

        .alphabet-nav ul li {
            padding: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .alphabet-nav ul li:hover {
             background: #002147;
        }
        .journal-item {
        font-size: 16px;
        margin-bottom: 10px;
        }

        .journal-item span {
        font-weight: bold;
        color: #0073e6; /* Blue color for highlighting */
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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
       document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("primary-subject").addEventListener("change", filterJournals);
    document.getElementById("secondary-subject").addEventListener("change", filterJournals);
    document.getElementById("open-access").addEventListener("change", filterJournals);
    document.getElementById("subscription").addEventListener("change", filterJournals);
    document.getElementById("submissions").addEventListener("change", filterJournals);
    document.getElementById("closed-submissions").addEventListener("change", filterJournals);
    document.getElementById("search").addEventListener("keyup", filterJournals);

    function filterJournals() {
        let searchValue = document.getElementById("search").value.toLowerCase();
        let primarySubject = document.getElementById("primary-subject").value;
        let secondarySubject = document.getElementById("secondary-subject").value;
        let openAccess = document.getElementById("open-access").checked ? "Open Access" : "";
        let subScription = document.getElementById("subscription").checked ? "Subscription-Based" : "";
        let acceptsSubmissions = document.getElementById("submissions").checked ? "Accepting Submissions" : "";
        let closedSubmissions = document.getElementById("closed-submissions").checked ? "Closed for Submissions" : "";

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "../../fetch_journals.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById("journal-list").innerHTML = xhr.responseText;
            }
        };

        xhr.send(
            "search=" + encodeURIComponent(searchValue) +
            "&primary_subject=" + encodeURIComponent(primarySubject) +
            "&secondary_subject=" + encodeURIComponent(secondarySubject) +
            "&open_access=" + encodeURIComponent(openAccess) +
            "&subscription=" + encodeURIComponent(subScription) +
            "&submissions=" + encodeURIComponent(acceptsSubmissions)+
            "&closed_submissions="+ encodeURIComponent(closedSubmissions)
        );
    }
});

function filterByLetter(letter) {
    let items = document.querySelectorAll(".journal-item");

    items.forEach(item => {
        let journalName = item.querySelector("a").textContent.trim().toUpperCase(); // Ensure it's uppercase
        if (journalName.startsWith(letter)) {
            item.style.display = "block";
        } else {
            item.style.display = "none";
        }
    });
}


        function updateSecondary() {
            let primary = document.getElementById("primary-subject").value;
            let secondary = document.getElementById("secondary-subject");

            let subjects = {
                "Computer Science": ["AI & Machine Learning", "Cybersecurity", "Software Engineering"],
                "Engineering": ["Mechanical", "Electrical", "Civil"],
                "Medicine": ["Cardiology", "Neurology", "Oncology"],
                "Social Sciences": ["Psychology", "Sociology", "Political Science"],
                "Chemistry": ["Organic", "Inorganic", "Physical"]
            };

            secondary.innerHTML = "<option>Select secondary subject</option>";
            if (primary in subjects) {
                subjects[primary].forEach(sub => {
                    let option = document.createElement("option");
                    option.text = sub;
                    secondary.add(option);
                });
                secondary.disabled = false;
            } else {
                secondary.disabled = true;
            }
        }
    </script>
</head>
<body>
    <header>
   <div class="logo">
  <a href="../../publish.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
  <nav class="nav-links">
    <a href="../../publish.php">Home</a>
    <br>
    <a href="../../services.php">Services</a>
    <br>
    <div class="dropdown">
    <a href="#">For Users ▼</a>
    <ul class="dropdown-menu">
        <li><a href="../../for_author.php">For Author</a></li>
        <li><a href="../../for_reviewer.php">For Reviewer</a></li>
        <li><a href="../../for_editor.php">For Editor</a></li>
    </ul>
</div>
<br>
    <div class="dropdown">
      <a href="#" class="user-name"><?php echo htmlspecialchars($_SESSION['first_name']); ?> ▼</a>
      <ul class="dropdown-menu">
        <li><a href="profile.php">View Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>
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
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Refine Publications by</h2>
            
            <label>Primary subject area</label>
            <select id="primary-subject" onchange="updateSecondary()">
                <option>Select primary subject</option>
                <option>Computer Science</option>
                <option>Engineering</option>
                <option>Medicine</option>
                <option>Social Sciences</option>
                <option>Chemistry</option>
            </select>
            
            <label>Secondary subject area</label>
            <select id="secondary-subject" disabled>
                <option>Select secondary subject</option>
            </select>
            
            <h3>Publication type</h3>
<label><input type="checkbox" id="journals" onchange="filterJournals()"> Journals</label>


<h3>Journal status</h3>
<label><input type="checkbox" id="submissions" onchange="filterJournals()"> Accepts submissions</label>
<label><input type="checkbox" id="closed-submissions" onchange="filterJournals()"> Closed submissions </label>

<h3>Access type</h3>
<label><input type="checkbox" id="open-access" onchange="filterJournals()"> Open Access</label>
<label><input type="checkbox" id="subscription" onchange="filterJournals()"> Subscription-Based</label>

        </aside>
        

        <!-- Main Content -->
        <main>
            <div class="search-container">
                <input type="text" id="search" placeholder="Search publications..." onkeyup="filterJournals()">
            </div>

            <div class="publication-list">
                <h2>Publications</h2>
                <ul id="journal-list">
                    <?php
                    $query = "SELECT id, journal_name, access_type FROM journals ORDER BY journal_name ASC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Loop through each journal and display it
    while ($row = $result->fetch_assoc()) {
        echo "<li class='journal-item'>
                <a href='journal_detail.php?journal_id=" . $row['id'] . "'>{$row['journal_name']}</a> <br>
                <span>{$row['access_type']}</span>
              </li>";
    }
} else {
    echo "No journals found.";
}

                    $conn->close();
                    ?>
                </ul>
            </div>
        </main>

        <!-- Alphabet Navigation -->
        <nav class="alphabet-nav">
            <ul>
                <script>
                    let alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
                    alphabet.forEach(letter => {
                        document.write(`<li onclick="filterByLetter('${letter}')">${letter}</li>`);
                    });
                </script>
            </ul>
        </nav>
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
