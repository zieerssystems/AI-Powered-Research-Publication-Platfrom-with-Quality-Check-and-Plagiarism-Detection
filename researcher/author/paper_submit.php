<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    die("Error: Unauthorized access. Please log in.");
}

$journal_name = $_SESSION['journal_name'] ?? "Unknown Journal";
$author_name = $_SESSION['first_name'] . " " . $_SESSION['last_name'];
$author_email = $_SESSION['author_email'];

if (!empty($_SESSION['errors'])) {
    echo "<div class='error-box'>";
    echo "<strong>Error(s) found:</strong><ul>";
    foreach ($_SESSION['errors'] as $field => $message) {
        echo "<li>$message</li>";
    }
    echo "</ul></div>";
    unset($_SESSION['errors']); // Clear errors after displaying
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Paper</title>
    <style>
    body {
        background-color: #e0f7fa; /* Light Blue Background */
        font-family: Arial, sans-serif;
    }
    .container { max-width: 800px; margin: auto; padding: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-weight: bold; }
    .form-group input, .form-group textarea { width: 100%; padding: 8px; }
    .form-group input[type="file"] { padding: 5px; }
    #author-fields { margin-bottom: 20px; }
    .author-section { margin-bottom: 20px; }
    .radio-group {
    display: flex; /* Display the radio buttons inline */
    gap: 10px; /* Adds space between the radio buttons */
    align-items: center; /* Aligns the radio buttons vertically */
}

    /* Blue Button Styles */
    .blue-btn {
        background-color: #007bff; /* Blue Background */
        color: white; /* White text */
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        text-align: center;
    }
    .blue-btn:hover {
        background-color: #0056b3; /* Darker blue on hover */
    }

    /* Centering the Buttons */
    .center-btn {
        text-align: center;
    }

    /* Error Message Styling */
    .error-box {
        background-color: #ffdddd;
        border: 1px solid red;
        padding: 10px;
        margin-bottom: 15px;
    }
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
footer {
      background: #002147;
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: auto;
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
          <div class="logo">
    <a href="../../publish.php">
      <img src="../../images/logo.png" alt="Zieers Logo">
    </a>
  </div>
    <div class="icons">
        <a href="../../publish.php">Home</a>
        <a href="../../services.php">Services</a>
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

    <h3>Submit Your Paper</h3>

    <!-- Error Message Display -->
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <strong>Error(s) found:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="process_paper_submit.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title of Paper *:</label>
            <input type="text" name="paper_title" required>
        </div>

        <div class="form-group">
            <label>Abstract: *</label>
            <textarea name="paper_abstract" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label>Keywords: *</label>
            <textarea name="paper_keywords" rows="4" required></textarea>
        </div>

        <h3>Primary Author</h3>
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="primary_author" value="<?php echo htmlspecialchars($author_name); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="primary_author_email" value="<?php echo htmlspecialchars($author_email); ?>" readonly>
        </div>

        <!-- Author Fields -->
        <div id="author-fields">
            <div class="author-section">
            <h3>Second Author</h3>
<div class="form-group">
    <label>Name:</label>
    <input type="text" name="second_author">
</div>
<div class="form-group">
    <label>Email:</label>
    <input type="email" name="second_author_email">
</div>
<div class="form-group">
    <label>Affiliation:</label><br>
    <div class="radio-group">
    Individual<input type="radio" name="second_author_affiliation_type" value="individual" onchange="toggleAffiliationFields('second')"> 
    Affiliated<input type="radio" name="second_author_affiliation_type" value="affiliated" onchange="toggleAffiliationFields('second')"> 
    </div>
</div>
<div id="affiliation-fields-second" class="affiliation-details" style="display: none;">
    <div class="form-group">
        <label>Position:</label>
        <input type="text" name="second_author_position">
    </div>
    <div class="form-group">
        <label>Institute:</label>
        <input type="text" name="second_author_institute">
    </div>
    <div class="form-group">
        <label>City:</label>
        <input type="text" name="second_author_city">
    </div>
    <div class="form-group">
        <label>State:</label>
        <input type="text" name="second_author_state">
    </div>
    <div class="form-group">
        <label>Country:</label>
        <input type="text" name="second_author_country">
    </div>
    <div class="form-group">
        <label>Education:</label>
        <input type="text" name="second_author_education">
    </div>
</div>
    </div>
        </div>
<br>
<br>
        <div class="center-btn">
            <button type="button" class="blue-btn" onclick="addAuthorFields()">Add More Authors</button>
        </div>

        <div class="form-group">
            <label>Upload Paper (PDF) *:</label>
            <input type="file" name="paper_file" required>
        </div>

        <div class="form-group">
            <label>Upload Cover Letter (PDF) *:</label>
            <input type="file" name="cover_letter" accept=".pdf">
        </div>

        <div class="form-group">
            <label>Upload Copyright Agreement (PDF) *:</label>
            <input type="file" name="copyright_agreement" accept=".pdf">
        </div>

        <div class="form-group">
            <label>Upload Supplementary Files (Multiple, ZIP/PDF/DOCX):</label>
            <input type="file" name="supplementary_files[]" accept=".zip,.pdf,.docx" multiple>
        </div>
        
        <div class="center-btn">
            <button type="submit" id="submit-btn" class="blue-btn">Submit Paper</button>
        </div>
        <div id="submit-message" style="color: green; font-weight: bold; margin-bottom: 10px; display: none;"></div>

    </form>
</div>
<script>
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submit-btn');
    const submitMsg = document.getElementById('submit-message');

    form.addEventListener('submit', function(e) {
        // Show waiting message
        submitMsg.style.color = 'blue';
        submitMsg.textContent = 'Please wait a sec...';
        submitMsg.style.display = 'block';

        // Disable submit button to prevent multiple submissions
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
    });
</script>

<script>
    let authorCount = 2; // Track dynamically added authors

    function addAuthorFields() {
        authorCount++;
        let newAuthor = document.createElement('div');
        newAuthor.classList.add('author-section');
        newAuthor.innerHTML = `
            <h3>Author ${authorCount}</h3>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="author_${authorCount}_name">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="author_${authorCount}_email">
            </div>
            <div class="form-group">
                <label>Affiliation:</label>
                <input type="radio" name="author_${authorCount}_affiliation_type" value="individual" onchange="toggleAffiliationFields(${authorCount})"> Individual
                <input type="radio" name="author_${authorCount}_affiliation_type" value="affiliated" onchange="toggleAffiliationFields(${authorCount})"> Affiliated
            </div>
            <div id="affiliation-fields-${authorCount}" class="affiliation-details" style="display: none;">
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="author_${authorCount}_position">
                </div>
                <div class="form-group">
                    <label>Institute:</label>
                    <input type="text" name="author_${authorCount}_institute">
                </div>
                <div class="form-group">
                    <label>City:</label>
                    <input type="text" name="author_${authorCount}_city">
                </div>
                <div class="form-group">
                    <label>State:</label>
                    <input type="text" name="author_${authorCount}_state">
                </div>
                <div class="form-group">
                    <label>Country:</label>
                    <input type="text" name="author_${authorCount}_country">
                </div>
                <div class="form-group">
                    <label>Education:</label>
                    <input type="text" name="author_${authorCount}_education">
                </div>
            </div>
        `;
        document.getElementById('author-fields').appendChild(newAuthor);
    }

    function toggleAffiliationFields(authorIndex) {
        let fieldId = authorIndex === 'second' ? 'affiliation-fields-second' : `affiliation-fields-${authorIndex}`;
        let affiliationFields = document.getElementById(fieldId);
        if (!affiliationFields) return;

        // Find the selected radio button properly
        let radioSelector = authorIndex === 'second' ? 
            'input[name="second_author_affiliation_type"]:checked' :
            `input[name="author_${authorIndex}_affiliation_type"]:checked`;

        let selectedRadio = document.querySelector(radioSelector);
        if (!selectedRadio) return;

        affiliationFields.style.display = selectedRadio.value === "affiliated" ? "block" : "none";
    }
</script>
<script>
  const form = document.querySelector('form');
  form.addEventListener('submit', function() {
    // Disable submit button to prevent multiple submits
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = "Please wait...";
  });
</script>
 <footer>
   <p onclick="window.open('https://www.zieers.com/', '_blank');">
    &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
</p>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>
