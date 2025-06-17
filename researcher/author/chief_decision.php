<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['chief_editor_id'];

// Use functions from db_connect.php
$accepted_count = getAcceptedCount($conn);
$rejected_count = getRejectedCount($conn);
$pending_papers = getPendingPapers($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Decisions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
    .btn-back {
        display: inline-block;
        margin: 20px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #4b6cb7, #182848);
        color: white;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, #182848, #4b6cb7);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
</style>
<body>
<a href="chief-dashboard.php" class="btn-back">← Back to Chief Dashboard</a>
<div class="container mt-4">
    <h2>📜 Review Decisions</h2>
    <!-- <a href="chief-dashboard.php" class="btn btn-secondary mt-4">🔙 Back</a> -->
    <br>

    <br>
    <!-- Summary -->
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3">
                <h5>✅ Final Accepted Papers</h5>
                <p><strong><?php echo $accepted_count; ?></strong> Papers</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h5>❌ Rejected (Post-Review) Papers</h5>
                <p><strong><?php echo $rejected_count; ?></strong> Papers</p>
            </div>
        </div>
    </div>
    <h3 class="mt-4">🔍 Pending Review Decisions</h3>

    <?php while ($row = $pending_papers->fetch_assoc()) {
        $paper_id = $row['paper_id'];
        $title = $row['title'];
        $author_name = $row['author_name'];

        $editorTasks = getEditorTasksByPaperId($conn, $paper_id);
        $reviewFeedback = getReviewerFeedbackByPaperId($conn, $paper_id);
    ?>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($title); ?></h5>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($author_name); ?></p>

            <h6>Editor Tasks:</h6>
            <ul>
                <?php while ($task = $editorTasks->fetch_assoc()) { ?>
                    <li>
                        <strong>Task <?php echo $task['task_type']; ?></strong> by <em><?php echo $task['editor_name']; ?></em><br>
                        <?php echo nl2br(htmlspecialchars($task['editor_feedback'])); ?>
                    </li>
                <?php } ?>
            </ul>

            <h6>Reviewer Feedback:</h6>
            <ul>
                <?php while ($fb = $reviewFeedback->fetch_assoc()) { ?>
                    <li>
                        <strong><?php echo $fb['reviewer_name']; ?></strong> (<?php echo $fb['review_date']; ?>):<br>
                        <?php echo nl2br(htmlspecialchars($fb['feedback'])); ?>
                    </li>
                <?php } ?>
            </ul>

            <div class="d-flex gap-2 mt-3">
                <form action="submit_review_editor.php" method="POST">
                    <input type="hidden" name="paper_id" value="<?php echo $paper_id; ?>">
                    <input type="hidden" name="title" value="<?php echo htmlspecialchars($title); ?>">
                    <input type="hidden" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>">
                    <input type="hidden" name="decision" value="Accepted (Final Decision)">
                    <button type="submit" class="btn btn-sm btn-success">✅ Accept</button>
                </form>

                <form action="submit_review_editor.php" method="POST">
                    <input type="hidden" name="paper_id" value="<?php echo $paper_id; ?>">
                    <input type="hidden" name="title" value="<?php echo htmlspecialchars($title); ?>">
                    <input type="hidden" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>">
                    <input type="hidden" name="decision" value="Rejected (Post-Review)">
                    <button type="submit" class="btn btn-sm btn-danger">❌ Reject</button>
                </form>
            </div>
        </div>
    </div>

    <?php } ?>
</div>
</body>
</html>
