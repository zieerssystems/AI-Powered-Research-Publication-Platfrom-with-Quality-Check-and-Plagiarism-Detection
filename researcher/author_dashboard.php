<?php
session_start();

if (!isset($_SESSION['author_id'])) {
    header("Location: author_dash_login.php");
    exit();
}
include(__DIR__ . "/../../include/db_connect.php");

$author_id = $_SESSION['author_id'];

// Author profile
$profile = getAuthorProfile($conn, $author_id);
$first_name = $profile['first_name'];
$email = $profile['email'];

// Paper stats
$stats = getAuthorPaperStats($conn, $author_id);
$total_submissions = $stats['total'];
$under_review = $stats['under_review'];
$accepted = $stats['accepted'];
$rejected_pre = $stats['rejected_pre'];
$rejected_post = $stats['rejected_post'];

// DOI paper count
$accepted_papers_count = getAcceptedWithDoiCount($conn, $author_id);

// Papers needing payment
$papers_needing_payment = getPapersNeedingPayment($conn, $author_id);

if (!isset($_SESSION['notifications_shown']) || $_SESSION['notifications_shown'] === false) {

    // Payment Notification
    if (count($papers_needing_payment) > 0) {
        $_SESSION['notifications'][] = "You have papers awaiting payment. Check out the <a href='payment.php'>Payment Page</a>";
    }

    // Published Notification
    if ($accepted_papers_count > 0) {
        $_SESSION['notifications'][] = " Congratulations! Your paper successfully published papers. <a href='published_articles.php'>📖 View Published Articles</a>";
    }

    // Under Review Notification
    if ($under_review > 0) {
        $_SESSION['notifications'][] = "📄 Your paper is currently under review.";
    }

    $_SESSION['notifications_shown'] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Author Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background: #f4f4f9;
        }
        html, body {
    overflow-x: hidden;
}
.dashboard-header div {
    word-break: break-word;
    max-width: 100%;
}

        .sidebar {
    width: 250px;
    background: #002147;
    height: 100vh;
    padding: 20px;
    position: fixed;
    color: white;
    resize: horizontal; /* allows resize */
    overflow: auto; /* enables scroll if content overflows */
    min-width: 180px; /* minimum width limit */
    max-width: 400px; /* optional: max width limit */
    box-sizing: border-box;
}

        
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px 0;
            font-size: 14px;
        }
        .sidebar a:hover {
            background: #3949ab;
        }
        .logout {
            background: red;
            text-align: center;
            padding: 12px;
            border-radius: 5px;
            font-size: 14px;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
            flex-grow: 1;
            width: calc(100% - 270px);
        }
        .dashboard-header {
            background: white;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .welcome-text {
            font-size: 18px;
            font-weight: bold;
        }
        .card-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .card {
            background: white;
            padding: 20px;
            width: calc(33.33% - 20px);
            min-width: 280px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }
        .card h3 {
            margin-bottom: 10px;
        }
        .btn {
            background: #002147;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
            font-size: 14px;
        }
        .btn:hover {
            background: #0056b3;
        }
        @media (max-width: 768px) {
            .card {
                width: 100%;
            }
            .content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
            .sidebar {
                display: none;
            }
        }
        .notification {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .notification a {
            color: #fff;
            text-decoration: none;
            font-weight: normal;
            text-decoration: underline;
        }
        .notification a:hover {
            color: #ddd;
        }
        footer {
            background: #002147;
            color: white;
            text-align: center;
            padding: 20px 10px;
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

    </style>
</head>
<div>

    <!-- Left Sidebar -->
    <div class="sidebar">
        <h2>📚 Author Panel</h2>
        <a href="../../publish.php">🏡 Home</a> 
        <a href="author_dashboard.php">🏛️ Dashboard</a>
        <a href="submit_manuscript.php">📄 Submited Manuscript</a>
        <a href="track_submission.php">📊 Track Submission</a>
        <a href="revision_management.php">🔄 Manage Revisions</a>
        <a href="editor_communication.php">📧 Messages</a>
        <a href="payment.php">💳 Payments</a>
        <a href="published_articles.php">📖 Published Articles</a>
        <a href="update_author_profile.php">⚙ My Account</a>
        <a href="author_logout.php" class="logout">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="welcome-text">Welcome, <?= htmlspecialchars($first_name) ?></div>
            <div>📧 <?= htmlspecialchars($email) ?></div>
        </div>

        <!-- Display Notifications if Any -->
        <?php if (isset($_SESSION['notifications']) && count($_SESSION['notifications']) > 0): ?>
            <?php foreach ($_SESSION['notifications'] as $notification): ?>
                <div class="notification"><?= $notification ?></div>
            <?php endforeach; ?>
            <!-- Clear notifications after displaying -->
            <?php unset($_SESSION['notifications']); ?>
        <?php endif; ?>

        <!-- Dashboard Cards -->
        <div class="card-container">
            
            <!-- Dashboard Overview -->
            <div class="card">
                <h3>📊 Dashboard Overview</h3>
                <p><strong>Total Submissions:</strong> <?= $total_submissions ?></p>
                <!-- <p><strong>Under Review:</strong> <?= $under_review ?></p>
                <p><strong>Accepted:</strong> <?= $accepted ?></p>
                <p><strong>Rejected (Pre-Review):</strong> <?= $rejected_pre ?></p>
                <p><strong>Rejected (Post-Review):</strong> <?= $rejected_post ?></p> -->
                <a href="submission_details.php" class="btn">View Details</a>
            </div>

            <!-- Submit Manuscript -->
            <div class="card">
                <h3>📄 Submit New Manuscript</h3>
                <p>Upload your research and track progress.</p>
                <a href="journal-listing.php" class="btn">Submit Now</a>
            </div>

            <!-- Track Submission -->
            <div class="card">
                <h3>📂 Track Your Submission</h3>
                <p>Monitor the status of your research papers.</p>
                <a href="track_submission.php" class="btn">Track Now</a>
            </div>

            <!-- Payments -->
            <div class="card">
                <h3>💳 Payments & Invoices</h3>
                <p>Check and manage your publication fees.</p>
                <a href="payment.php" class="btn">View Payments</a>
            </div>

            <!-- Published Articles -->
            <div class="card">
                <h3>📖 Published Articles</h3>
                <p>citations</p>
                <a href="published_articles.php" class="btn">View Articles</a>
            </div>
</div>
</div>
</body>
</html>
