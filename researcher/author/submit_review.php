<?php
session_start();
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");
$reviewer_id = $_SESSION['reviewer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paper_id = $_POST['paper_id'] ?? null;
    $feedback = $_POST['feedback'] ?? '';
    $action = $_POST['action'] ?? '';
    $journal_name = $_POST['journal_name'] ?? '';

    if (!$paper_id || !$action) {
        die("Invalid Request: Missing Paper ID or Status");
    }

    // Fetch author_id
    $author_id = fetchAuthorId($conn, $paper_id);

    // Set statuses
    if ($action === "Accepted" || $action === "Rejected") {
        $paper_status = "Under Review";
        $assignment_status = "Completed";
    } elseif ($action === "Revision Requested") {
        $paper_status = "Revision Requested";
        $assignment_status = "Revision Requested";
    }

    // Update papers table
    updatePaper($conn, $paper_status, $paper_id);

    // Update paper_assignments table
    updatePaperAssignmentStatus($conn, $assignment_status, $paper_id, $reviewer_id);

    // Update completed_date only if accepted or rejected
    if ($action === "Accepted" || $action === "Rejected") {
        updateCompletedDate($conn, $paper_id, $reviewer_id);
    }

    // Insert or update feedback
    updateFeedback($conn, $paper_id, $reviewer_id, $feedback, $author_id, $journal_name);

    header("Location: reviewer_dashboard.php");
    exit();
}

// Handle GET request (show feedback form)
$paper_id = $_GET['paper_id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$paper_id || !$status) {
    die("Invalid access to review page.");
}

// Fetch paper title or other info to show
$paper = fetchPaper($conn, $paper_id);
$title = $paper['title'];
$journal_id = $paper['journal_id'];

// Get journal name
$journal_name = fetchJournalName($conn, $journal_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Review</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <a href="view_manuscript.php" class="btn btn-secondary mb-3">⬅ Back</a>
    <h2>Submit Review for: <?= htmlspecialchars($title) ?> (<?= htmlspecialchars($status) ?>)</h2>

    <form method="POST">
        <input type="hidden" name="paper_id" value="<?= $paper_id ?>">
        <input type="hidden" name="action" value="<?= $status ?>">
        <input type="hidden" name="journal_name" value="<?= htmlspecialchars($journal_name) ?>">

        <div class="mb-3">
            <label for="feedback" class="form-label">Your Feedback:</label>
            <textarea name="feedback" id="feedback" class="form-control" rows="7" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
</div>
</body>
</html>
