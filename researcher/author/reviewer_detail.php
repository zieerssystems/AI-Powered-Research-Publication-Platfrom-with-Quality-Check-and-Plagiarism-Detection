<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['editor_id'];
$msg = '';

// Update result if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paper_id'])) {
    $paper_id = intval($_POST['paper_id']);
    $result = $_POST['result'];

    if (updateEditorTaskResult($conn, $result, $editor_id, $paper_id)) {
        header("Location: editor_dashboard.php");
        exit();
    } else {
        $msg = "❌ Error updating status.";
    }
}

// Fetch completed reviews using the function from db_connect.php
$reviews = fetchCompleted($conn, $editor_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
</head>
<body>
    <a href="editor_dashboard.php" class="btn-back">← Back to Editor Dashboard</a>
    <div class="container mt-4">
        <h2>📝 Completed Reviews - Editor Panel</h2>

        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Paper Title</th>
                    <th>Journal Name</th>
                    <th>Deadline</th>
                    <th>Completed Date</th>
                    <th>Feedback</th>
                    <th>Status</th>
                    <th>Review Result</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $reviews->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['journal_name']) ?></td>
                    <td><?= htmlspecialchars($row['deadline'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['completed_date'] ?? '') ?></td>
                    <td><?= nl2br(htmlspecialchars($row['feedback'] ?? '')) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['assignment_status'] ?? '')) ?></td>
                    <td>
                        <?php if ($row['assignment_status'] === 'Completed'): ?>
                            <form method="POST">
                                <input type="hidden" name="paper_id" value="<?= $row['paper_id'] ?>">
                                <select name="result" class="form-select" required>
                                    <option value="">-- Select Result --</option>
                                    <option value="Processed for Next Level">Processed for Next Level</option>
                                    <option value="Not Processed">Not Processed</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">Update</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">Not Available</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
