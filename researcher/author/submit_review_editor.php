<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$success_message = "";
$error_message = "";

// Initialize default values
$paper_id = $_POST['paper_id'] ?? null;
$title = $_POST['title'] ?? '';
$author_name = $_POST['author_name'] ?? '';
$decision = $_POST['decision'] ?? '';
$comments = $_POST['comments'] ?? '';

// Handle form submission and update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comments'])) {
    if (!$paper_id || !$decision) {
        $error_message = "Missing required information.";
    } else {
        if (updatePaperStatusAndFeedback($conn, $decision, $comments, $paper_id)) {
            $success_message = "✅ Paper status updated successfully for '<strong>" . htmlspecialchars($title) . "</strong>' by <strong>" . htmlspecialchars($author_name) . "</strong>.";
        } else {
            $error_message = "❌ Error updating paper: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Editor Review</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>📜 Submit Editor Final Decision</h2>
    <a href="chief_decision.php" class="btn btn-secondary mt-4">🔙 Back</a>
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php elseif ($error_message): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="paper_id" value="<?= htmlspecialchars($paper_id) ?>">
        <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">
        <input type="hidden" name="author_name" value="<?= htmlspecialchars($author_name) ?>">
        <input type="hidden" name="decision" value="<?= htmlspecialchars($decision) ?>">

        <div class="mb-3">
            <label class="form-label">Title:</label>
            <p><strong><?= htmlspecialchars($title) ?></strong></p>
        </div>

        <div class="mb-3">
            <label class="form-label">Author Name:</label>
            <p><strong><?= htmlspecialchars($author_name) ?></strong></p>
        </div>

        <div class="mb-3">
            <label class="form-label">Final Decision:</label>
            <p><strong><?= htmlspecialchars($decision) ?></strong></p>
        </div>

        <div class="mb-3">
            <label for="comments" class="form-label">Editor Comments:</label>
            <textarea name="comments" id="comments" class="form-control" rows="5" required><?= htmlspecialchars($comments) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Final Decision</button>
    </form>
</div>
</body>
</html>
