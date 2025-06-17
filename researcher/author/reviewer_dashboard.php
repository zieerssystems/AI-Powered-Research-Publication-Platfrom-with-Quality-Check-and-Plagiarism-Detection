<?php
session_start();
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$reviewer_id = $_SESSION['reviewer_id'];

// Fetch reviewer details using the function from db_connect.php
$reviewer = fetchReviewer_dash($conn, $reviewer_id);
$first_name = $reviewer['first_name'];
$last_name = $reviewer['last_name'];
$email = $reviewer['email'];
$last_login = $reviewer['last_login'];

// Count revised submissions using the function from db_connect.php
$revised_submitted_count = countRevisedSubmissions($conn, $reviewer_id);

// Initialize counters
$pending_reviews = $completed_reviews = $overdue_reviews = 0;
$today = date("Y-m-d");

// Fetch review stats using the function from db_connect.php
$stats = fetchReviewStats($conn, $reviewer_id);
$pending_reviews = $stats['pending'];
$completed_reviews = $stats['completed'];

// Count overdue reviews using the function from db_connect.php
$overdue_reviews = countOverdueReviews($conn, $reviewer_id, $today);

// Fetch pending assignments using the function from db_connect.php
$pending_assignments = fetchPendingAssignments($conn, $reviewer_id);

// Fetch accepted papers for review submission using the function from db_connect.php
$review_submissions = fetchAcceptedPapersForReview($conn, $reviewer_id);

// Fetch deadlines for color-coded notifications using the function from db_connect.php
$deadlines_result = fetchDeadlines($conn, $reviewer_id);

$red_count = $dark_yellow_count = $yellow_count = $green_count = 0;
$one_day_before = date("Y-m-d", strtotime("+1 day"));
$two_days_before = date("Y-m-d", strtotime("+2 days"));

while ($row = $deadlines_result->fetch_assoc()) {
    $deadline_date = $row['deadline'];

    if ($deadline_date < $today) {
        $red_count++;
    } elseif ($deadline_date == $one_day_before) {
        $dark_yellow_count++;
    } elseif ($deadline_date == $two_days_before) {
        $yellow_count++;
    } elseif ($deadline_date > $two_days_before) {
        $green_count++;
    }
}

// Determine which badge to show
$deadline_badge = "";
if ($red_count > 0) {
    $deadline_badge = "<span class='badge bg-danger'>$red_count</span>";
} elseif ($dark_yellow_count > 0) {
    $deadline_badge = "<span class='badge bg-warning text-dark'>$dark_yellow_count</span>";
} elseif ($yellow_count > 0) {
    $deadline_badge = "<span class='badge bg-warning'>$yellow_count</span>";
} elseif ($green_count > 0) {
    $deadline_badge = "<span class='badge bg-success'>$green_count</span>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reviewer Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f9; }
        .sidebar { width: 250px; height: 100vh; background: #002147; color: white; position: fixed; padding-top: 20px; overflow-y: auto; }
        .sidebar a { display: block; padding: 15px; color: white; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover { background: #3949ab; }
        .content { margin-left: 250px; padding: 20px; }
        .card { box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); border-radius: 8px; padding: 15px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">Reviewer Panel</h4>
    <a href="../../publish.php">🏡 Home</a>
    <a href="reviewer_dashboard.php">🏛️ Dashboard</a>
    <a href="pending_reviews.php">📄 Pending Reviews
        <?php if ($pending_reviews > 0): ?>
            <span class="badge bg-danger"><?= $pending_reviews ?></span>
        <?php endif; ?>
    </a>
    <a href="view_manuscripts.php">📂 View Manuscripts</a>
    <a href="change_login_details_review.php">✍ Change login details</a>
    <a href="resubmitted.php">📄 Revision Requested
        <?php if ($revised_submitted_count > 0): ?>
            <span class="badge bg-danger"><?= $revised_submitted_count ?></span>
        <?php endif; ?>
    </a>
    <a href="deadlines.php">⏳ Deadlines & Reminders <?= $deadline_badge ?></a>
    <a href="review_history.php">📜 Review History</a>
    <a href="contact_editor.php">📧 Contact Editor</a>
    <a href="update_profile.php">⚙ My Account/new journal request</a>
    <a href="reviewer_logout.php" class="text-danger">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Welcome <?= htmlspecialchars($first_name . ' ' . $last_name) ?></h2>
    <p>Email: <?= htmlspecialchars($email) ?></p>
    <p>Last Login: <?= $last_login ? $last_login : 'Never logged in' ?></p>

    <!-- Dashboard Overview -->
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <h5>📄 Pending Reviews</h5>
                <p><strong><?= $pending_reviews ?></strong> Manuscripts</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <h5>✅ Completed Reviews</h5>
                <p><strong><?= $completed_reviews ?></strong> Manuscripts</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <h5>⚠️ Overdue Reviews</h5>
                <p><strong><?= $overdue_reviews ?></strong> Manuscripts</p>
            </div>
        </div>
    </div>

    <!-- Pending Assignments -->
    <h3 class="mt-4">Pending Assignments</h3>
    <?php while ($row = $pending_assignments->fetch_assoc()): ?>
        <div class="card">
            <h5>📑 Manuscript: <?= htmlspecialchars($row['title']) ?></h5>
            <p><strong>Deadline:</strong> <?= htmlspecialchars($row['deadline']) ?></p>
        </div>
    <?php endwhile; ?>

    <!-- Review Submission -->
    <h3 class="mt-4">Review Submission</h3>
    <?php if ($review_submissions->num_rows > 0): ?>
        <?php while ($row = $review_submissions->fetch_assoc()): ?>
            <div class="card">
                <h5>📑 Manuscript: <?= htmlspecialchars($row['title']) ?></h5>
                <p><strong>Status:</strong> In-Review</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No manuscripts currently under review.</p>
    <?php endif; ?>
</div>

</body>
</html>
