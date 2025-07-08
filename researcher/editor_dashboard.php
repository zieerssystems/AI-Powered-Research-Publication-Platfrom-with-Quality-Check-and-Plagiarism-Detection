<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Check if editor is logged in
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

if (isset($_GET['seen_notify']) && $_GET['seen_notify'] == 1) {
    $_SESSION['review_notification_seen'] = true;
}

$editor_id = $_SESSION['editor_id'];

$editor = getEditorDetails_2($conn, $editor_id);

// Define task labels
$task_labels = [
    1 => ['label' => 'Initial Review', 'file' => 'initial_review.php'],
    2 => ['label' => 'Plagiarism Check', 'file' => 'plagiarism_check.php'],
    3 => ['label' => 'Assign Reviewer', 'file' => 'assign_review.php'],
    4 => ['label' => 'Format Check', 'file' => 'formate_accp_rej.php']
];

// Get assigned tasks
$assigned_tasks = getAssignedTasks($conn, $editor_id);

// Define an array of assigned task types for checking later
$assigned_task_types = array_keys($assigned_tasks);

// Fetch counts and manuscripts
$pending_reviews = getPendingReviews_2($conn, $editor_id);
$new_manuscripts_count = getNewManuscriptsCount($conn, $editor_id);
$manuscripts = fetchManuscripts($conn, $editor_id);
$co_authors_list = fetchCoAuthorsList($conn);

// Check if review notification should be shown
$showBadge = shouldShowReviewNotification($conn, $editor_id);

// Initialize an array to store pending counts for each task type
$pending_counts = [];

// Loop through each task type to get pending counts
foreach ($task_labels as $task_id => $task) {
    $pending_counts[$task_id] = getPendingCount_2($conn, $editor_id, $task_id);
}

// Get the first paper and reviewer ID
$review_details = getFirstPaperAndReviewerId($conn, $editor_id);
$first_paper_id = $review_details['paper_id'];
$first_reviewer_id = $review_details['reviewer_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #002147;
            color: white;
            position: fixed;
            overflow-y: auto;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #3949ab;
        }

        .content {
            margin-left: 250px;
            height: 100vh;
            overflow-y: auto;
            padding: 20px;
            background-color: #f4f7f9;
        }

        .card {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">Editor Panel</h4>
    <a href="../../publish.php">🏡 Home</a>
    <a href="editor_dashboard.php">🏛️ Dashboard</a>

    <?php if (array_intersect([3], $assigned_task_types)) : ?>
        <a href="editor_manuscript.php">📄 Manuscripts
        <?php if ($new_manuscripts_count > 0): ?>
            <span class="badge bg-danger"><?php echo $new_manuscripts_count; ?></span>
        <?php endif; ?>
        </a>
    <?php endif; ?>

    <?php if (in_array(3, $assigned_task_types)) : ?>
        <a href="reviewers.php">👨‍🏫 Reviewers</a>
    <?php endif; ?>

    <?php if (array_intersect([2, 3, 4], $assigned_task_types)) : ?>
        <a href="revised_submitted_papers.php">📄 Resubmitted Manuscripts
        <?php if ($new_manuscripts_count > 0): ?>
            <span class="badge bg-danger"><?php echo $new_manuscripts_count; ?></span>
        <?php endif; ?>
        </a>
    <?php endif; ?>

    <a href="change_login_details.php">✍ Change login details</a>

    <?php if (in_array(3, $assigned_task_types)) : ?>
        <a href="decisions.php">✅ Decisions</a>
        <a href="review_feedback.php">📜 Review Feedback</a>
    <?php endif; ?>

    <?php if (in_array(1, $assigned_task_types)) : ?>
        <a href="initial_review_history.php">
            👤 Initial Review History
            <?php if ($pending_counts[1] > 0): ?>
                <span style="background:red; color:white; border-radius:50%; padding: 2px 8px; font-size: 12px; margin-left: 5px;">
                    <?= $pending_counts[1] ?>
                </span>
            <?php endif; ?>
        </a>
    <?php endif; ?>

    <?php if (in_array(3, $assigned_task_types)) : ?>
        <a href="assign_review_history.php">
            👤 Assign Review History
            <?php if ($pending_counts[3] > 0): ?>
                <span style="background:red; color:white; border-radius:50%; padding: 2px 8px; font-size: 12px; margin-left: 5px;">
                    <?= $pending_counts[3] ?>
                </span>
            <?php endif; ?>
        </a>
    <?php endif; ?>

    <a href="publication.php">📢 Publication</a>
    <a href="analytics.php">📈 Reports & Analytics</a>
    <a href="contact_chief.php">📧 Contact Chief Editor</a>
    <a href="my_account.php">👤 My Account</a>
    <a href="logout.php" class="text-danger">🚪 Logout</a>

    <?php if (empty($assigned_task_types)) : ?>
        <div class="text-light text-center mt-4">
            <small><em>No tasks currently assigned</em></small>
        </div>
    <?php endif; ?>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Welcome <?php echo htmlspecialchars($editor['full_name']); ?></h2>
    <p>Email: <?php echo htmlspecialchars($editor['email']); ?></p>
    <p>Last Login: <?php echo htmlspecialchars($editor['last_login']); ?></p>

    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-md-4">
            <?php if (in_array(3, $assigned_task_types)) : ?>
                <a href="reviewer_detail.php?seen_notify=1<?php
                    if ($first_paper_id && $first_reviewer_id) {
                        echo "&paper_id={$first_paper_id}&reviewer_id={$first_reviewer_id}";
                    }
                ?>" class="btn btn-sm btn-primary position-relative">
                    📜 Manage Reviews
                    <?php if ($showBadge): ?>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                            <span class="visually-hidden">New updates</span>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <?php foreach ($task_labels as $task_id => $task): ?>
            <?php if (!empty($assigned_tasks[$task_id])) : ?>
                <div class="col-md-2 mb-3">
                    <a href="<?= $task['file'] ?>" class="btn btn-primary btn-block">
                        <?= htmlspecialchars($task['label']) ?>
                        <span class="badge bg-danger">Pending</span>
                    </a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
