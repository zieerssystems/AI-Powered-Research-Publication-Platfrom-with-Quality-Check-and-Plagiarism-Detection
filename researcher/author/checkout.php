<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_POST['selected_papers'])) {
    die("No papers selected.");
}

$paper_ids = $_POST['selected_papers'];
$paperDetails = [];
$totalAmount = 0;
$validPaperIds = []; // Only unpaid papers will go here
$author_id = $_SESSION['user_id'];

foreach ($paper_ids as $paper_id) {
    // Check if already paid
    if (!isPaperPaid($conn, $paper_id, $author_id)) {
        // Not yet paid
        $paper = getPaperById1($conn, $paper_id);
        if ($paper) {
            $paperDetails[] = $paper;
            $totalAmount += $paper['reader_fee_amount'];
            $validPaperIds[] = $paper_id;
        }
    }
}

// Save for use in Razorpay
$_SESSION['checkout_papers'] = $validPaperIds;
$_SESSION['total_amount'] = $totalAmount;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<style>
    /* Basic page styling */
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9; /* Light Gray Background */
            margin: 0;
            padding: 0;
        }

        h2, h3 {
            color: #333;
            text-align: center;
        }
        table {
            margin: auto;
            border-collapse: collapse;
            width: 80%;
            background-color: #fff;
            box-shadow: 0 0 10px #ccc;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        button {
            margin: 20px auto;
            display: block;
            background-color: #3399cc;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
        }
        button:hover {
            background-color: #287aa9;
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
<body>
    <!-- Header -->
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
        color:black;
    ">
        ← Back
    </button>
</div>
<h2>Checkout</h2>

<table>
    <tr>
        <th>Title</th>
        <th>Volume</th>
        <th>Issue</th>
        <th>Reader Fee (INR)</th>
    </tr>
    <?php foreach ($paperDetails as $paper): ?>
        <tr>
            <td><?= htmlspecialchars($paper['title']) ?></td>
            <td><?= htmlspecialchars($paper['volume']) ?></td>
            <td><?= htmlspecialchars($paper['issue']) ?></td>
            <td><?= number_format($paper['reader_fee_amount'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Total: ₹<?= number_format($totalAmount, 2) ?></h3>

<form id="paymentForm">
    <button type="button" onclick="payNow()">Pay with Razorpay</button>
    <?php if (isset($_POST['journal_id'])): ?>
    <button type="button" onclick="window.location.href='journal_detail.php?journal_id=<?= htmlspecialchars($_POST['journal_id']) ?>'">Cancel</button>
<?php else: ?>
    <button type="button" onclick="window.location.href='journal-listing.php'">Cancel</button>
<?php endif; ?>
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function payNow() {
    const options = {
        "key": "rzp_test_nKyYkRw2gRb1zO", // Replace with your actual Razorpay Key
        "amount": "<?= $totalAmount * 100 ?>", // Amount in paise
        "currency": "INR",
        "name": "Zieers",
        "description": "Paper Access Fee",
        "handler": function (response) {
            // Send to backend
            fetch("payment_done.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id || "",
                    amount: <?= $totalAmount ?>,
                    paper_ids: <?= json_encode($validPaperIds) ?>
                })
            }).then(res => res.text()).then(data => {
                const paperIds = <?= json_encode($validPaperIds) ?>;
const queryString = "?paper_ids=" + encodeURIComponent(paperIds.join(','));
window.location.href = "paper.php" + queryString;
 // Redirect after success
            });
        },
        "prefill": {
            "name": "<?= $_SESSION['first_name'] ?? 'User' ?>",
            "email": "<?= $_SESSION['email'] ?? 'test@example.com' ?>"
        },
        "theme": {
            "color": "#3399cc"
        }
    };
    const rzp = new Razorpay(options);
    rzp.open();
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
