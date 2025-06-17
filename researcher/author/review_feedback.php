<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Redirect if not logged in as editor
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

// Fetch feedback and paper details using the function from db_connect.php
$editor_id = $_SESSION['editor_id'];
$result = fetchFeedbackAndPaperDetails($conn, $editor_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviewer Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f6f9;
            padding: 30px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .status-badge {
            font-weight: bold;
        }
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
<a href="editor_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
<div class="container">
    <h3 class="mb-4">📝 Reviewer Feedback</h3>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Reviewer</th>
                        <th>Author</th>
                        <th>Journal</th>
                        <th>Title</th>
                        <th>Feedback</th>
                        <th>Status</th>
                        <th>Review Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['reviewer_first'] . ' ' . $row['reviewer_last']; ?></td>
                        <td><?= $row['author_first'] . ' ' . $row['author_last']; ?></td>
                        <td><?= htmlspecialchars($row['journal_name']); ?></td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= nl2br(htmlspecialchars($row['feedback'])); ?></td>
                        <td>
                            <span class="badge bg-<?php
                                echo match($row['status']) {
                                    'Accepted' => 'success',
                                    'Rejected' => 'danger',
                                    'Revision Requested' => 'warning',
                                    'Revised Submitted' => 'info',
                                    default => 'secondary'
                                };
                            ?>">
                                <?= $row['status']; ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($row['review_date'])); ?></td>
                        <td>
                            <a href="../author/manuscripts.php?paper_id=<?php echo $row['paper_id']; ?>" class="btn btn-sm btn-success mb-1">View Paper</a>
                            <a href="reassign_reviewer.php?paper_id=<?= $row['paper_id']; ?>" class="btn btn-sm btn-warning mb-1">
                                🔁 Reassign
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No feedback available.</div>
    <?php endif; ?>
</div>
</body>
</html>
