<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

if (isset($_SESSION['error'])) {
    echo "<div style='color: red; font-weight: bold;'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo "<div style='color: green; font-weight: bold;'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']);
}

$reviewer_id = intval($_GET['reviewer_id'] ?? 0);
$paper_id = intval($_GET['paper_id'] ?? 0);

// Fetch reviewer details using the function from db_connect.php
$reviewer = fetchReviewerDetails($conn, $reviewer_id);

if (!$reviewer) {
    die("Reviewer not found.");
}

// Get the count of reviewed papers for the current reviewer
$reviewed_count = fetchReviewedCount($conn, $reviewer_id);

// Fetch paper title and journal name
$paper_title = "";
$journal_name = "";
if ($paper_id > 0) {
    $paper_data = fetchPaperDetails($conn, $paper_id);
    if ($paper_data) {
        $paper_title = $paper_data['title'];
        $journal_name = $paper_data['journal_name'];
    } else {
        die("Paper not found.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Paper</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>📌 Assign Paper to <?php echo htmlspecialchars($reviewer['first_name'] . ' ' . $reviewer['last_name']); ?></h2>

    <a href="reviewers.php" class="btn btn-secondary mb-3">⬅ Back to Reviewers List</a>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Reviewer Details</h5>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($reviewer['first_name'] . ' ' . $reviewer['last_name']); ?></p>
            <p><strong>Reviewed Papers:</strong> <?php echo $reviewed_count; ?></p>
        </div>
    </div>

    <form method="POST" action="process_assignment.php">
        <input type="hidden" name="reviewer_id" value="<?php echo $reviewer_id; ?>">
        <input type="hidden" name="paper_id" value="<?php echo $paper_id; ?>">

        <p><strong>Paper Title:</strong> <?php echo htmlspecialchars($paper_title); ?></p>
        <p><strong>Journal:</strong> <?php echo htmlspecialchars($journal_name); ?></p>

        <label><strong>Set Deadline:</strong></label>
        <input type="date" name="deadline" class="form-control" required>

        <button type="submit" class="btn btn-success mt-3">✅ Assign Paper</button>
    </form>
</div>
</body>
</html>
