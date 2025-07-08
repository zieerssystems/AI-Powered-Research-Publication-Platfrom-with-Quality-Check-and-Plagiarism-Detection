<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

$editor_id = $_SESSION['editor_id'];
$task_type = 1;

$result = getAcceptedPapersForReview($conn, $editor_id, $task_type);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Initial Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="bg-light">
<a href="initial_review_history.php" class="btn-back">← Back to History</a>
<div class="container mt-5">
    <h3 class="mb-4">Accepted Papers for Initial Review</h3>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
            <th>Paper Title</th>
            <th>Journal</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>File</th>
            <th>Feedback & Result</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $today = new DateTime();
        while ($row = $result->fetch_assoc()):
            $deadline = new DateTime($row['deadline']);
            $interval = $today->diff($deadline);
            $daysLeft = (int)$interval->format('%r%a');

            $color = 'table-success';
            if ($daysLeft < 0) $color = 'table-danger';
            elseif ($daysLeft <= 2) $color = 'table-warning';

            $filePath = "../../uploads/" . basename($row['file_path']);
        ?>
            <tr class="<?= $color ?>">
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['journal_name']) ?></td>
                <td><?= htmlspecialchars($row['deadline']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <?php if (!empty($row['file_path'])): ?>
                        <a class="btn btn-sm btn-info" target="_blank" href="<?= $filePath ?>">Open File</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" class="row g-2">
                        <div class="col-12">
                            <textarea name="feedback" class="form-control" rows="2" placeholder="Enter your feedback"><?= htmlspecialchars($row['feedback']) ?></textarea>
                        </div>
                        <div class="col-md-8">
                            <select name="result" class="form-select">
                                <option <?= $row['result'] === 'Not Processed' ? 'selected' : '' ?>>Not Processed</option>
                                <option <?= $row['result'] === 'Processed for Next Level' ? 'selected' : '' ?>>Processed for Next Level</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-end">
                            <input type="hidden" name="paper_id" value="<?= $row['paper_id'] ?>">
                            <input type="hidden" name="editor_id" value="<?= $editor_id ?>">
                            <input type="hidden" name="task_type" value="<?= $task_type ?>">
                            <button type="submit" name="action" value="update_both" class="btn btn-success">Update Both</button>
                        </div>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_both') {
    $paper_id = $_POST['paper_id'];
    $editor_id = $_POST['editor_id'];
    $task_type = $_POST['task_type'];
    $feedback = $_POST['feedback'];
    $result = $_POST['result'];

    updateEditorTask($conn, $feedback, $result, $paper_id, $editor_id, $task_type);

    header("Location: editor_dashboard.php");
    exit();
}
$conn->close();
?>
</body>
</html>
