<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

$user_id = $_SESSION['user_id'];

// Ensure journal_id is retrieved
if (!isset($_GET['journal_id']) || empty($_GET['journal_id'])) {
    die("Error: journal_id is missing in the URL.");
}

$journal_id = intval($_GET['journal_id']); // Convert to integer

// Fetch journal details
$journal = getJournalDetails($journal_id);

if (!$journal) {
    die("Error: No journal found with the given ID.");
}

// Ensure variables are set before use
$journal_name = htmlspecialchars($journal['journal_name'] ?? 'Unknown Journal');
$journal_image = $journal['journal_image'] ?? '';
$image_path = !empty($journal_image) ? "/admin/" . htmlspecialchars($journal_image) : 'default-image.jpg';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $author_id = (int) $_POST['author_id'];
    $journal_id = (int) $_POST['journal_id'];

    // Check if already linked to avoid duplicates
    if (!isAuthorLinked($conn, $author_id, $journal_id)) {
        if (linkAuthorToJournal($conn, $author_id, $journal_id)) {
            $_SESSION['message'] = "Successfully linked your account to the journal.";
        } else {
            $_SESSION['error'] = "Failed to link account to journal.";
        }
    } else {
        $_SESSION['message'] = "Your account is already linked to this journal.";
    }
}

$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Assume you checked the login and it failed due to unregistered author
$_SESSION['register_message'] = "Register as author in this journal.";

// Clear the session error message
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Login - <?php echo htmlspecialchars($journal_name); ?></title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #002147;
            color: white;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
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

        .container {
            width: 60%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .journal-box {
            width: 100%;
            text-align: center;
        }

        .journal-image img {
            width: 150px;
            height: auto;
            border-radius: 5px;
            display: block;
            margin: 0 auto;
        }

        .book-container {
            width: 70%;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .journal-box h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
            width: 100%;
            display: block;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-buttons {
            display: flex;
            justify-content: center;
        }

        button {
            background-color: #002147;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .instructions {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            font-size: 14px;
            text-align: left;
            width: 80%;
        }

        .instructions p {
            margin: 10px 0;
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

        .footer-column h3, .footer-column h4 {
            margin-bottom: 15px;
            color: #ffffff;
        }

        .footer-column p, .footer-column a, .footer-column li {
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

        .register-message {
            background-color: #ffeb3b;
            color: #333;
            padding: 10px 20px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
<header>
    <div><?php echo htmlspecialchars($journal_name); ?></div>
    <div style="position: relative; display: inline-block;">
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
        </div>
        <button class="dropbtn">Register ▼</button>
        <div class="dropdown-content" style="display: none; position: absolute; background-color: white; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0;">
                    <a href="article_register.php?journal_id=<?php echo $journal_id; ?>&role=Author" style="color:black; padding: 12px 16px; text-decoration:none; display:block;">Author Register</a>
                    <a href="article_register.php?journal_id=<?php echo $journal_id; ?>&role=Editor" style="color:black; padding: 12px 16px; text-decoration:none; display:block;">Editor Register</a>
                    <a href="article_register.php?journal_id=<?php echo $journal_id; ?>&role=Reviewer" style="color:black; padding: 12px 16px; text-decoration:none; display:block;">Reviewer Register</a>
                </div>
            </div>
        </div>
    </div>
</header>

<div style="margin: 20px 0 0 20px;">
    <button onclick="history.back()" style="
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        color: black;
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
    ">
        ← Back
    </button>
</div>

<script>
    const dropbtn = document.querySelector('.dropbtn');
    const dropdownContent = document.querySelector('.dropdown-content');

    dropbtn.addEventListener('click', function() {
        const isVisible = dropdownContent.style.display === 'block';
        dropdownContent.style.display = isVisible ? 'none' : 'block';
    });

    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
            if (dropdownContent.style.display === 'block') {
                dropdownContent.style.display = 'none';
            }
        }
    }
</script>

<div class="container">
    <div class="journal-box">
        <h2>Welcome to Editorial Manager ® for <?php echo htmlspecialchars($journal_name); ?></h2>
        <div class="journal-image">
            <img src="<?php echo $image_path; ?>" alt="Journal Cover">
        </div>
        <form action="process_submit.php" method="POST">
            <input type="hidden" name="journal_id" value="<?php echo $journal_id; ?>">
            <?php if (isset($_SESSION['error_message'])): ?>
                <div style="color: red; margin-bottom: 10px;"><?php echo $_SESSION['error_message']; ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['register_message'])): ?>
                <div class="register-message">
                    <?php echo $_SESSION['register_message']; ?>
                </div>
                <script>
                    setTimeout(function() {
                        const msg = document.querySelector('.register-message');
                        if (msg) msg.style.display = 'none';
                    }, 3000);
                </script>
                <?php unset($_SESSION['register_message']); ?>
            <?php endif; ?>

            <label>Username:</label>
            <input type="text" name="username" placeholder="Email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <div class="login-buttons">
                <button type="submit" name="role" value="author">Author Login</button>
            </div>
        </form>
    </div>
    <h3>NOTE: The email and password you used to register with on this platform, can also be used to log in as an author after completing your author registration, and to submit your paper to this journal.</h3>
      <p style="margin-top: 10px; color: #555;">
        Already have an account in any other journals? Click <strong>Continue</strong> to log in.
    </p>

    <div class="login-buttons">
        <form action="check_journal_registration.php" method="post">
            <input type="hidden" name="journal_id" value="<?php echo $journal_id; ?>">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <button type="submit">Continue</button>
        </form>
    </div>
</div>

<div class="book-container">
    <div class="footer-links">
        <a href="author-guidelines.php?journal_id=<?php echo $journal_id; ?>">Instructions for Authors | </a>
        <a href="../../for_reviewer.php">Instructions for Reviewers | </a>
        <a href="aims_scope.php?journal_id=<?php echo $journal_id; ?>">About the Journal</a>
    </div>
    <div class="instructions">
        <p><strong>First-time users:</strong> Click "Register" in the navigation bar to enter your details. Upon successful registration, redirect to login page use your registered email to login to your dashboard.</p>
        <p><strong>Repeat users:</strong> Click the "Login" button and proceed as appropriate.</p>
        <p><strong>Authors:</strong> Click "Register" as "Author" to submit and track manuscripts.</p>
        <p><strong>Reviewers:</strong> Click "Register" as "Reviewer" in the navigation bar to enter your details. Upon successful registration, redirect login page. Please wait up to get approved by admin (You will get notified by email).</p>
        <p><strong>Editors:</strong> Click "Register" as "Editors" in the navigation bar to enter your details. Upon successful registration, redirect login page. Please wait up to get approved by admin (You will get notified by email).</p>
        <p><strong>To change credentials:</strong> After registration, log in and click "Update My Information."</p>
    </div>
</div>

<?php if (!empty($error_message)): ?>
    <div style="color: red; font-weight: bold; margin: 10px 0;">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

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
            &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
        </p>
    </div>
</footer>

<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>
