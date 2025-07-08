<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . "/../../include/db_connect.php");

// Fetch logged-in user details if email is in session
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?redirect=publish.php");
    exit();
}

$user = getLoggedInUserDetails($_SESSION['user_id']);

// Get role from URL and store in session
$role = $_GET['role'] ?? $_POST['role'] ?? '';
$_SESSION['role'] = $role;

$journal_id = 0;
$journal_name = "Registration";
$journal_image = "";

// If the role is Author, we need to check if a journal ID is provided
if ($role === "Author") {
    if (!isset($_GET['journal_id'])) {
        die("Error: journal_id is required for Authors.");
    }
    $journal_id = (int) $_GET['journal_id'];
    $journal = getJournalDetails($journal_id);
    $journal_name = $journal ? htmlspecialchars($journal['journal_name']) : "Unknown Journal";
    $journal_image = $journal ? htmlspecialchars($journal['journal_image']) : "";
} elseif ($role === "Reviewer" || $role === "Editor") {
    $journal_id = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['first_name']) || !isset($_POST['last_name']) || !isset($_POST['email'])) {
        die("Error: Required fields are missing.");
    }

    $_SESSION['journal_id'] = $journal_id;
    $_SESSION['first_name'] = $_POST['first_name'];
    $_SESSION['middle_name'] = $_POST['middle_name'] ?? '';
    $_SESSION['last_name'] = $_POST['last_name'];
    $_SESSION['email'] = $_POST['email'];

    $email = trim($_POST['email']);
    $user_id = $_SESSION['user_id'];

    // Role-based user_id check
    if ($role === "Reviewer") {
        if (isUserRegisteredAsReviewer($conn, $user_id)) {
            echo "<div class='container'><h3>Error: You are already registered as a Reviewer.</h3><p>Please log in or choose a different role.</p></div>";
            exit;
        }
    } elseif ($role === "Editor") {
        if (isUserRegisteredAsEditor($conn, $user_id)) {
            echo "<div class='container'><h3>Error: You are already registered as an Editor.</h3><p>Please log in or choose a different role.</p></div>";
            exit;
        }
    } elseif ($role === "Author") {
        if (isUserRegisteredAsAuthor($conn, $user_id)) {
            $stmt = $conn->prepare("SELECT id FROM author WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($author_id);
            $stmt->fetch();
            $stmt->close();

            if (isAuthorLinkedToJournal($conn, $author_id, $journal_id)) {
                echo "<div class='container'><h3>You are already registered for this journal.</h3></div>";
                exit;
            } else {
                echo "<div class='container'><h3>Already registered author, not linked to this journal.</h3>
                    <p>Do you want to link your account to this journal?</p>
                    <form action='submit-article.php?journal_id=$journal_id' method='POST'>
                        <input type='hidden' name='author_id' value='$author_id'>
                        <input type='hidden' name='journal_id' value='$journal_id'>
                        <button type='submit' class='btn'>Yes, link to this journal</button>
                    </form>
                    <form action='submit-article.php' method='GET' style='margin-top:10px;'>
                        <input type='hidden' name='journal_id' value='$journal_id'>
                        <button type='submit' class='btn' style='background-color:#ccc;'>Cancel</button>
                    </form>
                </div>";
                exit;
            }
        } else {
            header("Location: author_reg.php?journal_id=$journal_id&role=Author");
            exit;
        }
    }

    // Redirect to appropriate registration form
    if ($role === "Reviewer") {
        header("Location: reviewer_reg.php?role=Reviewer");
        exit;
    } elseif ($role === "Editor") {
        header("Location: editor_reg.php?role=Editor");
        exit;
    }
}

// Handle optional messages
if (isset($_GET['msg'])) {
    $messages = [
        'success' => 'OTP sent to your email.',
        'notfound' => 'Email not found.',
        'invalid' => 'Invalid OTP.',
        'passwordupdated' => 'Password successfully updated.',
        'error' => 'An error occurred. Please try again later.',
    ];
    if (array_key_exists($_GET['msg'], $messages)) {
        echo "<script>alert('" . $messages[$_GET['msg']] . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Registration</title>
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
        .sub-header {
            background-color: #e0e0e0;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .sub-header a {
            color: #333;
            text-decoration: none;
            margin-right: 15px;
        }

        .sub-header a:hover {
            text-decoration: underline;
        }

        .container {
            width: 50%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .container h2 {
            font-size: 22px;
            margin-bottom: 15px;
        }

        .btn {
            display: block;
            width: 80%;
            padding: 10px;
            margin: 10px auto;
            border-radius: 5px;
            cursor: pointer;
            border: 1px solid grey;
        }

        .btn:hover {
            background: #666;
            color: white;
        }

        .input-box {
            width: 90%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .instructions, .warning {
            text-align: left;
            font-size: 14px;
            margin-top: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-left: 4px solid #ff6b6b;
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

        .h2 {
            text-align: center;
        }
    </style>
</head>
<body>
<header>
    <div><?php echo $journal_name; ?></div>
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
        <?php if ($role === "Editor"): ?>
            <a href="editor_login.php">Login</a>
        <?php elseif ($role === "Reviewer"): ?>
            <a href="reviewer_login.php">Login</a>
        <?php else: ?>
            <a href="submit-article.php?journal_id=<?php echo $journal_id; ?>">Author Login</a>
        <?php endif; ?>
    </div>
</header>
<div class="sub-header">
    <?php if ($role === "Author"): ?>
        <div class="h2">
            <h3>Author Registration</h3>
        </div>
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
    <?php elseif ($role === "Reviewer"): ?>
        <div class="h2">
            <h3>Reviewer Registration</h3>
        </div>
        <div style="margin: 20px 0 0 20px;">
            <button onclick="history.back()" style="
                background-color: #f1f1f1;
                border: 1px solid #ccc;
                padding: 8px 16px;
                font-size: 14px;
                border-radius: 4px;
                cursor: pointer;">
                ← Back
            </button>
        </div>
    <?php else: ?>
        <div class="h2">
            <h3>Editor Registration</h3>
        </div>
        <div style="margin: 20px 0 0 20px;">
            <button onclick="history.back()" style="
                background-color: #f1f1f1;
                border: 1px solid #ccc;
                padding: 8px 16px;
                font-size: 14px;
                border-radius: 4px;
                cursor: pointer;">
                ← Back
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="container">
    <h2>Registration Method</h2>

    <form action="article_register.php?<?php echo http_build_query(['role' => $role] + ($journal_id ? ['journal_id' => $journal_id] : [])); ?>" method="POST">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
        <input type="hidden" name="journal_id" value="<?php echo $journal_id; ?>">
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
        <input type="text" class="input-box" name="first_name" placeholder="Given/First Name*" required value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" readonly>
        <input type="text" class="input-box" name="middle_name" placeholder="Middle Name" value="<?php echo htmlspecialchars($user['middle_name'] ?? ''); ?>" readonly>
        <input type="text" class="input-box" name="last_name" placeholder="Family/Last Name*" required value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" readonly>
        <input type="email" class="input-box" name="email" placeholder="E-mail Address*" required value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
        <button type="submit" class="btn">Continue</button>
    </form>
    <?php if ($role === "Author"): ?>
    <div class="warning">
        <p><strong>WARNING:</strong> If you already have an account as an Author, please DO NOT register again. This may cause issues with your submissions.</p>
        <p>If you need to update your details, use the 'UPDATE MY INFORMATION' option. For help, contact support@zieers.com.</p>
    </div>
    <?php elseif ($role === "Reviewer"): ?>
    <div class="warning">
        <p><strong>WARNING:</strong> If you already have an account as Reviewer , please DO NOT register again. This may cause issues with your submissions.</p>
        <p> you can update your details. For help, contact support@zieers.com.</p>
    </div>
     <?php else: ?>
    <div class="warning">
        <p><strong>WARNING:</strong> If you already have an account as an Editor, please DO NOT register again. This may cause issues with your submissions.</p>
        <p> you can update your details. For help, contact support@zieers.com.</p>
    </div>
    <?php endif; ?>
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
