<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

$editor_id = $_SESSION['editor_id'];
$task_type = 3;

// Fetch initial review tasks using the function from db_connect.php
$result = fetchInitialReviewTasks($conn, $editor_id, $task_type);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Initial Review Tasks</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #182848, #4b6cb7);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .paper-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #4b6cb7;
        }

        .action-buttons {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- Header -->
<h2>📄 Initial Review Tasks</h2>

<!-- Back and History Buttons -->
<div style="text-align: center; margin-bottom: 30px;">
    <a href="editor_dashboard.php" class="btn btn-primary" style="margin-right: 10px;">← Back to Dashboard</a>
    <a href="assign_review_history.php" class="btn btn-secondary">🕒 View Review History</a>
</div>

<!-- Papers List -->
<?php
if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        if (strtolower($row['status']) != 'accepted' && strtolower($row['status']) != 'rejected'):
?>
    <div class="paper-card">
        <h4>📌 Title: <?= htmlspecialchars($row['title']) ?></h4>
        <p>📚 Journal: <?= htmlspecialchars($row['journal_name']) ?></p>
        <p>Deadline: <?= htmlspecialchars($row['deadline']) ?></p>

        <div class="action-buttons">
            <form method="POST" action="assign_update_status.php" style="display:inline-block; margin-right:10px;">
                <input type="hidden" name="paper_id" value="<?= $row['paper_id'] ?>">
                <input type="hidden" name="editor_id" value="<?= $editor_id ?>">
                <input type="hidden" name="task_type" value="<?= $task_type ?>">
                <button type="submit" name="decision" value="accept" class="btn btn-success">✅ Accept</button>
            </form>

            <form method="POST" action="assign_update_status.php" style="display:inline-block;">
                <input type="hidden" name="paper_id" value="<?= $row['paper_id'] ?>">
                <input type="hidden" name="editor_id" value="<?= $editor_id ?>">
                <input type="hidden" name="task_type" value="<?= $task_type ?>">
                <button type="submit" name="decision" value="reject" class="btn btn-danger">❌ Reject</button>
            </form>
        </div>
    </div>
<?php
        endif;
    endwhile;
else:
    echo '<p style="text-align:center; color:#666;">No tasks available for review.</p>';
endif;

$conn->close();
?>

</body>
</html>
